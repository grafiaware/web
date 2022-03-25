<?php
declare(strict_types=1);

namespace Test\Integration\Repository;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Test\AppRunner\AppRunner;
use Pes\Container\Container;

//use Container\DbUpgradeContainerConfigurator;
use Container\HierarchyContainerConfigurator;
//use Test\Integration\Model\Container\TestModelContainerConfigurator;

use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Dao\LoginDao;

use Events\Model\Entity\VisitorProfile;
use Events\Model\Dao\VisitorProfileDao;
use Events\Model\Repository\VisitorProfileRepo;

use Model\RowData\RowData;

use Model\Dao\Exception\DaoKeyVerificationFailedException;


/**
 *
 * @author pes2704
 */
class VisitorProfileRepositoryTest extends AppRunner {


    private $container;

    /**
     *
     * @var VisitorProfileRepo
     */
    private $visitorProfileRepo;

    private static $loginNamePrefix;
    private static $loginName;
    private static $loginNameAdded;
    private static $visitorProfileAdded;

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        $container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        // mazání - zde jen pro případ, že minulý test nebyl dokončen
        self::deleteRecords($container);

        // toto je příprava testu
        self::$loginNamePrefix = "testVisitorProfile";

        // visitor profile record pro testy get
        self::$loginName = self::insertLoginRecord($container);
        self::insertRecord($container, self::$loginName);
    }

    private static function insertRecord(Container $container, $loginName) {
        /** @var VisitorProfileDao $visitorProfileDao */
        $visitorProfileDao = $container->get(VisitorProfileDao::class);

        $rowData = new RowData([
            'login_login_name' => $loginName,
            'name' => "Name" . (string) (1000+random_int(0, 999)),
            'surname' => "Name" . (string) (1000+random_int(0, 999)),
            'email' => "mail" . (string) (1000+random_int(0, 999)).'@ztrewzqtrwzeq.cc',
            'phone' => (string) (1000+random_int(0, 999)).' '.(string) (1000+random_int(0, 999)).' '.(string) (1000+random_int(0, 999))
        ]);
        $visitorProfileDao->insert($rowData);
    }

    private static function insertLoginRecord(Container $container) {
        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        // prefix + uniquid - bez zamykání db
        do {
            $loginName = self::$loginNamePrefix."_".uniqid();
            $login = $loginDao->get($loginName);
        } while ($login);

        $rowData = new RowData([
            'login_name' => $loginName,
        ]);
        $loginDao->insertWithKeyVerification($rowData);

        return $loginName;
    }

    private static function deleteRecords(Container $container) {
        /** @var VisitorProfileDao $visitorDataDao */
        $visitorDataDao = $container->get(VisitorProfileDao::class);
        $prefix = self::$loginNamePrefix;
        $rows = $visitorDataDao->find("login_login_name LIKE '$prefix%'", []);
        foreach($rows as $row) {
            $visitorDataDao->delete($row);
        }
    }

    protected function setUp(): void {
        $this->container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        $this->visitorProfileRepo = $this->container->get(VisitorProfileRepo::class);
    }

    protected function tearDown(): void {
        $this->visitorProfileRepo->flush();
    }

    public static function tearDownAfterClass(): void {
        $container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );

        self::deleteRecords($container);
    }

    public function testSetUp() {
        $this->assertInstanceOf(VisitorProfileRepo::class, $this->visitorProfileRepo);
    }

    public function testGetNonExisted() {
        $visitorProfile = $this->visitorProfileRepo->get('dlksdhfweuih');
        $this->assertNull($visitorProfile);
    }

    public function testGetAfterSetup() {
        $visitorProfile = $this->visitorProfileRepo->get(self::$loginName);    // !!!! jenom po insertu v setUp - hodnotu vrací dao
        $this->assertInstanceOf(VisitorProfile::class, $visitorProfile);
    }

    public function testAdd() {
        self::$loginNameAdded = self::insertLoginRecord($this->container);

        $visitorProfile = new VisitorProfile();
        $visitorProfile->setLoginLoginName(self::$loginNameAdded);
        $visitorProfile->setPrefix("Bleble.");
        $visitorProfile->setName("Jméno");
        $visitorProfile->setSurname("Příjmení");
        $visitorProfile->setPostfix("Blabla.");
        $visitorProfile->setEmail("qwertzuio@twrqew.qt");
        $visitorProfile->setPhone('+999 888 777 666');
        $visitorProfile->setCvEducationText("Školy mám.");
        $visitorProfile->setCvSkillsText("Umím fčecko nejlýp.");

        $this->visitorProfileRepo->add($visitorProfile);
        $this->assertTrue($visitorProfile->isLocked());
        self::$visitorProfileAdded = $visitorProfile;

//        $cvFinfo = new \SplFileInfo($cvFilepathName);
//        $file = $cvFinfo->openFile();
    }

    public function testGetAfterAdd() {
        $visitorProfile = $this->visitorProfileRepo->get(self::$loginNameAdded);
        $this->assertInstanceOf(VisitorProfile::class, $visitorProfile);
    }

    public function testAddAndReread() {
        $loginName = self::insertLoginRecord($this->container);

        $visitorProfile = new VisitorProfile();
        $visitorProfile->setLoginLoginName($loginName);
        $visitorProfile->setPrefix("Trdlo.");
        $visitorProfile->setName("Julián");
        $visitorProfile->setSurname("Bublifuk");
        $this->visitorProfileRepo->add($visitorProfile);
        $this->visitorProfileRepo->flush();
        $visitorProfileRereaded = $this->visitorProfileRepo->get($loginName);
        $this->assertInstanceOf(VisitorProfile::class, $visitorProfileRereaded);
        $this->assertTrue($visitorProfileRereaded->isPersisted());
    }

    public function testFindAll() {
        $visitorProfile = $this->visitorProfileRepo->findAll();
        $this->assertTrue(is_array($visitorProfile));
    }

    public function testFind() {
        $prefix = self::$loginNamePrefix;
        $visitorsProfile = $this->visitorProfileRepo->find("login_login_name LIKE '$prefix%'", []);
        $this->assertTrue(is_array($visitorsProfile));
    }

    public function testRemove() {
        $visitorProfile = $this->visitorProfileRepo->get(self::$loginName);    // !!!! jenom po insertu v setUp - hodnotu vrací dao
        $this->assertInstanceOf(VisitorProfile::class, $visitorProfile);
        $this->visitorProfileRepo->remove($visitorProfile);
        $this->assertFalse($visitorProfile->isPersisted());
        $this->assertTrue($visitorProfile->isLocked());   // maže až při flush
        $this->visitorProfileRepo->flush();
        $this->assertFalse($visitorProfile->isLocked());
        // pokus o čtení
        $visitorProfile = $this->visitorProfileRepo->get(self::$loginName);
        $this->assertNull($visitorProfile);
    }

}
