<?php
declare(strict_types=1);
namespace Test\Integration\Auth\Model\Repository;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Test\AppRunner\AppRunner;

use Pes\Http\Factory\EnvironmentFactory;

use Application\WebAppFactory;

use Pes\Container\Container;

use Container\AuthContainerConfigurator;
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

    const PREFIX = "testLoginAggregateCredentials";
    const LOGIN_NAME1 = self::PREFIX."1";
    const LOGIN_NAME2 = self::PREFIX."2";

    private $container;

    /**
     *
     * @var LoginAggregateCredentialsRepo
     */
    private $loginAggCredRepo;

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();

        $container =
            (new AuthContainerConfigurator())->configure(
                (new DbOldContainerConfigurator())->configure(
                    (new Container(
//                            $this->getApp()->getAppContainer()       bez app kontejneru
                        )
                    )
                )
            );

        // mazání - zde jen pro případ, že minulý test nebyl dokončen
        self::deleteRecords($container);

        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
            $rowData = new RowData();
            $rowData->import(['login_name'=>self::LOGIN_NAME1]);
        $loginDao->insert($rowData);

        /** @var RegistrationDao $credentialsDao */
        $credentialsDao = $container->get(CredentialsDao::class);
        $rowData = new RowData();
        $rowData->import(['login_name_fk'=> self::LOGIN_NAME1, 'password_hash'=>"testHeslosetUpBeforeClass", "role_fk"=>"panákZTestu"]);
        $credentialsDao->insert($rowData);
    }

    protected function setUp(): void {

        $this->container =
            (new AuthContainerConfigurator())->configure(
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

        /** @var CredentialsDao $credentialsDao */
        $credentialsDao = $container->get(CredentialsDao::class);
        $rows = $credentialsDao->find("login_name_fk LIKE :login_name_like", ['login_name_like'=> self::PREFIX."%"]);
        foreach ($rows as $row) {
            $credentialsDao->delete($row);
        }

        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        $rows = $loginDao->find("login_name LIKE :login_name_like", ['login_name_like'=> self::PREFIX."%"]);
        foreach ($rows as $row) {
            $loginDao->delete($row);
        }
    }

    public function testSetUp() {
        $this->assertInstanceOf(LoginAggregateCredentialsRepo::class, $this->loginAggCredRepo);
    }

    public function testGetNonExisted() {
        $loginAgg = $this->loginAggCredRepo->get("QWER45T6U7I89OPOLKJHGFD");
        $this->assertNull($loginAgg);
    }

    public function testGetAndRemoveAfterSetup() {
        $loginAgg = $this->loginAggCredRepo->get(self::LOGIN_NAME1);
        $this->assertInstanceOf(LoginAggregateCredentials::class, $loginAgg);
        /** @var LoginAggregateCredentials $loginAgg */
        $this->assertInstanceOf(Credentials::class, $loginAgg->getCredentials(), "Přečtený LoginAggregateRegistration nemá Credentials entity");
        $this->loginAggCredRepo->remove($loginAgg);  // poznámka: entity přestane být persisted a lock až ve flush()
    }

    public function testGetAfterRemove() {
        $loginAgg = $this->loginAggCredRepo->get(self::LOGIN_NAME1);
        $this->assertNull($loginAgg);
    }

    ###### complete aggregate ################

    public function testAddComplete() {
        $loginAgg = new LoginAggregateCredentials();
        $loginAgg->setLoginName(self::LOGIN_NAME1);
        $credentials = new Credentials();
        $credentials->setLoginNameFk(self::LOGIN_NAME1);
        $credentials->setPasswordHash("testHeslo");
        $loginAgg->setCredentials($credentials);
        $this->loginAggCredRepo->add($loginAgg);
        $this->loginAggCredRepo->flush();
        $this->assertTrue($loginAgg->isPersisted(), 'LoginAggregate není persistován.');
        $this->assertTrue($credentials->isPersisted(), 'Credentials není persistován.');
    }

    public function testGetAfterAddComplete() {
        $loginAgg = $this->loginAggCredRepo->get(self::LOGIN_NAME1);
        $this->assertInstanceOf(LoginAggregateCredentials::class, $loginAgg);
    }

    ###### uncomplete aggregate ################

    public function testAddUncomplete() {
        $loginAgg = new LoginAggregateCredentials();
        $loginAgg->setLoginName(self::LOGIN_NAME2);
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
        $loginAgg = $this->loginAggCredRepo->get(self::LOGIN_NAME2);
        $this->assertInstanceOf(LoginAggregateCredentials::class, $loginAgg);
    }

    public function testSetNewCredentialsUncomplete() {
        /** @var LoginAggregateCredentials $loginAgg */
        $loginAgg = $this->loginAggCredRepo->get(self::LOGIN_NAME2);
        $credentials = new Credentials();
        $credentials->setLoginNameFk($loginAgg->getLoginName());
        $credentials->setPasswordHash("testHeslo");
        $loginAgg->setCredentials($credentials);
        // nesmyslný assert - jen proto, aby test nebyl risky. Potřebné operace proběhnou až při volání flush (v tearDown())
        $this->assertInstanceOf(Credentials::class, $loginAgg->getCredentials());
    }

    public function testGetAfterSetNewCredentialsUncomplete() {
        /** @var LoginAggregateCredentials $loginAgg */
        $loginAgg = $this->loginAggCredRepo->get(self::LOGIN_NAME2);
        $this->assertInstanceOf(LoginAggregateCredentials::class, $loginAgg);
        $this->assertInstanceOf(Credentials::class, $loginAgg->getCredentials());

    }
}
