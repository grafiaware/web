<?php
declare(strict_types=1);

namespace Test\Integration\Auth\Controler;

use Application\Api\Catalog\AuthRouteCatalog;
use Application\Feature\FeatureFlags;
use Auth\Authenticator\DbAuthenticator;
use Auth\Authenticator\DbHashAuthenticator;
use Auth\Middleware\Login\Controler\AccountControler;
use Auth\Middleware\Login\Controler\LoginLogoutControler;
use Auth\Model\Dao\CredentialsDao;
use Auth\Model\Dao\LoginDao;
use Auth\Model\Dao\LoginProfileDao;
use Auth\Model\Dao\LoginTokenDao;
use Auth\Model\Dao\RegistrationDao;
use Auth\Model\Dao\RoleDao;
use Auth\Model\Entity\Credentials;
use Auth\Model\Entity\LoginAggregateFull;
use Auth\Model\Repository\LoginAggregateFullRepo;
use Auth\Service\AccountChangeLog;
use Auth\Service\AccountService;
use Auth\Service\DisplayNameService;
use Auth\Service\EventsLoginWriter;
use Auth\Service\GlobalRoleGrantor;
use Auth\Service\LoginLog;
use Auth\Service\LoginNameGenerator;
use Auth\Service\LoginThrottle;
use Auth\Service\LoginTokenService;
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
use Test\Integration\Mail\Support\MailSpy;
use Test\Support\AuthControllerContainerFactory;

/**
 * A9 (docs/vp/zadani/A9.md) s přepínačem vp.sprava-roli: účet typu admin, úpravy údajů účtu, zpomalené přihlašování,
 * global_role_grantor z konfigurace, logy přihlášení a změn.
 * Potřebuje migrace auth 20261110000001 a 20261110000002.
 */
final class AccountA9FlowTest extends AppRunner
{
    private const PREFIX = 'a9acc_test_';
    private const PASSWORD = 'heslo-a9-1234';

    private Container $container;
    private MailSpy $mailSpy;
    private Security $security;
    private Flash $flash;
    /** @var array<string, string[]> soubor => řádky */
    public array $logs = [];
    private \DateTimeImmutable $now;

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
        [$base, $this->mailSpy] = AuthControllerContainerFactory::createWithMailSpy();
        $this->security = new Security();
        $this->flash = new Flash();
        $this->logs = [];
        $this->now = new \DateTimeImmutable('2026-11-10 12:00:00');
        $this->container = $this->childContainer($base, null);
    }

    /** Podřízený kontejner: politika A9, logy do paměti, hodiny testu, volitelně global_role_grantor. */
    private function childContainer(Container $base, ?array $grantorConfig): Container
    {
        $c = new Container($base);
        $test = $this;
        $logger = fn(string $file) => new class($test, $file) extends AbstractLogger {
            public function __construct(private $test, private string $file) {}
            public function log($level, \Stringable|string $message, array $context = []): void { $this->test->logs[$this->file][] = (string) $message; }
        };
        $c->setOverride(RoleChangeLog::class, fn() => new RoleChangeLog($logger('RoleChanges.log')));
        $c->setOverride(AccountChangeLog::class, fn() => new AccountChangeLog($logger('AccountChanges.log')));
        $c->setOverride(LoginLog::class, fn() => new LoginLog($logger('LoginSelf.log'), $logger('LoginAdmin.log')));
        $c->setOverride(LoginThrottle::class, fn(Container $c) => new LoginThrottle($base->get(LoginProfileDao::class), 5, 30, 900, fn() => $test->now()));
        $c->setOverride(GlobalRoleGrantor::class, fn() => new GlobalRoleGrantor($grantorConfig, true));
        $c->setOverride(AccountService::class, fn() => new AccountService($base->get(LoginDao::class), $base->get(CredentialsDao::class),
            $base->get(RegistrationDao::class), $base->get(RoleDao::class), $base->get(LoginNameGenerator::class),
            $base->get(DisplayNameService::class), $base->get(LoginTokenService::class), new EventsLoginWriter(),
            '.{8,}', 'Heslo musí mít nejméně 8 znaků.', new RoleGrantPolicy(true), $base->get(LoginProfileDao::class),
            new AccountChangeLog($logger('AccountChanges.log')), new RoleChangeLog($logger('RoleChanges.log'))));
        return $c;
    }

    public function now(): \DateTimeImmutable
    {
        return $this->now;
    }

    private function features(): FeatureFlags
    {
        return new FeatureFlags(['vp.zakladani-uctu' => true, 'vp.osobni-jmeno' => true, 'vp.registrace-emailem' => true,
            'vp.prihlaseni-odkazem' => true, 'vp.sprava-roli' => true]);
    }

    private function loginAs(string $login, string $role): void
    {
        $c = new Credentials();
        $c->setLoginNameFk($login);
        $c->setRoleFk($role);
        $a = new LoginAggregateFull();
        $a->setLoginName($login);
        $a->setCredentials($c);
        $this->security = new Security();
        $this->security->newContext($a);
    }

    private function repos(?Security $security = null): array
    {
        $security ??= $this->security;
        $sec = $this->createStub(StatusSecurityRepo::class);
        $sec->method('get')->willReturn($security);
        $sec->method('getClone')->willReturn($security);
        $flash = $this->createStub(StatusFlashRepo::class);
        $flash->method('get')->willReturn($this->flash);
        $pres = $this->createStub(StatusPresentationRepo::class);
        $pres->method('getClone')->willReturn(new Presentation());
        return [$sec, $flash, $pres];
    }

    private function bound(object $controler, string $method, string $pattern): object
    {
        foreach (AuthRouteCatalog::definitions() as $d) {
            if ($d->httpMethod === $method && $d->urlPattern === $pattern) {
                $controler->bindRouteResponse($d);
                return $controler;
            }
        }
        $this->fail("routa $method $pattern neexistuje");
    }

    private function accountControler(string $pattern): AccountControler
    {
        [$sec, $flash, $pres] = $this->repos();
        return $this->bound((new AccountControler($sec, $flash, $pres, $this->container->get(AccountService::class)))
            ->setFeatureFlags($this->features())->injectContainer($this->container), 'POST', $pattern);
    }

    private function loginControler(Security $security): LoginLogoutControler
    {
        [$sec, $flash, $pres] = $this->repos($security);
        return $this->bound((new LoginLogoutControler($sec, $flash, $pres, $this->container->get(LoginAggregateFullRepo::class),
            new DbHashAuthenticator(), new DbAuthenticator()))
            ->setFeatureFlags($this->features())->injectContainer($this->container), 'POST', '/auth/v1/login');
    }

    private function passwordLogin(string $name, string $password, ?Security $security = null): Security
    {
        $security ??= new Security();
        $auth = ConfigurationCache::auth();
        $this->loginControler($security)->login($this->createPostRequest(['login' => 1,
            $auth['fieldNameJmeno'] => $name, $auth['fieldNameHeslo'] => $password]));
        return $security;
    }

    private function messages(): string
    {
        $this->flash->storeMessages();
        $text = implode(' ', array_column($this->flash->getMessages(), 'message'));
        $this->flash = new Flash();
        return $text;
    }

    /** Účet přímo v DB: login, credentials (hash hesla), volitelně registrace s e-mailem, origin. */
    private function account(string $suffix, ?string $role, ?string $email, ?string $origin, string $password = self::PASSWORD): string
    {
        $login = self::PREFIX . $suffix . substr(uniqid(), -5);
        $row = new RowData();
        $row->offsetSet('login_name', $login);
        $this->container->get(LoginDao::class)->insert($row);
        if ($email !== null) {
            $reg = new RowData();
            $reg->import(['login_name_fk' => $login, 'password_hash' => '', 'email' => $email]);
            $this->container->get(RegistrationDao::class)->insert($reg);
        }
        $cred = new RowData();
        $cred->import(['login_name_fk' => $login, 'password_hash' => password_hash($password, PASSWORD_DEFAULT), 'role_fk' => $role]);
        $this->container->get(CredentialsDao::class)->insert($cred);
        if ($origin !== null) {
            $this->container->get(LoginProfileDao::class)->setOrigin($login, $origin);
        }
        return $login;
    }

    private function emailOf(string $login): ?string
    {
        $reg = $this->container->get(RegistrationDao::class)->get(['login_name_fk' => $login]);
        return $reg === null ? null : (string) $reg['email'];
    }

    private function roleOf(string $login): ?string
    {
        return $this->container->get(CredentialsDao::class)->get(['login_name_fk' => $login])['role_fk'] ?? null;
    }

    private function allLogs(): string
    {
        return implode("\n", array_merge(...array_values($this->logs ?: [[]])));
    }

    // ------------------------------------------------------------------------------------------

    public function testCreateAdminAccountRequiresLoginNameSendsLinkSetsOrigin(): void
    {
        $this->loginAs('spravce_uctu', 'auth_role_grantor');
        $email = 'a9.new.' . uniqid() . '@example.test';

        $response = $this->accountControler('/auth/v1/account')->create($this->createPostRequest(['email' => $email, 'role' => '']));
        $this->assertSame(303, $response->getStatusCode());
        $this->assertStringContainsString('Zadejte přihlašovací jméno', $this->messages());

        $login = self::PREFIX . 'new' . substr(uniqid(), -5);
        $response = $this->accountControler('/auth/v1/account')->create($this->createPostRequest([
            'email' => $email, 'loginName' => $login, 'role' => '', 'displayName' => 'Nový Správce',
            'password' => 'ignorovane-heslo', 'passwordByMail' => '']));
        $this->assertSame(303, $response->getStatusCode());
        $this->assertStringContainsString('bez role', $this->messages());

        $cred = $this->container->get(CredentialsDao::class)->get(['login_name_fk' => $login]);
        $this->assertNull($cred['role_fk']);
        $this->assertSame('', $cred['password_hash'], 'heslo nezadává správce');
        $this->assertSame('admin', $this->container->get(LoginProfileDao::class)->getOrigin($login));
        $this->assertCount(1, $this->mailSpy->sent, 'vždy odkaz na nastavení hesla');
        $this->assertStringContainsString("spravce_uctu → účet $login: založení účtu", $this->allLogs());
        $this->assertStringNotContainsString($email, $this->allLogs(), 'v logu není e-mail');
    }

    public function testEventsGrantorCannotCreateEditor(): void
    {
        $this->loginAs('ev_grantor', 'events_role_grantor');
        $login = self::PREFIX . 'ed' . substr(uniqid(), -5);
        $this->accountControler('/auth/v1/account')->create($this->createPostRequest([
            'email' => 'a9.ed.' . uniqid() . '@example.test', 'loginName' => $login, 'role' => 'editor']));
        $this->assertStringContainsString('nemůžete založit', $this->messages());
        $this->assertNull($this->container->get(LoginDao::class)->get(['login_name' => $login]));
    }

    public function testUpdateEmailDisplayNameAndRoleByType(): void
    {
        $admin = $this->account('adm', 'visitor', 'stary.' . uniqid() . '@example.test', 'admin');
        $oldEmail = $this->emailOf($admin);
        $self = $this->account('self', 'visitor', 'self.' . uniqid() . '@example.test', 'self');
        $legacy = $this->account('leg', null, null, 'legacy');
        $newEmail = 'novy.' . uniqid() . '@example.test';

        // auth_role_grantor: e-mail u admin účtu, oznámení na původní adresu
        $this->loginAs('spravce_uctu', 'auth_role_grantor');
        $this->accountControler('/auth/v1/account/:loginname/update')->update($this->createPostRequest([
            'email' => $newEmail, 'displayName' => 'Jana Nová', 'changeRole' => '1', 'selectRole' => 'representative']), $admin);
        $this->assertStringContainsString('změněno e-mail, osobní jméno, role', $this->messages());
        $this->assertSame($newEmail, $this->emailOf($admin));
        $this->assertSame('representative', $this->roleOf($admin));
        $this->assertCount(1, $this->mailSpy->sent);
        $this->assertStringContainsString('změnil e-mailovou adresu', $this->mailSpy->sent[0]->getContent()->getHtml());
        $this->assertStringNotContainsString($newEmail, $this->mailSpy->sent[0]->getContent()->getHtml(), 'nová adresa se do oznámení nepíše');
        $this->assertStringContainsString("spravce_uctu → účet $admin: visitor → representative", $this->allLogs());
        $this->assertStringNotContainsString($newEmail, $this->allLogs());
        $this->assertStringNotContainsString((string) $oldEmail, $this->allLogs());

        // self: e-mail je identita, nemění se
        $this->accountControler('/auth/v1/account/:loginname/update')->update($this->createPostRequest(['email' => $newEmail]), $self);
        $this->assertStringContainsString('e-mail nemění', $this->messages());
        $this->assertNotSame($newEmail, $this->emailOf($self));

        // legacy: doplnění e-mailu (vznikne registrace)
        $this->accountControler('/auth/v1/account/:loginname/update')->update($this->createPostRequest(['email' => $newEmail]), $legacy);
        $this->assertSame($newEmail, $this->emailOf($legacy));
        $this->assertStringContainsString("účet $legacy: doplnění e-mailu", $this->allLogs());

        // events_role_grantor: e-mail ne, role jen visitor/representative
        $this->loginAs('ev_grantor', 'events_role_grantor');
        $this->accountControler('/auth/v1/account/:loginname/update')->update($this->createPostRequest(['email' => 'jiny@example.test']), $admin);
        $this->assertStringContainsString('E-mail účtů smí měnit jen', $this->messages());
        $this->accountControler('/auth/v1/account/:loginname/update')->update($this->createPostRequest(['changeRole' => '1', 'selectRole' => 'editor']), $admin);
        $this->assertSame('representative', $this->roleOf($admin));
        $this->accountControler('/auth/v1/account/:loginname/update')->update($this->createPostRequest(['changeRole' => '1', 'selectRole' => 'visitor']), $admin);
        $this->assertSame('visitor', $this->roleOf($admin));
    }

    public function testThrottleAcrossSessionsAndReset(): void
    {
        $login = $this->account('thr', 'visitor', 'thr.' . uniqid() . '@example.test', 'self');
        for ($i = 1; $i <= 5; $i++) {
            $this->assertFalse($this->passwordLogin($login, 'spatne-heslo')->hasValidSecurityContext());
        }
        $this->assertStringContainsString('Příliš mnoho neúspěšných pokusů', $this->messages());
        // správné heslo z jiné session je během blokování odmítnuto
        $this->assertFalse($this->passwordLogin($login, self::PASSWORD)->hasValidSecurityContext());
        $this->assertStringContainsString('Zkuste to znovu za 30 s', $this->messages());

        $this->now = $this->now->modify('+31 seconds');
        $ok = $this->passwordLogin($login, self::PASSWORD);
        $this->assertTrue($ok->hasValidSecurityContext());
        $profile = $this->container->get(LoginProfileDao::class);
        $this->assertSame(0, $profile->getThrottle($login)['failedCount']);
        $this->assertNotNull($profile->findProfiles()[$login]['lastLogin']);

        // pokusy během blokování se nepočítají; další chyba po vypršení blokování → prodleva roste (30 s, pak 60 s)
        $throttle = new LoginThrottle($profile, 5, 30, 900, fn() => $this->now);
        for ($i = 1; $i <= 6; $i++) {
            $this->passwordLogin($login, 'spatne-heslo');
        }
        $this->assertSame(5, $profile->getThrottle($login)['failedCount']);
        $this->assertSame(30, $throttle->secondsLocked($login));
        $this->now = $this->now->modify('+31 seconds');
        $this->passwordLogin($login, 'spatne-heslo');
        $this->assertSame(60, $throttle->secondsLocked($login));
        $this->messages();

        $self = implode("\n", $this->logs['LoginSelf.log'] ?? []);
        $this->assertStringContainsString("Přihlášení OK: $login (self, heslo)", $self);
        $this->assertStringContainsString("Přihlášení NEÚSPĚŠNÉ: $login (špatné heslo)", $self);
        $this->assertStringNotContainsString('spatne-heslo', $this->allLogs());
        $this->assertStringNotContainsString(self::PASSWORD, $this->allLogs());
    }

    public function testUnknownAccountLogsWithoutName(): void
    {
        $this->assertFalse($this->passwordLogin('mozna-heslo-v-poli-jmeno', 'x')->hasValidSecurityContext());
        $this->assertStringContainsString('Přihlášení NEÚSPĚŠNÉ: neznámý účet', implode("\n", $this->logs['LoginAdmin.log'] ?? []));
        $this->assertStringNotContainsString('mozna-heslo-v-poli-jmeno', $this->allLogs());
    }

    public function testGlobalRoleGrantorFromConfiguration(): void
    {
        [$base] = AuthControllerContainerFactory::createWithMailSpy();
        $config = ['enabled' => true, 'loginName' => 'global_role_grantor', 'passwordHash' => password_hash('root-heslo-12345', PASSWORD_DEFAULT)];
        $this->container = $this->childContainer($base, $config);

        $this->assertFalse($this->passwordLogin('global_role_grantor', 'spatne')->hasValidSecurityContext());
        $security = $this->passwordLogin('GLOBAL_ROLE_GRANTOR', 'root-heslo-12345');
        $this->assertTrue($security->hasValidSecurityContext(), $this->messages() . ' | ' . $this->allLogs());
        $this->assertSame('global_role_grantor', $security->getLoginAggregate()->getCredentials()->getRoleFk());
        $this->assertNull($this->container->get(LoginDao::class)->get(['login_name' => 'global_role_grantor']), 'účet není v DB');
        $admin = implode("\n", $this->logs['LoginAdmin.log'] ?? []);
        $this->assertStringContainsString('Přihlášení OK: global_role_grantor (konfigurace, heslo)', $admin);

        // vypnutý v konfiguraci = neexistuje
        $this->container = $this->childContainer($base, ['enabled' => false] + $config);
        $this->assertFalse($this->passwordLogin('global_role_grantor', 'root-heslo-12345')->hasValidSecurityContext());
        $this->assertFalse((new GlobalRoleGrantor(['enabled' => false] + $config, true))->isActive());
        $this->assertFalse((new GlobalRoleGrantor($config, false))->isActive(), 'bez přepínače vp.sprava-roli neexistuje');
        $this->assertFalse((new GlobalRoleGrantor(null, true))->isActive(), 'bez souboru neexistuje');
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
        // events.login zápis z A6 (EventsLoginWriter)
        try {
            $events = (new \Container\EventsModelContainerConfigurator())->configure((new \Container\EventsDbContainerConfigurator())->configure(new Container()))
                ->get(\Events\Model\Dao\LoginDao::class);
            foreach ($events->find('login_name LIKE :l', $like) as $row) {
                $events->delete($row);
            }
        } catch (\Throwable) {
        }
    }
}
