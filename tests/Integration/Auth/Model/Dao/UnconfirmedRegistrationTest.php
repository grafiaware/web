<?php
declare(strict_types=1);

namespace Test\Integration\Auth\Model\Dao;

use Application\Api\Catalog\AuthRouteCatalog;
use Auth\Middleware\Login\Controler\UnconfirmedRegistrationControler;
use Auth\Model\Dao\CredentialsDao;
use Auth\Model\Dao\LoginDao;
use Auth\Model\Dao\LoginProfileDao;
use Auth\Model\Dao\LoginTokenDao;
use Auth\Model\Dao\RegistrationDao;
use Auth\Model\Entity\Credentials;
use Auth\Model\Entity\LoginAggregateFull;
use Auth\Service\LoginTokenService;
use Auth\Service\RoleGrantPolicy;
use Auth\Service\UnconfirmedRegistrationCleaner;
use Pes\Container\Container;
use Pes\Database\Handler\Handler;
use Pes\Model\RowData\RowData;
use Site\Common\ActiveSite;
use Site\ConfigurationCache;
use Status\Model\Entity\Flash;
use Status\Model\Entity\Presentation;
use Status\Model\Entity\Security;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusSecurityRepo;
use Test\AppRunner\AppRunner;
use Test\Support\AuthControllerContainerFactory;

/**
 * A3 (docs/vp/zadani/A3.md): sloupec confirmed (migrace auth 20261106000002), výběr a smazání nepotvrzených registrací,
 * ruční úklid: bez přepínače A9 jen supervisor, s ním global_role_grantor a auth_role_grantor (RoleGrantPolicy).
 */
final class UnconfirmedRegistrationTest extends AppRunner
{
    private const PREFIX = 'a3clean_test_';

    private Container $container;

    public static function setUpBeforeClass(): void
    {
        self::bootstrapBeforeClass();
        if (!defined('PES_RUNNING_ON_PRODUCTION_HOST')) {
            define('PES_RUNNING_ON_PRODUCTION_HOST', false);
        }
        ActiveSite::force('NajdiSi');
        ConfigurationCache::resetCache();
        self::cleanup();
    }

    public static function tearDownAfterClass(): void
    {
        self::cleanup();
        ActiveSite::reset();
        ConfigurationCache::resetCache();
    }

    protected function setUp(): void
    {
        [$this->container] = AuthControllerContainerFactory::createWithMailSpy();
    }

    /**
     * Login + registrace s datem vzniku před $daysAgo dny; volitelně credentials, firma, potvrzení.
     */
    private function account(string $suffix, int $daysAgo, bool $credentials = false, ?string $company = null, bool $confirmed = false): string
    {
        $login = self::PREFIX . $suffix . substr(uniqid(), -5);
        $row = new RowData();
        $row->offsetSet('login_name', $login);
        $this->container->get(LoginDao::class)->insert($row);
        $reg = new RowData();
        $reg->import(['login_name_fk' => $login, 'password_hash' => '', 'email' => $login . '@example.test', 'info' => $company]);
        $this->container->get(RegistrationDao::class)->insert($reg);
        $created = (new \DateTimeImmutable("-{$daysAgo} days"))->format('Y-m-d H:i:s');
        $stmt = $this->container->get(Handler::class)->prepare(
            "UPDATE registration SET created = :c" . ($confirmed ? ", confirmed = :c2" : "") . " WHERE login_name_fk = :l");
        $stmt->bindValue(':c', $created);
        if ($confirmed) {
            $stmt->bindValue(':c2', $created);
        }
        $stmt->bindValue(':l', $login);
        $stmt->execute();
        if ($credentials) {
            $cred = new RowData();
            $cred->import(['login_name_fk' => $login, 'password_hash' => '', 'role_fk' => 'representative']);
            $this->container->get(CredentialsDao::class)->insert($cred);
        }
        return $login;
    }

    private function ours(array $rows): array
    {
        return array_values(array_filter(array_map(fn($r) => is_array($r) ? $r['login_name_fk'] : $r, $rows),
            fn($l) => str_starts_with($l, self::PREFIX)));
    }

    public function testFindAndDeleteOnlyOldUnconfirmedWithoutCredentials(): void
    {
        $oldVisitor = $this->account('oldvis', 20);
        $oldRepre = $this->account('oldrep', 30, false, 'Firma a.s.');
        $young = $this->account('young', 5);
        $confirmed = $this->account('conf', 40, true, null, true);
        $manual = $this->account('manual', 40, true, 'Ručně dokončená');   // má credentials, confirmed NULL
        $this->container->get(LoginTokenService::class)->issue($oldVisitor, LoginTokenService::PURPOSE_LOGIN, 30);
        $this->container->get(LoginProfileDao::class)->setDisplayName($oldVisitor, 'Smazat Se Mnou');

        /** @var RegistrationDao $dao */
        $dao = $this->container->get(RegistrationDao::class);
        $all = $this->ours($dao->findUnconfirmed(null, self::PREFIX));
        sort($all);
        $expectedAll = [$oldVisitor, $oldRepre, $young];
        sort($expectedAll);
        $this->assertSame($expectedAll, $all);

        $old = $this->ours($dao->findUnconfirmed(14, self::PREFIX));
        sort($old);
        $expectedOld = [$oldVisitor, $oldRepre];
        sort($expectedOld);
        $this->assertSame($expectedOld, $old);

        // jen testovací loginy – úklid bez prefixu by smazal i nepotvrzené registrace vývojové DB
        $deletedLogins = $dao->deleteUnconfirmedOlderThan(14, self::PREFIX);
        sort($deletedLogins);
        $this->assertSame($expectedOld, $deletedLogins);
        $this->assertSame([$young], $this->ours($dao->findUnconfirmed(null, self::PREFIX)), 'zůstane jen mladá nepotvrzená');
        foreach ([$oldVisitor, $oldRepre] as $login) {
            $this->assertNull($this->container->get(LoginDao::class)->get(['login_name' => $login]));
            $this->assertNull($dao->get(['login_name_fk' => $login]));
        }
        $this->assertNull($this->container->get(LoginProfileDao::class)->getDisplayName($oldVisitor), 'osobní jméno smazáno kaskádou');
        $this->assertSame([], $this->container->get(LoginTokenDao::class)->find('login_name_fk = :l', ['l' => $oldVisitor]));
        foreach ([$young, $confirmed, $manual] as $login) {
            $this->assertNotNull($this->container->get(LoginDao::class)->get(['login_name' => $login]), "$login zůstává");
        }
    }

    public function testMarkConfirmedIsIdempotent(): void
    {
        $login = $this->account('mark', 1);
        /** @var RegistrationDao $dao */
        $dao = $this->container->get(RegistrationDao::class);
        $dao->markConfirmed($login);
        $first = $this->confirmedOf($login);
        $this->assertNotNull($first);
        sleep(1);
        $dao->markConfirmed($login);
        $this->assertSame($first, $this->confirmedOf($login));
        $this->assertNotContains($login, $this->ours($dao->findUnconfirmed(null, self::PREFIX)));
    }

    private function confirmedOf(string $login): ?string
    {
        $stmt = $this->container->get(Handler::class)->prepare("SELECT confirmed FROM registration WHERE login_name_fk = :l");
        $stmt->bindValue(':l', $login);
        $stmt->execute();
        $v = $stmt->fetchColumn();
        return $v === false ? null : $v;
    }

    public function testOnlySupervisorMayClean(): void
    {
        foreach (['' => 401, 'events_administrator' => 303, 'editor' => 303, 'auth_role_grantor' => 303] as $role => $status) {
            [$controler, $calls] = $this->controler($role, false);
            $this->assertSame($status, $controler->clean($this->createPostRequest([]))->getStatusCode());
            $this->assertSame(0, $calls->count, 'role ' . ($role ?: 'nepřihlášený') . ' nesmaže nic');
        }
        [$controler, $calls] = $this->controler('supervisor', false);
        $this->assertSame(303, $controler->clean($this->createPostRequest([]))->getStatusCode());
        $this->assertSame(1, $calls->count);
        $this->assertSame(14, $calls->days);
    }

    /** S přepínačem vp.sprava-roli (A9) spouští úklid jen global_role_grantor a auth_role_grantor. */
    public function testA9GrantorsMayClean(): void
    {
        foreach (['supervisor', 'events_administrator', 'events_role_grantor', 'red_role_grantor'] as $role) {
            [$controler, $calls] = $this->controler($role, true);
            $this->assertSame(303, $controler->clean($this->createPostRequest([]))->getStatusCode());
            $this->assertSame(0, $calls->count, "role $role nesmaže nic");
        }
        foreach (['auth_role_grantor', 'global_role_grantor'] as $role) {
            [$controler, $calls] = $this->controler($role, true);
            $this->assertSame(303, $controler->clean($this->createPostRequest([]))->getStatusCode());
            $this->assertSame(1, $calls->count, "role $role smaže");
        }
    }

    /**
     * Kontrolér s úklidem nad podstrčeným DAO (skutečný úklid by smazal data vývojové DB).
     */
    private function controler(string $role, bool $grantors): array
    {
        $security = new Security();
        if ($role !== '') {
            $c = new Credentials();
            $c->setLoginNameFk('admin');
            $c->setRoleFk($role);
            $a = new LoginAggregateFull();
            $a->setLoginName('admin');
            $a->setCredentials($c);
            $security->newContext($a);
        }
        $calls = new \stdClass();
        $calls->count = 0;
        $calls->days = null;
        $dao = $this->createStub(RegistrationDao::class);
        $dao->method('deleteUnconfirmedOlderThan')->willReturnCallback(function (int $days) use ($calls) {
            $calls->count++;
            $calls->days = $days;
            return ['x', 'y'];
        });
        $sec = $this->createStub(StatusSecurityRepo::class);
        $sec->method('get')->willReturn($security);
        $sec->method('getClone')->willReturn($security);
        $flash = $this->createStub(StatusFlashRepo::class);
        $flash->method('get')->willReturn(new Flash());
        $pres = $this->createStub(StatusPresentationRepo::class);
        $pres->method('getClone')->willReturn(new Presentation());
        $controler = (new UnconfirmedRegistrationControler($sec, $flash, $pres, new UnconfirmedRegistrationCleaner($dao), new RoleGrantPolicy($grantors)))
            ->injectContainer($this->container);
        foreach (AuthRouteCatalog::definitions() as $d) {
            if ($d->urlPattern === '/auth/v1/admin/unconfirmed/clean') {
                $controler->bindRouteResponse($d);
            }
        }
        return [$controler, $calls];
    }

    private static function cleanup(): void
    {
        [$c] = AuthControllerContainerFactory::createWithMailSpy();
        $like = ['l' => self::PREFIX . '%'];
        foreach ([LoginTokenDao::class, LoginProfileDao::class, CredentialsDao::class, RegistrationDao::class] as $daoClass) {
            $dao = $c->get($daoClass);
            foreach ($dao->find('login_name_fk LIKE :l', $like) as $row) {
                $dao->delete($row);
            }
        }
        $loginDao = $c->get(LoginDao::class);
        foreach ($loginDao->find('login_name LIKE :l', $like) as $row) {
            $loginDao->delete($row);
        }
    }
}
