<?php
declare(strict_types=1);

namespace Test\Integration\Auth\Controler;

use Application\Feature\FeatureFlags;
use Auth\Authenticator\DbAuthenticator;
use Auth\Authenticator\DbHashAuthenticator;
use Auth\Middleware\Login\Controler\ConfirmControler;
use Auth\Middleware\Login\Controler\LoginLinkControler;
use Auth\Middleware\Login\Controler\LoginLogoutControler;
use Auth\Middleware\Login\Controler\PasswordControler;
use Auth\Middleware\Login\Controler\RegistrationControler;
use Auth\Model\Dao\CredentialsDao;
use Auth\Model\Dao\LoginDao;
use Auth\Model\Dao\LoginTokenDao;
use Auth\Model\Dao\RegistrationDao;
use Auth\Model\Repository\LoginAggregateCredentialsRepo;
use Auth\Model\Repository\LoginAggregateFullRepo;
use Auth\Model\Repository\LoginAggregateRegistrationRepo;
use Auth\Model\Repository\RegistrationRepo;
use Auth\Service\LoginTokenService;
use Pes\Container\Container;
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
 * A1+A2 (docs/vp/zadani/A1.md): celý tok s zapnutými přepínači vp.registrace-emailem a vp.prihlaseni-odkazem –
 * registrace e-mailem bez hesla → opakovaná registrace → potvrzení a přihlášení → přihlašovací odkaz →
 * nastavení hesla bez starého → přihlášení e-mailem a heslem.
 */
final class RegistrationByEmailFlowTest extends AppRunner
{
    private const PREFIX = 'a1flow_test_';

    private static string $email;
    private static string $loginName;
    private static ?string $uid = null;

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
        $id = uniqid();
        self::$email = 'A1Flow.Test.' . $id . '@example.test';
        self::$loginName = self::PREFIX . strtolower($id);
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
        $this->security = self::$sharedSecurity ??= new Security();
        $this->flash = new Flash();
    }

    private static ?Security $sharedSecurity = null;

    private function features(): FeatureFlags
    {
        return new FeatureFlags(['vp.registrace-emailem' => true, 'vp.prihlaseni-odkazem' => true]);
    }

    private function securityRepo(): StatusSecurityRepo
    {
        $repo = $this->createStub(StatusSecurityRepo::class);
        $repo->method('get')->willReturn($this->security);
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

    private function registration(): RegistrationControler
    {
        return (new RegistrationControler($this->securityRepo(), $this->flashRepo(), $this->presentationRepo(),
            $this->container->get(LoginAggregateRegistrationRepo::class)))
            ->setFeatureFlags($this->features())->injectContainer($this->container);
    }

    private function register(array $extra = []): void
    {
        $this->registration()->register($this->createPostRequest(['register' => 1, 'email' => self::$email] + $extra));
    }

    public function test1RegisterWithoutPasswordGeneratesLoginFromEmail(): void
    {
        $this->register();

        $names = $this->container->get(RegistrationDao::class)->findLoginNamesByEmail(self::$email, false);
        $this->assertSame([self::$loginName], $names);
        $registration = $this->container->get(RegistrationRepo::class)->get(self::$loginName);
        $this->assertSame('', $registration->getPasswordHash());
        self::$uid = $registration->getUid();

        $this->assertCount(1, $this->mailSpy->sent);
        $html = $this->mailSpy->sent[0]->getContent()->getHtml();
        $this->assertStringContainsString('auth/v1/confirm/' . self::$uid, $html);
        $this->assertStringContainsString('dalším postupem', $this->messages()[0]);
    }

    public function test2SecondRegistrationWithSameEmailResendsConfirmation(): void
    {
        $this->register(['fieldNameExhibitor' => 1, 'info' => 'Jiná firma']);

        $this->assertSame([self::$loginName], $this->container->get(RegistrationDao::class)->findLoginNamesByEmail(self::$email, false));
        $this->assertCount(1, $this->mailSpy->sent);
        $this->assertStringContainsString('auth/v1/confirm/' . self::$uid, $this->mailSpy->sent[0]->getContent()->getHtml());
    }

    public function test3ConfirmGetShowsButtonAndPostConfirmsAndLogsIn(): void
    {
        $controler = (new ConfirmControler($this->securityRepo(), $this->flashRepo(), $this->presentationRepo(),
            $this->container->get(LoginAggregateCredentialsRepo::class), $this->container->get(RegistrationRepo::class)))
            ->setFeatureFlags($this->features())->injectContainer($this->container);

        $page = $controler->confirm($this->createPostRequest([]), self::$uid);
        $this->assertSame(200, $page->getStatusCode());
        $this->assertStringContainsString('Dokončit registraci', (string) $page->getBody());
        $this->assertNull($this->container->get(LoginAggregateCredentialsRepo::class)->get(self::$loginName)->getCredentials());

        $response = $controler->confirmAndLogin($this->createPostRequest(['confirm' => 1]), self::$uid);
        $this->assertSame(303, $response->getStatusCode());

        $cred = $this->container->get(CredentialsDao::class)->get(['login_name_fk' => self::$loginName]);
        $this->assertNotNull($cred);
        $this->assertSame('', $cred['password_hash']);
        $this->assertSame('visitor', $cred['role_fk']);
        $this->assertTrue($this->security->hasValidSecurityContext());
        $this->assertSame(self::$loginName, $this->security->getLoginAggregate()->getLoginName());

        $this->assertCount(1, $this->mailSpy->sent);  // ne zástupce firmy (checkbox jen ve 2. registraci, ta účet nevytvořila)
        $this->assertStringContainsString(self::$loginName, $this->mailSpy->sent[0]->getContent()->getHtml());
    }

    public function test4PasswordLoginOfAccountWithoutPasswordFails(): void
    {
        $this->security->removeContext();
        $this->login(self::$email, 'cokoli12345');
        $this->assertFalse($this->security->hasValidSecurityContext());
    }

    public function test5LoginLinkLogsInOnlyOnce(): void
    {
        $controler = $this->loginLinkControler();
        $controler->send($this->createPostRequest(['email' => '  ' . strtoupper(self::$email)]));
        $this->assertCount(1, $this->mailSpy->sent);
        $html = $this->mailSpy->sent[0]->getContent()->getHtml();
        $this->assertMatchesRegularExpression('#auth/v1/loginlink/([A-Za-z0-9_-]{43})#', $html);
        preg_match('#auth/v1/loginlink/([A-Za-z0-9_-]{43})#', $html, $m);
        $token = $m[1];

        $row = $this->container->get(LoginTokenDao::class)->get(['token_hash' => hash('sha256', $token)]);
        $this->assertSame(self::$loginName, $row['login_name_fk']);

        $page = $controler->page($this->createPostRequest([]), $token);
        $this->assertStringContainsString('Přihlásit se', (string) $page->getBody());
        $this->assertFalse($this->security->hasValidSecurityContext(), 'GET token nesmí přihlásit ani spotřebovat');

        $controler->login($this->createPostRequest([]), $token);
        $this->assertTrue($this->security->hasValidSecurityContext());
        $this->assertGreaterThanOrEqual(time(), $this->security->getInfo(LoginLinkControler::INFO_PASSWORD_SET_UNTIL));

        $this->security->removeContext();
        $controler->login($this->createPostRequest([]), $token);
        $this->assertFalse($this->security->hasValidSecurityContext());
        $this->assertStringContainsString('není platný', implode(' ', $this->messages()));
    }

    public function test6UnknownEmailGetsSameAnswerAndNoMail(): void
    {
        $this->loginLinkControler()->send($this->createPostRequest(['email' => 'nikdo.' . uniqid() . '@example.test']));
        $this->assertCount(0, $this->mailSpy->sent);
        $this->assertStringContainsString('Pokud u nás účet', $this->messages()[0]);
    }

    public function test7SetPasswordWithoutOldThenLoginByEmailAndName(): void
    {
        $fullRepo = $this->container->get(LoginAggregateFullRepo::class);
        $this->security->newContext($fullRepo->get(self::$loginName));
        $auth = ConfigurationCache::auth();
        $controler = (new PasswordControler($this->securityRepo(), $this->flashRepo(), $this->presentationRepo(),
            $fullRepo, new DbHashAuthenticator()))->setFeatureFlags($this->features())->injectContainer($this->container);

        $controler->changePassword($this->createPostRequest(['changepassword' => 1, $auth['fieldNameHeslo'] => 'kratke']));
        $this->assertStringContainsString('8 znaků', $this->messages()[0]);

        $controler->changePassword($this->createPostRequest(['changepassword' => 1, $auth['fieldNameHeslo'] => 'NoveHeslo123']));
        $fullRepo->flush();
        $this->assertStringContainsString('změněno', $this->messages()[0]);

        $this->security->removeContext();
        [$this->container] = AuthControllerContainerFactory::createWithMailSpy();
        $this->login(self::$email, 'NoveHeslo123');
        $this->assertTrue($this->security->hasValidSecurityContext());

        $this->security->removeContext();
        $this->login(self::$loginName, 'NoveHeslo123');
        $this->assertTrue($this->security->hasValidSecurityContext());
    }

    public function test8RegistrationOfConfirmedEmailSendsLoginLinkNotNewAccount(): void
    {
        $this->register();
        $this->assertSame([self::$loginName], $this->container->get(RegistrationDao::class)->findLoginNamesByEmail(self::$email, false));
        $this->assertCount(1, $this->mailSpy->sent);
        $this->assertStringContainsString('auth/v1/loginlink/', $this->mailSpy->sent[0]->getContent()->getHtml());
    }

    public function test9ExhibitorWithPasswordAndWithoutPassword(): void
    {
        $auth = ConfigurationCache::auth();
        foreach (['s heslem' => 'TajneHeslo99', 'bez hesla' => ''] as $case => $password) {
            [$this->container, $this->mailSpy] = AuthControllerContainerFactory::createWithMailSpy();
            $email = 'a1flow.test.repre' . uniqid() . '@example.test';
            $this->registration()->register($this->createPostRequest(['register' => 1, 'email' => $email,
                $auth['fieldNameHeslo'] => $password, 'fieldNameExhibitor' => 1, 'info' => 'Firma s.r.o.']));
            [$login] = $this->container->get(RegistrationDao::class)->findLoginNamesByEmail($email, false);
            $registration = $this->container->get(RegistrationRepo::class)->get($login);
            $this->assertSame('Firma s.r.o.', $registration->getInfo(), $case);

            $this->mailSpy->sent = [];
            (new ConfirmControler($this->securityRepo(), $this->flashRepo(), $this->presentationRepo(),
                $this->container->get(LoginAggregateCredentialsRepo::class), $this->container->get(RegistrationRepo::class)))
                ->setFeatureFlags($this->features())->injectContainer($this->container)
                ->confirmAndLogin($this->createPostRequest(['confirm' => 1]), $registration->getUid());

            $cred = $this->container->get(CredentialsDao::class)->get(['login_name_fk' => $login]);
            $this->assertSame('visitor', $cred['role_fk'], $case);
            if ($password === '') {
                $this->assertSame('', $cred['password_hash'], $case);
            } else {
                $this->assertTrue(password_verify($password, $cred['password_hash']), $case);
            }
            $regRow = AuthControllerContainerFactory::createWithMailSpy()[0]->get(RegistrationDao::class)->get(['login_name_fk' => $login]);
            $this->assertSame('', $regRow['password_hash'], "$case: heslo z registrace smazané");

            $this->assertCount(2, $this->mailSpy->sent, $case);   // zástupci + organizátorovi
            $grafia = $this->mailSpy->sent[1]->getContent()->getHtml();
            $this->assertStringContainsString('Firma s.r.o.', $grafia);
            if ($password === '') {
                $this->assertStringContainsString('bez hesla', $grafia);
            }
            $this->security->removeContext();
        }
    }

    private function loginLinkControler(): LoginLinkControler
    {
        return (new LoginLinkControler($this->securityRepo(), $this->flashRepo(), $this->presentationRepo(),
            $this->container->get(LoginTokenService::class), $this->container->get(LoginAggregateFullRepo::class)))
            ->setFeatureFlags($this->features())->injectContainer($this->container);
    }

    private function login(string $name, string $password): void
    {
        $auth = ConfigurationCache::auth();
        (new LoginLogoutControler($this->securityRepo(), $this->flashRepo(), $this->presentationRepo(),
            $this->container->get(LoginAggregateFullRepo::class), new DbHashAuthenticator(), new DbAuthenticator()))
            ->setFeatureFlags($this->features())->injectContainer($this->container)
            ->login($this->createPostRequest(['login' => 1, $auth['fieldNameJmeno'] => $name, $auth['fieldNameHeslo'] => $password]));
    }

    private static function cleanup(): void
    {
        [$c] = AuthControllerContainerFactory::createWithMailSpy();
        $like = ['l' => self::PREFIX . '%'];
        foreach ([LoginTokenDao::class, CredentialsDao::class, RegistrationDao::class] as $daoClass) {
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
