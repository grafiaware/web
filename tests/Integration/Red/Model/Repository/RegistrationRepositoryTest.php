<?php
declare(strict_types=1);
namespace Test\Integration\Repository;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use PHPUnit\Framework\TestCase;

use Pes\Http\Factory\EnvironmentFactory;

use Pes\Container\Container;

use Test\Integration\Red\Container\TestHierarchyContainerConfigurator;
use Test\Integration\Red\Container\TestDbUpgradeContainerConfigurator;

use Auth\Model\Entity\Registration;
use Auth\Model\Dao\LoginDao;
use Auth\Model\Dao\RegistrationDao;
use Auth\Model\Repository\RegistrationRepo;


/**
 *
 * @author pes2704
 */
class RegistrationRepositoryTest extends TestCase {

    private static $inputStream;

    private $app;
    private $container;

    /**
     *
     * @var RegistrationRepo
     */
    private $registrationRepo;

    static $uidForEmail;

    public static function mock(array $userData = []) {
        //Validates if default protocol is HTTPS to set default port 443
        if ((isset($userData['HTTPS']) && $userData['HTTPS'] !== 'off') ||
            ((isset($userData['REQUEST_SCHEME']) && $userData['REQUEST_SCHEME'] === 'https'))) {
            $defscheme = 'https';
            $defport = 443;
        } else {
            $defscheme = 'http';
            $defport = 80;
        }

        $data = array_merge([
            'SERVER_PROTOCOL'      => 'HTTP/1.1',
            'REQUEST_METHOD'       => 'GET',
            'REQUEST_SCHEME'       => $defscheme,
            'SCRIPT_NAME'          => '',
            'REQUEST_URI'          => '',
            'QUERY_STRING'         => '',
            'SERVER_NAME'          => 'localhost',
            'SERVER_PORT'          => $defport,
            'HTTP_HOST'            => 'localhost',
            'HTTP_ACCEPT'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'HTTP_ACCEPT_LANGUAGE' => 'en-US,en;q=0.8',
            'HTTP_ACCEPT_CHARSET'  => 'ISO-8859-1,utf-8;q=0.7,*;q=0.3',
            'HTTP_USER_AGENT'      => 'PES',
            'REMOTE_ADDR'          => '127.0.0.1',
            'REQUEST_TIME'         => time(),
            'REQUEST_TIME_FLOAT'   => microtime(true),
        ], $userData);

         return (new EnvironmentFactory())->create($data, self::$inputStream);
    }

    public static function setUpBeforeClass(): void {
        if ( !defined('PES_DEVELOPMENT') AND !defined('PES_PRODUCTION') ) {
            define('PES_FORCE_DEVELOPMENT', 'force_development');
            //// nebo
            //define('PES_FORCE_PRODUCTION', 'force_production');

            define('PROJECT_PATH', 'c:/ApacheRoot/web/');

            include '../vendor/pes/pes/src/Bootstrap/Bootstrap.php';
        }

        // input stream je možné otevřít jen jednou
        self::$inputStream = fopen('php://temp', 'w+');  // php://temp will store its data in memory but will use a temporary file once the amount of data stored hits a predefined limit (the default is 2 MB). The location of this temporary file is determined in the same way as the sys_get_temp_dir() function.

        $container =
                (new TestHierarchyContainerConfigurator())->configure(
                           (new TestDbUpgradeContainerConfigurator())->configure(new Container())
                        );

        // mazání - zde jen pro případ, že minulý test nebyl dokončen
        self::deleteRecords($container);

        // toto je příprava testu
        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        /** @var RegistrationDao $registrationDao */
        $registrationDao = $container->get(RegistrationDao::class);
        $loginDao->insertWithKeyVerification(['login_name'=>"testRegistration"]);
        $registrationDao->insert(['login_name_fk'=>"testRegistration", 'password_hash'=>'testHeslo']);
    }

    private static function deleteRecords(Container $container) {
        /** @var RegistrationDao $registrationDao */
        $registrationDao = $container->get(RegistrationDao::class);
        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);

        $registrationDao->delete(['login_name_fk'=>"testRegistration"]);
        $loginDao->delete(['login_name'=>"testRegistration"]);
    }

    protected function setUp(): void {
        $environment = $this->mock(
                ['HTTP_USER_AGENT'=>'AppRunner']

                );
//        $this->app = (new WebAppFactory())->createFromEnvironment($environment);

        $this->container =
            (new LoginContainerConfigurator())->configure(
                (new DbOldContainerConfigurator())->configure(
                    (new Container(
//                            $this->getApp()->getAppContainer()       bez app kontejneru
                        )
                    )
                )
            );

        $this->registrationRepo = $this->container->get(RegistrationRepo::class);
    }

    protected function tearDown(): void {
        $this->registrationRepo->flush();
    }

    public static function tearDownAfterClass(): void {
        $container =
            (new LoginContainerConfigurator())->configure(
                (new DbOldContainerConfigurator())->configure(
                    (new Container(
//                            $this->getApp()->getAppContainer()       bez app kontejneru
                        )
                    )
                )
            );

        self::deleteRecords($container);
    }

    public function testSetUp() {
        $this->assertInstanceOf(RegistrationRepo::class, $this->registrationRepo);
    }

    public function testGetNonExisted() {
        $registration = $this->registrationRepo->get("QWER45T6U7I89OPOLKJHGFD");
        $this->assertNull($registration);
    }

    public function testGetAndRemoveAfterSetup() {
        $registration = $this->registrationRepo->get("testRegistration");
        $this->assertInstanceOf(Registration::class, $registration);

        $registration = $this->registrationRepo->remove($registration);
        $this->assertNull($registration);
    }

    public function testGetAfterRemove() {
        $loginAgg = $this->registrationRepo->get("testRegistration");
        $this->assertNull($loginAgg);
    }

    public function testAdd() {
        $registration = new Registration();
        $registration->setLoginNameFk("testRegistration");
        $registration->setPasswordHash("testHeslo");
        $registration->setEmail("test.email@mejl.cz");
        $registration->setInfo("testInfo jhedhgdjgjwdgdj  io ipfpf ");
        $this->registrationRepo->add($registration);
        $this->assertTrue($registration->isLocked(), 'Registration není zamčena po add.');
    }

    public function testGetAfterAdd() {
        $registration = $this->registrationRepo->get("testRegistration");
        $this->assertInstanceOf(Registration::class, $registration);
        $this->assertTrue(is_string($registration->getUid()));
        $this->assertInstanceOf(\DateTime::class, $registration->getCreated());
        self::$uidForEmail = $registration->getUid();
        $this->assertTrue(is_string(self::$uidForEmail));
    }

    public function testGetByUid() {
        $registration = $this->registrationRepo->getByUid(self::$uidForEmail);
        $this->assertInstanceOf(Registration::class, $registration);
    }

    public function testGetAndRemoveAfterAdd() {
        $registration = $this->registrationRepo->get("testRegistration");
        $this->registrationRepo->remove($registration);
        $this->assertTrue($registration->isLocked(), 'Registration není zamčena po remove.');
    }

    public function testAddAndReread() {
        $registration = new Registration();
        $registration->setLoginNameFk("testRegistration");
        $registration->setPasswordHash("testHeslo");
        $registration->setEmail("test.email@mejl.cz");
        $registration->setInfo("testInfo2 jhedhgdjgjwdgdj  io ipfpf ");
        $this->registrationRepo->add($registration);
        $this->registrationRepo->flush();
        $registration = $this->registrationRepo->get($registration->getLoginNameFk());
        $this->assertTrue($registration->isPersisted(), 'Registration není persistován.');
        $this->assertTrue(is_string($registration->getUid()));
        $this->assertInstanceOf(\DateTime::class, $registration->getCreated());
    }

}
