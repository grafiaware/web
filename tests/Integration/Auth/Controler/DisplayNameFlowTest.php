<?php
declare(strict_types=1);

namespace Test\Integration\Auth\Controler;

use Application\Feature\FeatureFlags;
use Auth\Authenticator\DbAuthenticator;
use Auth\Authenticator\DbHashAuthenticator;
use Auth\Middleware\Login\Controler\ConfirmControler;
use Auth\Middleware\Login\Controler\DisplayNameControler;
use Auth\Middleware\Login\Controler\LoginLogoutControler;
use Auth\Middleware\Login\Controler\RegistrationControler;
use Auth\Model\Dao\CredentialsDao;
use Auth\Model\Dao\LoginDao;
use Auth\Model\Dao\LoginProfileDao;
use Auth\Model\Dao\LoginTokenDao;
use Auth\Model\Dao\RegistrationDao;
use Auth\Model\Repository\LoginAggregateCredentialsRepo;
use Auth\Model\Repository\LoginAggregateFullRepo;
use Auth\Model\Repository\LoginAggregateRegistrationRepo;
use Auth\Model\Repository\RegistrationRepo;
use Auth\Service\DisplayNameService;
use Pes\Container\Container;
use Pes\Core\Security\Password\Password;
use Pes\Model\RowData\RowData;
use Site\Common\ActiveSite;
use Site\ConfigurationCache;
use Status\Model\Entity\Flash;
use Status\Model\Entity\Presentation;
use Status\Model\Entity\Security;
use Status\Model\Entity\SecurityInterface;
use Status\Model\Repository\StatusFlashRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusSecurityRepo;
use Test\AppRunner\AppRunner;
use Test\Support\AuthControllerContainerFactory;

/**
 * A8 (docs/vp/zadani/A8.md): osobní jméno – při registraci, po přihlášení v session, změna a smazání,
 * účet přihlašovaný jménem + heslem, kaskádové smazání s loginem.
 */
final class DisplayNameFlowTest extends AppRunner
{
    private const PREFIX = 'a8name_test_';

    private Container $container;
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
        [$this->container] = AuthControllerContainerFactory::createWithMailSpy();
        $this->security = new Security();
        $this->flash = new Flash();
    }

    private function features(bool $displayName = true): FeatureFlags
    {
        return new FeatureFlags(['vp.registrace-emailem' => true, 'vp.prihlaseni-odkazem' => true, 'vp.osobni-jmeno' => $displayName]);
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

    private function displayNameControler(): DisplayNameControler
    {
        return (new DisplayNameControler($this->securityRepo(), $this->flashRepo(), $this->presentationRepo(),
            $this->container->get(DisplayNameService::class)))
            ->setFeatureFlags($this->features())->injectContainer($this->container);
    }

    private function profileDao(): LoginProfileDao
    {
        return $this->container->get(LoginProfileDao::class);
    }

    public function testRegistrationWithNameThenConfirmShowsName(): void
    {
        $email = 'A8Name.Test.' . uniqid() . '@example.test';
        (new RegistrationControler($this->securityRepo(), $this->flashRepo(), $this->presentationRepo(),
            $this->container->get(LoginAggregateRegistrationRepo::class)))
            ->setFeatureFlags($this->features())->injectContainer($this->container)
            ->register($this->createPostRequest(['register' => 1, 'email' => $email, 'displayName' => '  Jan   Novák ']));

        $logins = $this->container->get(RegistrationDao::class)->findLoginNamesByEmail($email, false);
        $this->assertCount(1, $logins);
        $loginName = $logins[0];
        $this->assertStringStartsWith(self::PREFIX, $loginName);
        $this->assertSame('Jan Novák', $this->profileDao()->getDisplayName($loginName));

        $uid = $this->container->get(RegistrationRepo::class)->get($loginName)->getUid();
        (new ConfirmControler($this->securityRepo(), $this->flashRepo(), $this->presentationRepo(),
            $this->container->get(LoginAggregateCredentialsRepo::class), $this->container->get(RegistrationRepo::class)))
            ->setFeatureFlags($this->features())->injectContainer($this->container)
            ->confirmAndLogin($this->createPostRequest(['confirm' => 1]), $uid);

        $this->assertTrue($this->security->hasValidSecurityContext());
        $this->assertSame('Jan Novák', $this->security->getInfo(SecurityInterface::INFO_DISPLAY_NAME));
    }

    public function testRegistrationWithoutNameStoresNothing(): void
    {
        $email = 'A8Name.Test.' . uniqid() . '@example.test';
        (new RegistrationControler($this->securityRepo(), $this->flashRepo(), $this->presentationRepo(),
            $this->container->get(LoginAggregateRegistrationRepo::class)))
            ->setFeatureFlags($this->features())->injectContainer($this->container)
            ->register($this->createPostRequest(['register' => 1, 'email' => $email]));
        $loginName = $this->container->get(RegistrationDao::class)->findLoginNamesByEmail($email, false)[0];
        $this->assertNull($this->profileDao()->getDisplayName($loginName));
    }

    public function testRegistrationWithFlagOffIgnoresName(): void
    {
        $email = 'A8Name.Test.' . uniqid() . '@example.test';
        (new RegistrationControler($this->securityRepo(), $this->flashRepo(), $this->presentationRepo(),
            $this->container->get(LoginAggregateRegistrationRepo::class)))
            ->setFeatureFlags($this->features(false))->injectContainer($this->container)
            ->register($this->createPostRequest(['register' => 1, 'email' => $email, 'displayName' => 'Jan Novák']));
        $loginName = $this->container->get(RegistrationDao::class)->findLoginNamesByEmail($email, false)[0];
        $this->assertNull($this->profileDao()->getDisplayName($loginName));
    }

    public function testNamePasswordAccountSetsChangesAndRemovesName(): void
    {
        $loginName = self::PREFIX . 'heslo' . uniqid();
        $row = new RowData();
        $row->offsetSet('login_name', $loginName);
        $this->container->get(LoginDao::class)->insert($row);
        $cred = new RowData();
        $cred->import(['login_name_fk' => $loginName, 'password_hash' => (new Password())->getPasswordHash('heslo1234'), 'role_fk' => 'editor']);
        $this->container->get(CredentialsDao::class)->insert($cred);

        $login = fn() => (new LoginLogoutControler($this->securityRepo(), $this->flashRepo(), $this->presentationRepo(),
                $this->container->get(LoginAggregateFullRepo::class), new DbHashAuthenticator(), new DbAuthenticator()))
            ->setFeatureFlags($this->features())->injectContainer($this->container)
            ->login($this->createPostRequest(['login' => 1,
                ConfigurationCache::auth()['fieldNameJmeno'] => $loginName, ConfigurationCache::auth()['fieldNameHeslo'] => 'heslo1234']));

        $login();
        $this->assertTrue($this->security->hasValidSecurityContext());
        $this->assertNull($this->security->getInfo(SecurityInterface::INFO_DISPLAY_NAME));

        $response = $this->displayNameControler()->change($this->createPostRequest(['displayName' => 'Petra Editorová']));
        $this->assertSame(303, $response->getStatusCode());
        $this->assertSame('Petra Editorová', $this->security->getInfo(SecurityInterface::INFO_DISPLAY_NAME));
        $this->assertSame('Petra Editorová', $this->profileDao()->getDisplayName($loginName));

        // nové přihlášení načte uložené jméno
        $this->security->removeContext();
        $login();
        $this->assertSame('Petra Editorová', $this->security->getInfo(SecurityInterface::INFO_DISPLAY_NAME));

        $this->displayNameControler()->change($this->createPostRequest(['displayName' => '   ']));
        $this->assertNull($this->security->getInfo(SecurityInterface::INFO_DISPLAY_NAME));
        $this->assertNull($this->profileDao()->getDisplayName($loginName));

        // kaskádové smazání s loginem
        $this->displayNameControler()->change($this->createPostRequest(['displayName' => 'Smazat Se Mnou']));
        foreach ([CredentialsDao::class] as $daoClass) {
            $dao = $this->container->get($daoClass);
            foreach ($dao->find('login_name_fk = :l', ['l' => $loginName]) as $r) {
                $dao->delete($r);
            }
        }
        $loginDao = $this->container->get(LoginDao::class);
        $loginDao->delete($loginDao->get(['login_name' => $loginName]));
        $this->assertNull($this->profileDao()->getDisplayName($loginName));
        $this->assertNull($this->profileDao()->get(['login_name_fk' => $loginName]));
    }

    public function testNotLoggedInGets401AndNothingIsSaved(): void
    {
        $response = $this->displayNameControler()->change($this->createPostRequest(['displayName' => 'Nikdo']));
        $this->assertSame(401, $response->getStatusCode());
        $this->assertNull($this->security->getInfo(SecurityInterface::INFO_DISPLAY_NAME));
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
