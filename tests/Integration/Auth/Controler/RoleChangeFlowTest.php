<?php
declare(strict_types=1);

namespace Test\Integration\Auth\Controler;

use Application\Api\Catalog\AuthRouteCatalog;
use Auth\Middleware\Login\Controler\AuthControler;
use Auth\Model\Dao\CredentialsDao;
use Auth\Model\Dao\LoginDao;
use Auth\Model\Entity\Credentials;
use Auth\Model\Entity\LoginAggregateFull;
use Auth\Model\Repository\CredentialsRepo;
use Auth\Model\Repository\RoleRepo;
use Auth\Service\RoleChangeLog;
use Auth\Service\RoleGrantPolicy;
use Pes\Container\Container;
use Pes\Model\RowData\RowData;
use Psr\Log\AbstractLogger;
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
 * A9 (docs/vp/zadani/A9.md): změna role v tabulce účtů – kontrola na serveru (oprava: dříve šlo nastavit libovolnou roli),
 * nikdo nemění vlastní roli, log změn rolí.
 */
final class RoleChangeFlowTest extends AppRunner
{
    private const PREFIX = 'a9role_test_';

    private Container $container;
    private Security $security;
    private Flash $flash;
    /** @var string[] */
    private array $logged = [];

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
        $this->security = new Security();
        $this->flash = new Flash();
        $this->logged = [];
    }

    private function account(string $suffix, ?string $role): string
    {
        $login = self::PREFIX . $suffix . substr(uniqid(), -5);
        $row = new RowData();
        $row->offsetSet('login_name', $login);
        $this->container->get(LoginDao::class)->insert($row);
        $cred = new RowData();
        $cred->import(['login_name_fk' => $login, 'password_hash' => '', 'role_fk' => $role]);
        $this->container->get(CredentialsDao::class)->insert($cred);
        return $login;
    }

    private function loginAs(string $login, string $role): void
    {
        $c = new Credentials();
        $c->setLoginNameFk($login);
        $c->setRoleFk($role);
        $a = new LoginAggregateFull();
        $a->setLoginName($login);
        $a->setCredentials($c);
        $this->security->newContext($a);
    }

    private function controler(bool $grantors): AuthControler
    {
        $sec = $this->createStub(StatusSecurityRepo::class);
        $sec->method('get')->willReturn($this->security);
        $sec->method('getClone')->willReturn($this->security);
        $flash = $this->createStub(StatusFlashRepo::class);
        $flash->method('get')->willReturn($this->flash);
        $pres = $this->createStub(StatusPresentationRepo::class);
        $pres->method('getClone')->willReturn(new Presentation());
        $test = $this;
        $logger = new class($test) extends AbstractLogger {
            public function __construct(private $test) {}
            public function log($level, \Stringable|string $message, array $context = []): void { $this->test->logLine((string) $message); }
        };
        $controler = (new AuthControler($sec, $flash, $pres, $this->container->get(RoleRepo::class), $this->container->get(CredentialsRepo::class)))
            ->injectContainer($this->container)
            ->setRoleGrantPolicy(new RoleGrantPolicy($grantors))
            ->setRoleChangeLog(new RoleChangeLog($logger));
        foreach (AuthRouteCatalog::definitions() as $d) {
            if ($d->urlPattern === '/auth/v1/credentials/:loginnamefk') {
                $controler->bindRouteResponse($d);
            }
        }
        return $controler;
    }

    public function logLine(string $line): void
    {
        $this->logged[] = $line;
    }

    private function change(bool $grantors, string $target, ?string $newRole): void
    {
        $response = $this->controler($grantors)->updateCredentials(
            $this->createPostRequest(['selectRole' => $newRole ?? AuthControler::NULL_VALUE]), $target);
        $this->assertSame(303, $response->getStatusCode());
        $this->container->get(CredentialsRepo::class)->flush();
    }

    private function roleOf(string $login): ?string
    {
        [$c] = AuthControllerContainerFactory::createWithMailSpy();   // čtení mimo identity map repozitáře
        return $c->get(CredentialsDao::class)->get(['login_name_fk' => $login])['role_fk'] ?? null;
    }

    private function messages(): string
    {
        $this->flash->storeMessages();
        return implode(' ', array_column($this->flash->getMessages(), 'message'));
    }

    /** Oprava bezpečnostní chyby – platí i s vypnutým přepínačem vp.sprava-roli. */
    public function testEventsAdministratorCannotGrantSupervisorWithoutFlag(): void
    {
        $admin = $this->account('eadmin', 'events_administrator');
        $user = $this->account('visitor', 'visitor');
        $this->loginAs($admin, 'events_administrator');

        $this->change(false, $user, 'supervisor');
        $this->assertSame('visitor', $this->roleOf($user));
        $this->assertStringContainsString('nemůžete přidělit', $this->messages());

        $this->change(false, $admin, 'supervisor');
        $this->assertSame('events_administrator', $this->roleOf($admin), 'vlastní roli nezmění');

        $this->change(false, $user, 'representative');
        $this->assertSame('representative', $this->roleOf($user));
        $this->assertCount(1, $this->logged);
        $this->assertStringContainsString("$admin → účet $user: visitor → representative", $this->logged[0]);
    }

    public function testGrantorsWithFlag(): void
    {
        $first = $this->account('first', 'auth_role_grantor');
        $second = $this->account('second', null);
        $editor = $this->account('editor', 'editor');
        $eventsGrantor = $this->account('egrant', 'events_role_grantor');

        // auth_role_grantor jmenuje druhého, ten odebere roli prvnímu (varianta B)
        $this->loginAs($first, 'auth_role_grantor');
        $this->change(true, $second, 'auth_role_grantor');
        $this->assertSame('auth_role_grantor', $this->roleOf($second));
        $this->change(true, $first, null);
        $this->assertSame('auth_role_grantor', $this->roleOf($first), 'sám sobě roli neodebere');

        $this->security = new Security();
        $this->loginAs($second, 'auth_role_grantor');
        $this->change(true, $first, null);
        $this->assertNull($this->roleOf($first));

        // events_role_grantor neupraví editora, nepřidělí editor
        $this->security = new Security();
        $this->loginAs($eventsGrantor, 'events_role_grantor');
        $this->change(true, $editor, 'visitor');
        $this->assertSame('editor', $this->roleOf($editor));
        $this->change(true, $first, 'editor');
        $this->assertNull($this->roleOf($first));
        $this->change(true, $first, 'visitor');
        $this->assertSame('visitor', $this->roleOf($first));

        // events_administrator s přepínačem účty nespravuje vůbec
        $this->security = new Security();
        $this->flash = new Flash();
        $this->loginAs($eventsGrantor, 'events_administrator');
        $this->change(true, $first, 'representative');
        $this->assertSame('visitor', $this->roleOf($first));
        $this->assertStringContainsString('Nemáte oprávnění', $this->messages());
    }

    private static function cleanup(): void
    {
        [$c] = AuthControllerContainerFactory::createWithMailSpy();
        $like = ['l' => self::PREFIX . '%'];
        $cred = $c->get(CredentialsDao::class);
        foreach ($cred->find('login_name_fk LIKE :l', $like) as $row) {
            $cred->delete($row);
        }
        $login = $c->get(LoginDao::class);
        foreach ($login->find('login_name LIKE :l', $like) as $row) {
            $login->delete($row);
        }
    }
}
