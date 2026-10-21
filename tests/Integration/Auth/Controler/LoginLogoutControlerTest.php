<?php
declare(strict_types=1);

namespace Test\Integration\Auth\Controler;

use Access\Enum\RoleEnum;
use Auth\Middleware\Login\Controler\LoginLogoutControler;
use Auth\Model\Dao\CredentialsDao;
use Auth\Model\Dao\LoginDao;
use Auth\Model\Repository\LoginAggregateFullRepo;
use Container\AuthContainerConfigurator;
use Container\AuthDbContainerConfigurator;
use Pes\Container\Container;
use Pes\Model\RowData\RowData;
use Site\Common\ActiveSite;
use Site\ConfigurationCache;
use Status\Model\Entity\Flash;
use Status\Model\Entity\Presentation;
use Status\Model\Entity\Security;
use Status\Model\Enum\FlashSeverityEnum;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusSecurityRepo;
use Test\AppRunner\AppRunner;
use Test\Support\StubAuthenticator;

final class LoginLogoutControlerTest extends AppRunner
{
    private const PREFIX = 'LoginLogoutCtrlTest_';
    private const LOGIN_NAME = self::PREFIX . 'visitor';
    private const PASSWORD = 'TestHeslo1';

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

    public function testLoginSuccessSetsSecurityContext(): void
    {
        $security = new Security();
        $securityRepo = $this->createMock(StatusSecurityRepo::class);
        $securityRepo->method('get')->willReturn($security);

        $flash = new Flash();
        $flashRepo = $this->createMock(StatusFlashRepo::class);
        $flashRepo->method('get')->willReturn($flash);

        $presentation = new Presentation();
        $presentation->setLastGetResourcePath('/events');
        $presentationRepo = $this->createMock(StatusPresentationRepo::class);
        $presentationRepo->method('getClone')->willReturn($presentation);

        $auth = ConfigurationCache::auth();
        $request = $this->createPostRequest([
            'login' => true,
            $auth['fieldNameJmeno'] => self::LOGIN_NAME,
            $auth['fieldNameHeslo'] => self::PASSWORD,
        ]);

        $controler = new LoginLogoutControler(
            $securityRepo,
            $flashRepo,
            $presentationRepo,
            $this->loginRepo,
            new StubAuthenticator(true, self::PASSWORD),
            new StubAuthenticator(false)
        );

        $response = $controler->login($request);

        $this->assertSame(303, $response->getStatusCode());
        $this->assertSame('/events', $response->getHeaderLine('Location'));
        $this->assertTrue($security->hasValidSecurityContext());
        $this->assertSame(self::LOGIN_NAME, $security->getLoginAggregate()->getLoginName());
        $flash->storeMessages();
        $messages = $flash->getMessages();
        $this->assertNotEmpty($messages);
        $this->assertSame(FlashSeverityEnum::SUCCESS, $messages[0]['severity']);
    }

    public function testLoginFailureAddsWarningFlash(): void
    {
        $security = new Security();
        $securityRepo = $this->createMock(StatusSecurityRepo::class);
        $securityRepo->method('get')->willReturn($security);

        $flash = new Flash();
        $flashRepo = $this->createMock(StatusFlashRepo::class);
        $flashRepo->method('get')->willReturn($flash);

        $presentation = new Presentation();
        $presentationRepo = $this->createMock(StatusPresentationRepo::class);
        $presentationRepo->method('getClone')->willReturn($presentation);

        $auth = ConfigurationCache::auth();
        $request = $this->createPostRequest([
            'login' => true,
            $auth['fieldNameJmeno'] => self::LOGIN_NAME,
            $auth['fieldNameHeslo'] => 'WrongPass1',
        ]);

        $controler = new LoginLogoutControler(
            $securityRepo,
            $flashRepo,
            $presentationRepo,
            $this->loginRepo,
            new StubAuthenticator(false),
            new StubAuthenticator(false)
        );

        $controler->login($request);

        $this->assertFalse($security->hasValidSecurityContext());
        $flash->storeMessages();
        $messages = $flash->getMessages();
        $this->assertNotEmpty($messages);
        $this->assertSame(FlashSeverityEnum::WARNING, $messages[0]['severity']);
    }

    public function testLogoutClearsSecurityContext(): void
    {
        $security = new Security();
        $securityRepo = $this->createMock(StatusSecurityRepo::class);
        $securityRepo->method('get')->willReturn($security);

        $flashRepo = $this->createMock(StatusFlashRepo::class);
        $flashRepo->method('get')->willReturn(new Flash());

        $presentation = new Presentation();
        $presentationRepo = $this->createMock(StatusPresentationRepo::class);
        $presentationRepo->method('getClone')->willReturn($presentation);

        $aggregate = $this->loginRepo->get(self::LOGIN_NAME);
        $this->assertNotNull($aggregate);
        $security->newContext($aggregate);

        $controler = new LoginLogoutControler(
            $securityRepo,
            $flashRepo,
            $presentationRepo,
            $this->loginRepo,
            new StubAuthenticator(),
            new StubAuthenticator()
        );

        $response = $controler->logout($this->createPostRequest(['logout' => true]));

        $this->assertSame(303, $response->getStatusCode());
        $this->assertSame('/', $response->getHeaderLine('Location'));
        $this->assertFalse($security->hasValidSecurityContext());
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
            'password_hash' => self::PASSWORD,
            'role_fk' => RoleEnum::VISITOR,
        ]);
        $credentialsDao->insert($credentialsRow);
    }

    private static function cleanup(): void
    {
        $container = (new AuthContainerConfigurator())->configure(
            (new AuthDbContainerConfigurator())->configure(new Container())
        );

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
