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

use Application\WebAppFactory;

use Pes\Container\Container;

use Container\LoginContainerConfigurator;
use Container\DbOldContainerConfigurator;


use Model\Dao\LoginDao;
use Model\Dao\RegistrationDao;
use Model\Repository\LoginAggregateRegistrationRepo;

use Model\Entity\LoginAggregateRegistration;
use Model\Entity\Registration;

use Model\Repository\Exception\UnableAddEntityException;

/**
 *
 * @author pes2704
 */
class LoginAggregateRegistrationRepositoryTest extends TestCase {

    private static $inputStream;

    private $app;
    private $container;

    /**
     *
     * @var LoginAggregateRegistrationRepo
     */
    private $loginAggRegRepo;

    private $emailedUid;

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
    private static function deleteRecords(Container $container) {
        /** @var RegistrationDao $registrationDao */
        $registrationDao = $container->get(RegistrationDao::class);
        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        $registrationDao->delete(['login_name_fk'=>"testLoginAggregateRegistration1"]);
        $loginDao->delete(['login_name'=>"testLoginAggregateRegistration1"]);
        $registrationDao->delete(['login_name_fk'=>"testLoginAggregateRegistration2"]);
        $loginDao->delete(['login_name'=>"testLoginAggregateRegistration2"]);
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
            (new LoginContainerConfigurator())->configure(
                (new DbOldContainerConfigurator())->configure(
                    (new Container(
//                            $this->getApp()->getAppContainer()       bez app kontejneru
                        )
                    )
                )
            );
        // mazání - zde jen pro případ, že minulý test nebyl dokončen
        self::deleteRecords($container);

        /** @var RegistrationDao $registrationDao */
        $registrationDao = $container->get(RegistrationDao::class);
        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        $loginDao->insertWithKeyVerification(['login_name'=>"testLoginAggregateRegistration1"]);
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

        $this->loginAggRegRepo = $this->container->get(LoginAggregateRegistrationRepo::class);
    }

    protected function tearDown(): void {
        $this->loginAggRegRepo->flush();
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
        $this->assertInstanceOf(LoginAggregateRegistrationRepo::class, $this->loginAggRegRepo);
    }

    public function testGetNonExisted() {
        $loginAgg = $this->loginAggRegRepo->get("QWER45T6U7I89OPOLKJHGFD");
        $this->assertNull($loginAgg);
    }

    public function testGetAndRemoveAfterSetup() {
        $loginAgg = $this->loginAggRegRepo->get("testLoginAggregateRegistration1");
        $this->assertInstanceOf(LoginAggregateRegistration::class, $loginAgg);
        $this->loginAggRegRepo->remove($loginAgg);
    }

    public function testGetAfterRemove() {
        $loginAgg = $this->loginAggRegRepo->get("testLoginAggregateRegistration1");
        $this->assertNull($loginAgg);
    }

    ###### complete aggregate ################

    public function testAddCompleteAndReread() {
        /** @var LoginAggregateRegistration $loginAgg */
        $loginAgg = new LoginAggregateRegistration();
        $loginAgg->setLoginName("testLoginAggregateRegistration1");
        $registration = new Registration();
        $registration->setLoginNameFk("testLoginAggregateRegistration1");
        $registration->setPasswordHash("testHeslo");
        $registration->setEmail("test.email@mejl.cz");
        $loginAgg->setRegistration($registration);
        $this->loginAggRegRepo->add($loginAgg);
        $this->loginAggRegRepo->flush();

        $this->assertTrue($loginAgg->isPersisted(), 'LoginAggregate není persistován.');
        $this->assertTrue($registration->isPersisted(), 'Registration není persistován.');
        // nověnačtený agregát (po flush)
        $loginAgg = $this->loginAggRegRepo->get($loginAgg->getLoginName());
        $this->assertInstanceOf(LoginAggregateRegistration::class, $loginAgg);
        $this->assertTrue($loginAgg->isPersisted(), 'Login není persistován.');
        $this->assertInstanceOf(Registration::class, $loginAgg->getRegistration());
        $this->assertTrue($loginAgg->getRegistration()->isPersisted(), 'Vnořený Registration není persistován.');
        $this->assertTrue(is_string($loginAgg->getRegistration()->getUid()));
        $this->assertInstanceOf(\DateTime::class, $loginAgg->getRegistration()->getCreated());
    }

    public function testGetAfterAddComplete() {
        $loginAgg = $this->loginAggRegRepo->get("testLoginAggregateRegistration1");
        $this->assertInstanceOf(LoginAggregateRegistration::class, $loginAgg);
    }

    ###### uncomplete aggregate ################

    public function testAddUncomplete() {
        $loginAgg = new LoginAggregateRegistration();
        $loginAgg->setLoginName("testLoginAggregateRegistration2");
        $this->loginAggRegRepo->add($loginAgg);
        $this->assertTrue($loginAgg->isPersisted());
    }

//    public function testAddDuplicateAfterAdd() {
//        $loginAgg = new LoginAggregateRegistration();
//
//        $this->expectException(UnableAddEntityException::class);
//        $this->loginAggCredRepo->add($loginAgg);
//        $this->loginAggCredRepo->flush();
//    }

    public function testGetAfterAddUncomplete() {
        $loginAgg = $this->loginAggRegRepo->get("testLoginAggregateRegistration2");
        $this->assertInstanceOf(LoginAggregateRegistration::class, $loginAgg);
    }

    public function testSetNewRegistrationUncomplete() {
        /** @var LoginAggregateRegistration $loginAgg */
        $loginAgg = $this->loginAggRegRepo->get("testLoginAggregateRegistration2");
        $registration = new Registration();
        $registration->setLoginNameFk($loginAgg->getLoginName());
        $registration->setPasswordHash("testHeslo");
        $registration->setEmail("test.email@mejl.cz");
        $loginAgg->setRegistration($registration);
        // nesmyslný assert - jen proto, aby test nebyl risky. Potřebné operace proběhnou až při volání flush (v tearDown())
        $this->assertInstanceOf(Registration::class, $loginAgg->getRegistration());
    }

    public function testGetAfterSetNewRegistrationUncomplete() {
        /** @var LoginAggregateRegistration $loginAgg */
        $loginAgg = $this->loginAggRegRepo->get("testLoginAggregateRegistration2");
        $this->assertInstanceOf(LoginAggregateRegistration::class, $loginAgg);
        $this->assertInstanceOf(Registration::class, $loginAgg->getRegistration());
        $this->emailedUid = $loginAgg->getRegistration()->getUid();
        $this->assertTrue(is_string($this->emailedUid));
    }
}
