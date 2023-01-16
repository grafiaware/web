<?php
declare(strict_types=1);
namespace Test\Integration\Auth\Model\Repository;

use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Container\AuthContainerConfigurator;
use Container\DbOldContainerConfigurator;

use Auth\Model\Entity\Registration;
use Auth\Model\Dao\LoginDao;
use Auth\Model\Dao\RegistrationDao;
use Auth\Model\Repository\RegistrationRepo;
use Model\RowData\RowData;

/**
 *
 * @author pes2704
 */
class RegistrationRepositoryTest extends AppRunner {

    const PREFIX = "testRegistration";
    const LOGIN_NAME1 = self::PREFIX."1";

    private $container;

    /**
     *
     * @var RegistrationRepo
     */
    private $registrationRepo;

    static $uidForEmail;

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

        // toto je příprava testu
        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        /** @var RegistrationDao $registrationDao */
        $registrationDao = $container->get(RegistrationDao::class);
        $rowData = new RowData();
        $rowData->import(['login_name'=>self::LOGIN_NAME1]);  //import -> hodnoty jsou changed
        $loginDao->insert($rowData);
        $rowData = new RowData();
        $rowData->import(['login_name_fk'=>self::LOGIN_NAME1, 'password_hash'=>'testHeslo']);  //import -> hodnoty jsou changed
        $registrationDao->insert($rowData);
    }

    private static function deleteRecords(Container $container) {
        /** @var RegistrationDao $registrationDao */
        $registrationDao = $container->get(RegistrationDao::class);
        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);

        $rowData = new RowData(['login_name_fk'=>self::LOGIN_NAME1]);  // do konstruktoru -> hodnoty jsou "staré"
        $registrationDao->delete($rowData);
        $rowData = new RowData(['login_name'=>self::LOGIN_NAME1]);
        $loginDao->delete($rowData);
    }

    protected function setUp(): void {

        $container =
            (new AuthContainerConfigurator())->configure(
                (new DbOldContainerConfigurator())->configure(
                    (new Container(
//                            $this->getApp()->getAppContainer()       bez app kontejneru
                        )
                    )
                )
            );

        $this->registrationRepo = $container->get(RegistrationRepo::class);
    }

    protected function tearDown(): void {
        $this->registrationRepo->flush();
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
        $this->assertInstanceOf(RegistrationRepo::class, $this->registrationRepo);
    }

    public function testGetNonExisted() {
        $registration = $this->registrationRepo->get("QWER45T6U7I89OPOLKJHGFD");
        $this->assertNull($registration);
    }

    public function testGetAndRemoveAfterSetup() {
        $registration = $this->registrationRepo->get(self::LOGIN_NAME1);
        $this->assertInstanceOf(Registration::class, $registration);

        $registration = $this->registrationRepo->remove($registration);
        $this->assertNull($registration);
    }

    public function testGetAfterRemove() {
        $loginAgg = $this->registrationRepo->get(self::LOGIN_NAME1);
        $this->assertNull($loginAgg);
    }

    public function testAdd() {
        $registration = new Registration();
        $registration->setLoginNameFk(self::LOGIN_NAME1);
        $registration->setPasswordHash("testHeslo");
        $registration->setEmail("test.email@mejl.cz");
        $registration->setInfo("testInfo jhedhgdjgjwdgdj  io ipfpf ");
        $this->registrationRepo->add($registration);
        $this->assertTrue($registration->isPersisted(), 'Registration není persisted po add. Pravděpodobně RegistrationDao není');
    }

    public function testGetAfterAdd() {
        $registration = $this->registrationRepo->get(self::LOGIN_NAME1);
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
        $registration = $this->registrationRepo->get(self::LOGIN_NAME1);
        $this->registrationRepo->remove($registration);
        $this->assertTrue($registration->isLocked(), 'Registration není zamčena po remove.');
    }

    public function testAddAndReread() {
        $registration = new Registration();
        $registration->setLoginNameFk(self::LOGIN_NAME1);
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
