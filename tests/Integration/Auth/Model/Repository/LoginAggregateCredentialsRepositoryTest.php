<?php
declare(strict_types=1);
namespace Test\Integration\Repository;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Test\AppRunner\AppRunner;

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

use Model\RowData\RowData;

/**
 * Description of MenuItemPaperRepositoryTest
 *
 * @author pes2704
 */
class LoginAggregateCredentialsRepositoryTest extends AppRunner {

    private $container;

    /**
     *
     * @var LoginAggregateCredentialsRepo
     */
    private $loginAggCredRepo;

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();

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
            $loginDao->insertWithKeyVerification(new RowData(['login_name'=>"testLoginAggregate1"]));
        } catch (DaoKeyVerificationFailedException $keyExistsExc) {

        }

    }

    protected function setUp(): void {

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

    }

    private static function deleteRecords(Container $container) {
        $container =
            (new LoginContainerConfigurator())->configure(
                (new DbOldContainerConfigurator())->configure(
                    (new Container())
                )
            );
        /** @var CredentialsDao $credentialsDao */
        $credentialsDao = $container->get(CredentialsDao::class);
        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        $credRowData = $credentialsDao->get("testLoginAggregate1");
        $credentialsDao->delete($credRowData);
        $loginDao->delete($rowData);
        $loginDao->delete(['login_name'=>"testLoginAggregate2"]);


        /** @var VisitorProfileDao $visitorDataDao */
        $visitorDataDao = $container->get(VisitorProfileDao::class);
        $prefix = self::$loginNamePrefix;
        $rows = $visitorDataDao->find("login_login_name LIKE '$prefix%'", []);
        foreach($rows as $row) {
            $visitorDataDao->delete($row);
        }
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
