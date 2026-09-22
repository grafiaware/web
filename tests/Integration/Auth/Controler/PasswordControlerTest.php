<?php
declare(strict_types=1);

namespace Test\Integration\Auth\Controler;

use Access\Enum\RoleEnum;
use Auth\Middleware\Login\Controler\PasswordControler;
use Auth\Model\Dao\CredentialsDao;
use Auth\Model\Dao\LoginDao;
use Auth\Model\Dao\RegistrationDao;
use Auth\Model\Entity\LoginAggregateFull;
use Auth\Model\Repository\LoginAggregateFullRepo;
use Container\AuthContainerConfigurator;
use Container\AuthDbContainerConfigurator;
use Pes\Container\Container;
use Pes\Core\Security\Password\Password;
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
use Test\Support\StubAuthenticator;

final class PasswordControlerTest extends AppRunner
{
    private const PREFIX = 'PasswordCtrlTest_';
    private const LOGIN_NAME = self::PREFIX . 'user';
    private const OLD_PASSWORD = 'OldPass123';
    private const NEW_PASSWORD = 'NewPass456';

    private LoginAggregateFullRepo $loginRepo;

    public static function setUpBeforeClass(): void
    {
        self::bootstrapBeforeClass();
        if (!defined('PES_RUNNING_ON_PRODUCTION_HOST')) {
            define('PES_RUNNING_ON_PRODUCTION_HOST', false);
        }
        ActiveSite::force('NajdiSi');
        ConfigurationCache::resetCache();
        self::cleanup();
        self::seedUser();
    }

    protected function setUp(): void
    {
        $container = (new AuthContainerConfigurator())->configure(
            (new AuthDbContainerConfigurator())->configure(new Container())
        );
        $this->loginRepo = $container->get(LoginAggregateFullRepo::class);
    }

    public static function tearDownAfterClass(): void
    {
        self::cleanup();
        ActiveSite::reset();
        ConfigurationCache::resetCache();
    }

    public function testChangePasswordWithoutLoginReturnsUnauthorized(): void
    {
        $controler = $this->createControler(new StubAuthenticator());

        $response = $controler->changePassword($this->createPostRequest(['changepassword' => true]));

        $this->assertSame(401, $response->getStatusCode());
    }

    public function testChangePasswordRejectsWrongOldPassword(): void
    {
        $security = new Security();
        $aggregate = $this->loginRepo->get(self::LOGIN_NAME);
        $this->assertNotNull($aggregate);
        $security->newContext($aggregate);

        $flash = new Flash();
        $controler = $this->createControler(new StubAuthenticator(false), $security, $flash);

        $auth = ConfigurationCache::auth();
        $controler->changePassword($this->createPostRequest([
            'changepassword' => true,
            $auth['fieldNameHesloStare'] => 'WrongPass1',
            $auth['fieldNameHeslo'] => self::NEW_PASSWORD,
        ]));

        $flash->storeMessages();
        $messages = $flash->getMessages();
        $this->assertNotEmpty($messages);
        $this->assertStringContainsString('staré heslo', $messages[0]['message']);
    }

    public function testChangePasswordUpdatesHashWhenOldPasswordMatches(): void
    {
        $security = new Security();
        $aggregate = $this->loginRepo->get(self::LOGIN_NAME);
        $this->assertNotNull($aggregate);
        $security->newContext($aggregate);

        $flash = new Flash();
        $controler = $this->createControler(
            new StubAuthenticator(true, self::OLD_PASSWORD),
            $security,
            $flash
        );

        $auth = ConfigurationCache::auth();
        $response = $controler->changePassword($this->createPostRequest([
            'changepassword' => true,
            $auth['fieldNameHesloStare'] => self::OLD_PASSWORD,
            $auth['fieldNameHeslo'] => self::NEW_PASSWORD,
        ]));
        $this->loginRepo->flush();

        $this->assertSame(303, $response->getStatusCode());
        $flash->storeMessages();
        $this->assertStringContainsString('změněno', $flash->getMessages()[0]['message']);

        $reloaded = $this->loginRepo->get(self::LOGIN_NAME);
        $this->assertTrue((new Password())->verifyPassword(self::NEW_PASSWORD, $reloaded->getCredentials()->getPasswordHash()));
    }

    public function testForgottenPasswordSendsMailWhenRegistrationExists(): void
    {
        [$container, $mailSpy] = AuthControllerContainerFactory::createWithMailSpy();
        $loginRepo = $container->get(LoginAggregateFullRepo::class);

        $flash = new Flash();
        $controler = $this->createControler(new StubAuthenticator(), security: null, flash: $flash);
        $controler->injectContainer($container);

        $auth = ConfigurationCache::auth();
        $controler->forgottenPassword($this->createPostRequest([
            'forgottenpassword' => true,
            $auth['fieldNameJmeno'] => self::LOGIN_NAME,
        ]));
        $loginRepo->flush();

        $this->assertCount(1, $mailSpy->sent);
        $flash->storeMessages();
        $this->assertStringContainsString('email', $flash->getMessages()[0]['message']);

        $reloaded = $loginRepo->get(self::LOGIN_NAME);
        $this->assertNotSame(
            (new Password())->getPasswordHash(self::OLD_PASSWORD),
            $reloaded->getCredentials()->getPasswordHash()
        );
    }

    private function createControler(
        StubAuthenticator $authenticator,
        ?Security $security = null,
        ?Flash $flash = null,
    ): PasswordControler {
        $security ??= new Security();
        $flash ??= new Flash();

        $securityRepo = $this->createMock(StatusSecurityRepo::class);
        $securityRepo->method('get')->willReturn($security);

        $flashRepo = $this->createMock(StatusFlashRepo::class);
        $flashRepo->method('get')->willReturn($flash);

        $presentationRepo = $this->createMock(StatusPresentationRepo::class);
        $presentationRepo->method('getClone')->willReturn(new Presentation());

        return new PasswordControler(
            $securityRepo,
            $flashRepo,
            $presentationRepo,
            $this->loginRepo,
            $authenticator
        );
    }

    private static function seedUser(): void
    {
        $container = (new AuthContainerConfigurator())->configure(
            (new AuthDbContainerConfigurator())->configure(new Container())
        );

        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        $loginRow = new RowData();
        $loginRow->offsetSet('login_name', self::LOGIN_NAME);
        $loginDao->insert($loginRow);

        /** @var CredentialsDao $credentialsDao */
        $credentialsDao = $container->get(CredentialsDao::class);
        $credentialsRow = new RowData();
        $credentialsRow->import([
            'login_name_fk' => self::LOGIN_NAME,
            'password_hash' => (new Password())->getPasswordHash(self::OLD_PASSWORD),
            'role_fk' => RoleEnum::VISITOR,
        ]);
        $credentialsDao->insert($credentialsRow);

        /** @var RegistrationDao $registrationDao */
        $registrationDao = $container->get(RegistrationDao::class);
        $registrationRow = new RowData();
        $registrationRow->import([
            'login_name_fk' => self::LOGIN_NAME,
            'password_hash' => self::OLD_PASSWORD,
            'email' => self::LOGIN_NAME . '@example.cz',
            'email_time' => (new \DateTime())->format('Y-m-d H:i:s'),
        ]);
        $registrationDao->insert($registrationRow);
    }

    private static function cleanup(): void
    {
        $container = (new AuthContainerConfigurator())->configure(
            (new AuthDbContainerConfigurator())->configure(new Container())
        );

        /** @var RegistrationDao $registrationDao */
        $registrationDao = $container->get(RegistrationDao::class);
        foreach ($registrationDao->find('login_name_fk LIKE :login_name_like', ['login_name_like' => self::PREFIX . '%']) as $row) {
            $registrationDao->delete($row);
        }

        /** @var CredentialsDao $credentialsDao */
        $credentialsDao = $container->get(CredentialsDao::class);
        foreach ($credentialsDao->find('login_name_fk LIKE :login_name_like', ['login_name_like' => self::PREFIX . '%']) as $row) {
            $credentialsDao->delete($row);
        }

        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        foreach ($loginDao->find('login_name LIKE :login_name_like', ['login_name_like' => self::PREFIX . '%']) as $row) {
            $loginDao->delete($row);
        }
    }
}
