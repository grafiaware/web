<?php
declare(strict_types=1);

namespace Test\Integration\Auth\Controler;

use Application\Api\Catalog\AuthRouteCatalog;
use Application\Feature\FeatureFlags;
use Auth\Authenticator\DbAuthenticator;
use Auth\Authenticator\DbHashAuthenticator;
use Auth\Middleware\Login\Controler\AccountControler;
use Auth\Middleware\Login\Controler\LoginLogoutControler;
use Auth\Middleware\Login\Controler\SetPasswordControler;
use Auth\Model\Dao\CredentialsDao;
use Auth\Model\Dao\LoginDao;
use Auth\Model\Dao\LoginProfileDao;
use Auth\Model\Dao\LoginTokenDao;
use Auth\Model\Dao\RegistrationDao;
use Auth\Model\Dao\RoleDao;
use Auth\Model\Entity\Credentials;
use Auth\Model\Entity\LoginAggregateFull;
use Auth\Model\Repository\LoginAggregateFullRepo;
use Auth\Service\AccountException;
use Auth\Service\AccountRequest;
use Auth\Service\AccountService;
use Auth\Service\DisplayNameService;
use Auth\Service\EventsLoginWriter;
use Auth\Service\LoginNameGenerator;
use Auth\Service\LoginTokenService;
use Container\EventsDbContainerConfigurator;
use Container\EventsModelContainerConfigurator;
use Events\Model\Dao\LoginDao as EventsLoginDao;
use Pes\Container\Container;
use Psr\Http\Message\ServerRequestInterface;
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
 * A6 (docs/vp/zadani/A6.md): založení účtu administrátorem – auth tabulky, events.login, oprávnění,
 * „heslo mailem“ s jednorázovým odkazem, úklid po chybě zápisu.
 */
final class AccountFlowTest extends AppRunner
{
    private const PREFIX = 'a6acc_test_';

    private Container $container;
    private MailSpy $mailSpy;
    private Security $security;
    private Flash $flash;

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
        [$this->container, $this->mailSpy] = AuthControllerContainerFactory::createWithMailSpy();
        $this->security = new Security();
        $this->flash = new Flash();
    }

    private function loginAs(?string $role): void
    {
        if ($role === null) {
            return;
        }
        $credentials = new Credentials();
        $credentials->setLoginNameFk('admin_' . $role);
        $credentials->setRoleFk($role);
        $aggregate = new LoginAggregateFull();
        $aggregate->setLoginName('admin_' . $role);
        $aggregate->setCredentials($credentials);
        $this->security->newContext($aggregate);
    }

    private function features(): FeatureFlags
    {
        return new FeatureFlags(['vp.zakladani-uctu' => true, 'vp.osobni-jmeno' => true, 'vp.registrace-emailem' => true]);
    }

    private function securityRepo(): StatusSecurityRepo
    {
        $repo = $this->createStub(StatusSecurityRepo::class);
        $repo->method('get')->willReturn($this->security);
        $repo->method('getClone')->willReturn($this->security);
        return $repo;
    }

    private function flashRepo(): StatusFlashRepo
    {
        $repo = $this->createStub(StatusFlashRepo::class);
        $repo->method('get')->willReturn($this->flash);
        return $repo;
    }

    private function presentationRepo(): StatusPresentationRepo
    {
        $repo = $this->createStub(StatusPresentationRepo::class);
        $repo->method('getClone')->willReturn(new Presentation());
        return $repo;
    }

    private function messages(): array
    {
        $this->flash->storeMessages();
        return array_column($this->flash->getMessages(), 'message');
    }

    /**
     * Kontrolér navázaný na routu z AuthRouteCatalog – kontroluje i povolený druh odpovědi (responseMode).
     */
    private function bound(object $controler, string $method, string $pattern): object
    {
        foreach (AuthRouteCatalog::definitions() as $definition) {
            if ($definition->httpMethod === $method && $definition->urlPattern === $pattern) {
                $controler->bindRouteResponse($definition);
                return $controler;
            }
        }
        $this->fail("routa $method $pattern neexistuje");
    }

    private function accountControler(string $pattern = '/auth/v1/account'): AccountControler
    {
        return $this->bound((new AccountControler($this->securityRepo(), $this->flashRepo(), $this->presentationRepo(),
            $this->container->get(AccountService::class)))
            ->setFeatureFlags($this->features())->injectContainer($this->container), 'POST', $pattern);
    }

    private function createJson(array $body): \Psr\Http\Message\ResponseInterface
    {
        return $this->accountControler('/auth/v1/api/account')->createJson($this->createPostRequest($body));
    }

    private function setPasswordControler(string $method): SetPasswordControler
    {
        return $this->bound((new SetPasswordControler($this->securityRepo(), $this->flashRepo(), $this->presentationRepo(),
            $this->container->get(LoginTokenService::class), $this->container->get(LoginAggregateFullRepo::class)))
            ->setFeatureFlags($this->features())->injectContainer($this->container), $method, '/auth/v1/setpassword/:token');
    }

    private static function email(string $suffix = ''): string
    {
        return 'A6Acc.Test.' . uniqid() . $suffix . '@example.test';
    }

    private function eventsLoginDao(): EventsLoginDao
    {
        return (new EventsModelContainerConfigurator())->configure((new EventsDbContainerConfigurator())->configure(new Container()))
            ->get(EventsLoginDao::class);
    }

    public function testSupervisorCreatesEditorWithGeneratedLoginAndPassword(): void
    {
        $this->loginAs('supervisor');
        $email = self::email();
        $response = $this->createJson([
            'email' => $email, 'role' => 'editor', 'password' => 'heslo-1234', 'displayName' => 'Eva Editorová']);
        $this->assertSame(201, $response->getStatusCode());
        $json = json_decode((string) $response->getBody(), true);
        $login = $json['loginName'];
        $this->assertStringStartsWith(self::PREFIX, $login);

        $cred = $this->container->get(CredentialsDao::class)->get(['login_name_fk' => $login]);
        $this->assertSame('editor', $cred['role_fk']);
        $this->assertTrue(password_verify('heslo-1234', $cred['password_hash']));
        $reg = $this->container->get(RegistrationDao::class)->get(['login_name_fk' => $login]);
        $this->assertSame($email, $reg['email']);
        $this->assertSame('', $reg['password_hash']);
        $this->assertSame('Eva Editorová', $this->container->get(LoginProfileDao::class)->getDisplayName($login));
        $this->assertNotNull($this->eventsLoginDao()->get(['login_name' => $login]), 'účet je hned v events.login');
        $this->assertCount(0, $this->mailSpy->sent, 'účet se zadaným heslem nedostane žádný e-mail');

        // přihlášení jménem i e-mailem (e-mail má jen tento účet)
        foreach ([$login, $email] as $identifier) {
            $security = new Security();
            $repo = $this->createStub(StatusSecurityRepo::class);
            $repo->method('get')->willReturn($security);
            (new LoginLogoutControler($repo, $this->flashRepo(), $this->presentationRepo(),
                $this->container->get(LoginAggregateFullRepo::class), new DbHashAuthenticator(), new DbAuthenticator()))
                ->setFeatureFlags($this->features())->injectContainer($this->container)
                ->login($this->createPostRequest(['login' => 1,
                    ConfigurationCache::auth()['fieldNameJmeno'] => $identifier, ConfigurationCache::auth()['fieldNameHeslo'] => 'heslo-1234']));
            $this->assertTrue($security->hasValidSecurityContext(), "přihlášení: $identifier");
        }
    }

    public function testGivenLoginNameAndDuplicateInAnyCase(): void
    {
        $this->loginAs('supervisor');
        $login = self::PREFIX . 'repre' . substr(uniqid(), -6);
        $response = $this->createJson([
            'email' => self::email(), 'loginName' => $login, 'role' => 'representative', 'password' => 'heslo-1234']);
        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame($login, json_decode((string) $response->getBody(), true)['loginName']);

        $countBefore = count($this->container->get(LoginDao::class)->find('login_name LIKE :l', ['l' => self::PREFIX . '%']));
        $response = $this->createJson([
            'email' => self::email(), 'loginName' => strtoupper($login), 'role' => 'representative', 'password' => 'heslo-1234']);
        $this->assertSame(400, $response->getStatusCode());
        $this->assertStringContainsString('už existuje', json_decode((string) $response->getBody(), true)['error']);
        $this->assertCount($countBefore, $this->container->get(LoginDao::class)->find('login_name LIKE :l', ['l' => self::PREFIX . '%']));
    }

    public function testTwoAccountsWithSameEmail(): void
    {
        $this->loginAs('events_administrator');
        $email = self::email('same');
        $logins = [];
        foreach ([1, 2] as $_) {
            $response = $this->createJson([
                'email' => $email, 'role' => 'visitor', 'password' => 'heslo-1234']);
            $this->assertSame(201, $response->getStatusCode());
            $logins[] = json_decode((string) $response->getBody(), true)['loginName'];
        }
        $this->assertNotSame($logins[0], $logins[1]);
        $found = $this->container->get(RegistrationDao::class)->findLoginNamesByEmail($email, true);
        sort($found);
        sort($logins);
        $this->assertSame($logins, $found);
    }

    public function testPermissions(): void
    {
        $countLogins = fn() => count($this->container->get(LoginDao::class)->find('login_name LIKE :l', ['l' => self::PREFIX . '%']));
        $before = $countLogins();
        foreach ([null, 'visitor', 'representative', 'editor'] as $role) {
            $this->security = new Security();
            $this->loginAs($role);
            $response = $this->createJson([
                'email' => self::email(), 'role' => 'visitor', 'password' => 'heslo-1234']);
            $this->assertSame(403, $response->getStatusCode(), 'role ' . ($role ?? 'nepřihlášený'));
        }
        $this->security = new Security();
        $this->loginAs('events_administrator');
        foreach (['editor', 'supervisor', 'events_administrator'] as $role) {
            $response = $this->createJson([
                'email' => self::email(), 'role' => $role, 'password' => 'heslo-1234']);
            $this->assertSame(403, $response->getStatusCode(), "events_administrator nezaloží $role");
        }
        $this->assertSame($before, $countLogins(), 'nic se nezapsalo');
    }

    public function testBrowserFormPermissions(): void
    {
        $anonymous = $this->accountControler()->create($this->createPostRequest(['email' => self::email(), 'role' => 'visitor', 'password' => 'heslo-1234']));
        $this->assertSame(401, $anonymous->getStatusCode());
        $this->loginAs('events_administrator');
        $response = $this->accountControler()->create($this->createPostRequest(['email' => self::email(), 'role' => 'editor', 'password' => 'heslo-1234']));
        $this->assertSame(303, $response->getStatusCode());
        $this->assertStringContainsString('nemůžete založit', implode(' ', $this->messages()));
    }

    public function testValidationErrorsInBrowserFormGoToFlash(): void
    {
        $this->loginAs('supervisor');
        $cases = [
            [['email' => 'neplatny', 'role' => 'visitor', 'password' => 'heslo-1234'], 'e-mailovou adresu'],
            [['email' => self::email(), 'role' => '', 'password' => 'heslo-1234'], 'roli'],
            [['email' => self::email(), 'role' => 'neexistuje', 'password' => 'heslo-1234'], 'roli'],
            [['email' => self::email(), 'role' => 'visitor'], 'heslo mailem'],
            [['email' => self::email(), 'role' => 'visitor', 'password' => 'kratke'], '8 znaků'],
            [['email' => self::email(), 'role' => 'visitor', 'password' => 'heslo-1234', 'loginName' => 'jiří'], 'Přihlašovací jméno'],
        ];
        foreach ($cases as [$body, $expected]) {
            $this->flash = new Flash();
            $response = $this->accountControler()->create($this->createPostRequest($body));
            $this->assertSame(303, $response->getStatusCode());
            $this->assertStringContainsString($expected, implode(' ', $this->messages()));
        }
    }

    public function testPasswordByMailSetsPasswordOnceAndLogsIn(): void
    {
        $this->loginAs('supervisor');
        $email = self::email('mail');
        $response = $this->accountControler()->create($this->createPostRequest([
            'email' => $email, 'role' => 'editor', 'passwordByMail' => '1']));
        $this->assertSame(303, $response->getStatusCode());
        $this->assertStringContainsString('odkaz na nastavení hesla', implode(' ', $this->messages()));
        $login = $this->container->get(RegistrationDao::class)->findLoginNamesByEmail($email, true)[0];
        $this->assertSame('', $this->container->get(CredentialsDao::class)->get(['login_name_fk' => $login])['password_hash']);

        $this->assertCount(1, $this->mailSpy->sent);
        $html = $this->mailSpy->sent[0]->getContent()->getHtml();
        $this->assertMatchesRegularExpression('~auth/v1/setpassword/([A-Za-z0-9_-]{43})~', $html);
        preg_match('~auth/v1/setpassword/([A-Za-z0-9_-]{43})~', $html, $m);
        $token = $m[1];
        $this->assertNull($this->container->get(LoginTokenDao::class)->get(['token_hash' => $token]), 'v DB není čitelný token');
        $this->assertStringContainsString('72', $html);

        $this->security = new Security();
        $page = $this->setPasswordControler('GET')->page($this->createPostRequest([]), $token);
        $this->assertStringContainsString('Nastavit heslo', (string) $page->getBody());

        $field = ConfigurationCache::auth()['fieldNameHeslo'];
        $short = $this->setPasswordControler('POST')->save($this->createPostRequest([$field => 'kratke']), $token);
        $this->assertSame(303, $short->getStatusCode());
        $this->assertStringContainsString('chyba=1', $short->getHeaderLine('Location'));
        $this->assertStringContainsString('Nastavit heslo', (string) $this->setPasswordControler('GET')->page($this->createPostRequest([]), $token)->getBody(),
            'krátké heslo token nespotřebuje');

        $ok = $this->setPasswordControler('POST')->save($this->createPostRequest([$field => 'nove-heslo-99']), $token);
        $this->assertSame(303, $ok->getStatusCode());
        $this->assertTrue($this->security->hasValidSecurityContext());
        $this->assertTrue(password_verify('nove-heslo-99', $this->container->get(CredentialsDao::class)->get(['login_name_fk' => $login])['password_hash']));

        $again = $this->setPasswordControler('POST')->save($this->createPostRequest([$field => 'jine-heslo-99']), $token);
        $this->assertSame(303, $again->getStatusCode());
        $this->assertStringContainsString('Odkaz už neplatí', (string) $this->setPasswordControler('GET')->page($this->createPostRequest([]), $token)->getBody());
        $this->assertTrue(password_verify('nove-heslo-99', $this->container->get(CredentialsDao::class)->get(['login_name_fk' => $login])['password_hash']));

        // nový odkaz z administrace
        $this->mailSpy->sent = [];
        $this->loginAs('supervisor');
        $resend = $this->accountControler('/auth/v1/account/:loginname/setpasswordlink')->sendSetPasswordLink($this->createPostRequest([]), $login);
        $this->assertSame(303, $resend->getStatusCode());
        $this->assertCount(1, $this->mailSpy->sent);
    }

    public function testExpiredSetPasswordLinkDoesNotWork(): void
    {
        $this->loginAs('supervisor');
        $email = self::email('exp');
        $this->accountControler()->create($this->createPostRequest(['email' => $email, 'role' => 'visitor', 'passwordByMail' => '1']));
        preg_match('~auth/v1/setpassword/([A-Za-z0-9_-]{43})~', $this->mailSpy->sent[0]->getContent()->getHtml(), $m);
        $dao = $this->container->get(LoginTokenDao::class);
        $row = $dao->get(['token_hash' => LoginTokenService::hash($m[1])]);
        $row['expires'] = '2000-01-01 00:00:00';
        $dao->update($row);
        $this->assertStringContainsString('Odkaz už neplatí', (string) $this->setPasswordControler('GET')->page($this->createPostRequest([]), $m[1])->getBody());
    }

    public function testFailedWriteLeavesNothing(): void
    {
        $failingCredentials = $this->createStub(CredentialsDao::class);
        $failingCredentials->method('insert')->willThrowException(new \RuntimeException('simulovaná chyba'));
        $failingCredentials->method('get')->willReturn(null);
        $c = $this->container;
        $service = new AccountService($c->get(LoginDao::class), $failingCredentials, $c->get(RegistrationDao::class),
            $c->get(RoleDao::class), $c->get(LoginNameGenerator::class), $c->get(DisplayNameService::class),
            $c->get(LoginTokenService::class), new EventsLoginWriter(), '.{8,}', 'Heslo musí mít nejméně 8 znaků.');
        $login = self::PREFIX . 'fail' . substr(uniqid(), -6);
        try {
            $service->create(new AccountRequest(self::email(), 'visitor', $login, 'heslo-1234'), 'supervisor');
            $this->fail('očekávaná chyba');
        } catch (AccountException $e) {
            $this->assertSame(400, $e->httpStatus);
        }
        $this->assertNull($c->get(LoginDao::class)->get(['login_name' => $login]));
        $this->assertNull($c->get(RegistrationDao::class)->get(['login_name_fk' => $login]));
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
        $events = (new EventsModelContainerConfigurator())->configure((new EventsDbContainerConfigurator())->configure(new Container()))
            ->get(EventsLoginDao::class);
        foreach ($events->find('login_name LIKE :l', $like) as $row) {
            $events->delete($row);
        }
    }
}
