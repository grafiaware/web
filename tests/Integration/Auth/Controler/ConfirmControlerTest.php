<?php
declare(strict_types=1);

namespace Test\Integration\Auth\Controler;

use Auth\Middleware\Login\Controler\ConfirmControler;
use Auth\Model\Dao\LoginDao;
use Auth\Model\Dao\RegistrationDao;
use Auth\Model\Repository\LoginAggregateCredentialsRepo;
use Auth\Model\Repository\RegistrationRepo;
use Container\AuthContainerConfigurator;
use Container\AuthDbContainerConfigurator;
use Pes\Container\Container;
use Pes\Core\Security\Password\Password;
use Pes\Model\RowData\RowData;
use Site\Common\ActiveSite;
use Site\ConfigurationCache;
use Status\Model\Entity\Flash;
use Status\Model\Entity\Presentation;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusSecurityRepo;
use Test\AppRunner\AppRunner;
use Test\Support\AuthControllerContainerFactory;

final class ConfirmControlerTest extends AppRunner
{
    private const PREFIX = 'ConfirmCtrlTest_';
    private const LOGIN_NAME = self::PREFIX . 'user';
    private const PLAIN_PASSWORD = 'ConfirmPass1';

    private static string $uid;

    public static function setUpBeforeClass(): void
    {
        self::bootstrapBeforeClass();
        if (!defined('PES_RUNNING_ON_PRODUCTION_HOST')) {
            define('PES_RUNNING_ON_PRODUCTION_HOST', false);
        }
        ActiveSite::force('NajdiSi');
        ConfigurationCache::resetCache();
        self::cleanup();
        self::seedPendingRegistration();
    }

    public static function tearDownAfterClass(): void
    {
        self::cleanup();
        ActiveSite::reset();
        ConfigurationCache::resetCache();
    }

    public function testConfirmCreatesCredentialsAndSendsMail(): void
    {
        [$container, $mailSpy] = AuthControllerContainerFactory::createWithMailSpy();

        /** @var LoginAggregateCredentialsRepo $loginAggCredRepo */
        $loginAggCredRepo = $container->get(LoginAggregateCredentialsRepo::class);
        /** @var RegistrationRepo $registrationRepo */
        $registrationRepo = $container->get(RegistrationRepo::class);

        $before = $loginAggCredRepo->get(self::LOGIN_NAME);
        $this->assertNotNull($before);
        $this->assertNull($before->getCredentials());

        $flash = new Flash();
        $controler = new ConfirmControler(
            $this->createMock(StatusSecurityRepo::class),
            $this->mockFlashRepo($flash),
            $this->mockPresentationRepo(),
            $loginAggCredRepo,
            $registrationRepo
        );
        $controler->injectContainer($container);

        $response = $controler->confirm($this->createPostRequest([]), self::$uid);

        $this->assertSame(303, $response->getStatusCode());
        $this->assertCount(1, $mailSpy->sent);

        $loginAggCredRepo->flush();
        $after = $loginAggCredRepo->get(self::LOGIN_NAME);
        $this->assertNotNull($after->getCredentials());
        $this->assertTrue((new Password())->verifyPassword(self::PLAIN_PASSWORD, $after->getCredentials()->getPasswordHash()));

        $registration = $registrationRepo->get(self::LOGIN_NAME);
        $this->assertSame('', $registration->getPasswordHash());

        $flash->storeMessages();
        $this->assertStringContainsString('dokončena', $flash->getMessages()[0]['message']);
    }

    public function testConfirmUnknownUidDoesNotCreateCredentials(): void
    {
        [$container] = AuthControllerContainerFactory::createWithMailSpy();
        $loginAggCredRepo = $container->get(LoginAggregateCredentialsRepo::class);
        $registrationRepo = $container->get(RegistrationRepo::class);

        $flash = new Flash();
        $controler = new ConfirmControler(
            $this->createMock(StatusSecurityRepo::class),
            $this->mockFlashRepo($flash),
            $this->mockPresentationRepo(),
            $loginAggCredRepo,
            $registrationRepo
        );
        $controler->injectContainer($container);

        $controler->confirm($this->createPostRequest([]), '00000000-0000-0000-0000-000000000000');

        $flash->storeMessages();
        $this->assertEmpty($flash->getMessages());
    }

    private function mockFlashRepo(Flash $flash): StatusFlashRepo
    {
        $repo = $this->createMock(StatusFlashRepo::class);
        $repo->method('get')->willReturn($flash);

        return $repo;
    }

    private function mockPresentationRepo(): StatusPresentationRepo
    {
        $repo = $this->createMock(StatusPresentationRepo::class);
        $repo->method('getClone')->willReturn(new Presentation());

        return $repo;
    }

    private static function seedPendingRegistration(): void
    {
        $container = (new AuthContainerConfigurator())->configure(
            (new AuthDbContainerConfigurator())->configure(new Container())
        );

        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        $loginRow = new RowData();
        $loginRow->offsetSet('login_name', self::LOGIN_NAME);
        $loginDao->insert($loginRow);

        /** @var RegistrationDao $registrationDao */
        $registrationDao = $container->get(RegistrationDao::class);
        $registrationRow = new RowData();
        $registrationRow->import([
            'login_name_fk' => self::LOGIN_NAME,
            'password_hash' => self::PLAIN_PASSWORD,
            'email' => self::LOGIN_NAME . '@example.cz',
            'email_time' => (new \DateTime())->format('Y-m-d H:i:s'),
        ]);
        $registrationDao->insert($registrationRow);
        self::$uid = $registrationRow['uid'];
    }

    private static function cleanup(): void
    {
        $container = (new AuthContainerConfigurator())->configure(
            (new AuthDbContainerConfigurator())->configure(new Container())
        );

        /** @var \Auth\Model\Dao\CredentialsDao $credentialsDao */
        $credentialsDao = $container->get(\Auth\Model\Dao\CredentialsDao::class);
        foreach ($credentialsDao->find('login_name_fk LIKE :login_name_like', ['login_name_like' => self::PREFIX . '%']) as $row) {
            $credentialsDao->delete($row);
        }

        /** @var RegistrationDao $registrationDao */
        $registrationDao = $container->get(RegistrationDao::class);
        foreach ($registrationDao->find('login_name_fk LIKE :login_name_like', ['login_name_like' => self::PREFIX . '%']) as $row) {
            $registrationDao->delete($row);
        }

        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        foreach ($loginDao->find('login_name LIKE :login_name_like', ['login_name_like' => self::PREFIX . '%']) as $row) {
            $loginDao->delete($row);
        }
    }
}
