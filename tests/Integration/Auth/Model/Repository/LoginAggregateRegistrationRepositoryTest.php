<?php
declare(strict_types=1);
namespace Test\Integration\Auth\Model\Repository;

use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Container\AuthContainerConfigurator;
use Container\DbOldContainerConfigurator;

use Model\RowData\RowData;

use Auth\Model\Dao\LoginDao;
use Auth\Model\Dao\RegistrationDao;
use Auth\Model\Repository\LoginAggregateRegistrationRepo;

use Auth\Model\Entity\LoginAggregateRegistration;
use Auth\Model\Entity\Registration;

/**
 *
 * @author pes2704
 */
class LoginAggregateRegistrationRepositoryTest extends AppRunner {

    const PREFIX = "testLoginAggregateRegistration";
    const LOGIN_NAME1 = self::PREFIX."1";
    const LOGIN_NAME2 = self::PREFIX."2";

    private $container;

    /**
     *
     * @var LoginAggregateRegistrationRepo
     */
    private $loginAggRegRepo;

    private $emailedUid;


    private static function deleteRecords(Container $container) {
        /** @var RegistrationDao $registrationDao */
        $registrationDao = $container->get(RegistrationDao::class);
        $rows = $registrationDao->find("login_name_fk LIKE :login_name_like", ['login_name_like'=> self::PREFIX."%"]);
        foreach ($rows as $row) {
            $registrationDao->delete($row);
        }

        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        $rows = $loginDao->find("login_name LIKE :login_name_like", ['login_name_like'=> self::PREFIX."%"]);
        foreach ($rows as $row) {
            $loginDao->delete($row);
        }
    }

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
        $rowData->import(['login_name'=> self::LOGIN_NAME1]);
        $loginDao->insert($rowData);

        /** @var RegistrationDao $registrationDao */
        $registrationDao = $container->get(RegistrationDao::class);
        $rowData = new RowData();
        $rowData->import(['login_name_fk'=> self::LOGIN_NAME1, 'password_hash'=>"testHeslosetUpBeforeClass"]);
        $registrationDao->insert($rowData);
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

        $this->loginAggRegRepo = $this->container->get(LoginAggregateRegistrationRepo::class);
    }

    protected function tearDown(): void {
        $this->loginAggRegRepo->flush();
    }

    public static function tearDownAfterClass(): void {
        $container =
            (new AuthContainerConfigurator())->configure(
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
        $loginAgg = $this->loginAggRegRepo->get(self::LOGIN_NAME1);
        $this->assertInstanceOf(LoginAggregateRegistration::class, $loginAgg);
        /** @var LoginAggregateRegistration $loginAgg */
        $this->assertInstanceOf(Registration::class, $loginAgg->getRegistration(), "Přečtený LoginAggregateRegistration nemá Registration entity");
        $this->loginAggRegRepo->remove($loginAgg);  // poznámka: entity přestane být persisted a lock až ve flush()
    }

    public function testGetAfterRemove() {
        $loginAgg = $this->loginAggRegRepo->get(self::LOGIN_NAME1);
        $this->assertNull($loginAgg);
    }

    ###### aggregate ################

    public function testAddCompleteAndReread() {
        /** @var LoginAggregateRegistration $loginAgg */
        $loginAgg = new LoginAggregateRegistration();
        $loginAgg->setLoginName(self::LOGIN_NAME1);
        $registration = new Registration();
        $registration->setLoginNameFk(self::LOGIN_NAME1);
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
        $loginAgg = $this->loginAggRegRepo->get(self::LOGIN_NAME1);
        $this->assertInstanceOf(LoginAggregateRegistration::class, $loginAgg);
    }

    ###### entita bez (potomkonské) entity ################

    /**
     * LoginAggregateRegistration bez Registration
     */
    public function testAddUncomplete() {
        $loginAgg = new LoginAggregateRegistration();
        $loginAgg->setLoginName(self::LOGIN_NAME2);
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
        $loginAgg = $this->loginAggRegRepo->get(self::LOGIN_NAME2);
        $this->assertInstanceOf(LoginAggregateRegistration::class, $loginAgg);
    }

    /**
     * Přidá novou Registration do přečtené nekompletní LoginAggregateRegistration
     */
    public function testSetNewRegistrationToUncomplete() {
        /** @var LoginAggregateRegistration $loginAgg */
        $loginAgg = $this->loginAggRegRepo->get(self::LOGIN_NAME2);
        $registration = new Registration();
        $registration->setLoginNameFk($loginAgg->getLoginName());
        $registration->setPasswordHash("testHeslo");
        $registration->setEmail("test.email@mejl.cz");
        $loginAgg->setRegistration($registration);
        // nesmyslný assert - jen proto, aby test nebyl risky. Potřebné operace proběhnou až při volání flush (v tearDown())
        $this->assertInstanceOf(Registration::class, $loginAgg->getRegistration());
    }

    public function testGetAfterSetNewRegistrationToUncomplete() {
        /** @var LoginAggregateRegistration $loginAgg */
        $loginAgg = $this->loginAggRegRepo->get(self::LOGIN_NAME2);
        $this->assertInstanceOf(LoginAggregateRegistration::class, $loginAgg);
        $this->assertInstanceOf(Registration::class, $loginAgg->getRegistration());
        $this->emailedUid = $loginAgg->getRegistration()->getUid();
        $this->assertTrue(is_string($this->emailedUid));
    }
}
