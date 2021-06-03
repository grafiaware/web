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

use Model\Dao\Exception\DaoKeyVerificationFailedException;

use Auth\Model\Dao\LoginDao;
use Auth\Model\Dao\CredentialsDao;
use Auth\Model\Repository\LoginAggregateCredentialsRepo;

use Auth\Model\Entity\LoginAggregateCredentials;
use Auth\Model\Entity\Credentials;

/**
 * Description of MenuItemPaperRepositoryTest
 *
 * @author pes2704
 */
class LoginAggregateCredentialsRepositoryTest extends TestCase {

    private static $inputStream;

    private $app;
    private $container;

    /**
     *
     * @var LoginAggregateCredentialsRepo
     */
    private $loginAggCredRepo;


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
            (new LoginContainerConfigurator())->configure(
                (new DbOldContainerConfigurator())->configure(
                    (new Container(
//                            $this->getApp()->getAppContainer()       bez app kontejneru
                        )
                    )
                )
            );

        try {
            /** @var LoginDao $loginDao */
            $loginDao = $container->get(LoginDao::class);
            $loginDao->insertWithKeyVerification(['login_name'=>"testLoginAggregate1"]);
        } catch (DaoKeyVerificationFailedException $keyExistsExc) {

        }

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

        $this->loginAggCredRepo = $this->container->get(LoginAggregateCredentialsRepo::class);
    }

    protected function tearDown(): void {
        $this->loginAggCredRepo->flush();
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
        /** @var CredentialsDao $credentialsDao */
        $credentialsDao = $container->get(CredentialsDao::class);
        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        $credentialsDao->delete(['login_name_fk'=>"testLoginAggregate1"]);
        $loginDao->delete(['login_name'=>"testLoginAggregate1"]);
        $credentialsDao->delete(['login_name_fk'=>"testLoginAggregate2"]);
        $loginDao->delete(['login_name'=>"testLoginAggregate2"]);
    }

    public function testSetUp() {
        $this->assertInstanceOf(LoginAggregateCredentialsRepo::class, $this->loginAggCredRepo);
    }

    public function testGet() {
        $loginAgg = $this->loginAggCredRepo->get("QWER45T6U7I89OPOLKJHGFD");
        $this->assertNull($loginAgg);
        $loginAgg = $this->loginAggCredRepo->get("pes2704");
        $this->assertInstanceOf(LoginAggregateCredentials::class, $loginAgg);
    }

    ###### complete aggregate ################

    public function testAddComplete() {
        $loginAgg = new LoginAggregateCredentials();
        $loginAgg->setLoginName("testLoginAggregate1");
        $credentials = new Credentials();
        $credentials->setLoginNameFk("testLoginAggregate1");
        $credentials->setPasswordHash("testHeslo");
        $loginAgg->setCredentials($credentials);
        $this->loginAggCredRepo->add($loginAgg);
        $this->loginAggCredRepo->flush();
        $this->assertTrue($loginAgg->isPersisted(), 'LoginAggregate není persistován.');
        $this->assertTrue($credentials->isPersisted(), 'Credentials není persistován.');
    }

    public function testGetAfterAddComplete() {
        $loginAgg = $this->loginAggCredRepo->get("testLoginAggregate1");
        $this->assertInstanceOf(LoginAggregateCredentials::class, $loginAgg);
    }

    ###### uncomplete aggregate ################

    public function testAddUncomplete() {
        $loginAgg = new LoginAggregateCredentials();
        $loginAgg->setLoginName("testLoginAggregate2");
        $this->loginAggCredRepo->add($loginAgg);
        $this->assertTrue($loginAgg->isPersisted());
    }

//    public function testAddDuplicateAfterAdd() {
//        $loginAgg = new LoginAggregateCredentials();
//
//        $this->expectException(UnableAddEntityException::class);
//        $this->loginAggCredRepo->add($loginAgg);
//        $this->loginAggCredRepo->flush();
//    }

    public function testGetAfterAddUncomplete() {
        $loginAgg = $this->loginAggCredRepo->get("testLoginAggregate2");
        $this->assertInstanceOf(LoginAggregateCredentials::class, $loginAgg);
    }

    public function testSetNewCredentialsUncomplete() {
        /** @var LoginAggregateCredentials $loginAgg */
        $loginAgg = $this->loginAggCredRepo->get("testLoginAggregate2");
        $credentials = new Credentials();
        $credentials->setLoginNameFk($loginAgg->getLoginName());
        $credentials->setPasswordHash("testHeslo");
        $loginAgg->setCredentials($credentials);
        // nesmyslný assert - jen proto, aby test nebyl risky. Potřebné operace proběhnou až při volání flush (v tearDown())
        $this->assertInstanceOf(Credentials::class, $loginAgg->getCredentials());
    }

    public function testGetAfterSetNewCredentialsUncomplete() {
        /** @var LoginAggregateCredentials $loginAgg */
        $loginAgg = $this->loginAggCredRepo->get("testLoginAggregate2");
        $this->assertInstanceOf(LoginAggregateCredentials::class, $loginAgg);
        $this->assertInstanceOf(Credentials::class, $loginAgg->getCredentials());

    }
}
