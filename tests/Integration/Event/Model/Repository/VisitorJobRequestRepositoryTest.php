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
use Events\Model\Dao\JobDao;

use Events\Model\Entity\VisitorJobRequest;
use Events\Model\Dao\VisitorJobRequestDao;
use Events\Model\Repository\VisitorJobRequestRepo;

use Model\RowData\RowData;

use Model\Dao\Exception\DaoKeyVerificationFailedException;


/**
 *
 * @author pes2704
 */
class VisitorJobRequestRepositoryTest extends AppRunner {


    private $container;

    /**
     *
     * @var VisitorJobRequestRepo
     */
    private $visitorJobRequestRepo;

    private static $loginNamePrefix;
    private static $jobPrefix;

    private static $loginName;
    private static $loginNameAdded;
    private static $visitorJobRequestAdded;

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        $container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        // mazání - zde jen pro případ, že minulý test nebyl dokončen
        self::deleteRecords($container);

        // toto je příprava testu
        self::$loginNamePrefix = "testVisitorJobRequest";

        // visitor profile record pro testy get
        self::$loginName = self::insertLoginRecord($container);
        self::insertRecord($container, self::$loginName);
    }

    private static function insertRecord(Container $container, $loginName) {
        /** @var VisitorJobRequestDao $visitorJobRequestDao */
        $visitorJobRequestDao = $container->get(VisitorJobRequestDao::class);

        $rowData = new RowData();
        $rowData->import([
            'login_login_name' => $loginName,
            'name' => "Name" . (string) (1000+random_int(0, 999)),
            'surname' => "Name" . (string) (1000+random_int(0, 999)),
            'email' => "mail" . (string) (1000+random_int(0, 999)).'@ztrewzqtrwzeq.cc',
            'phone' => (string) (1000+random_int(0, 999)).' '.(string) (1000+random_int(0, 999)).' '.(string) (1000+random_int(0, 999))
        ]);
        $visitorJobRequestDao->insert($rowData);
    }

    private static function insertLoginRecord(Container $container) {
        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        // prefix + uniquid - bez zamykání db
        do {
            $loginName = self::$loginNamePrefix."_".uniqid();
            $login = $loginDao->get(['login_name' => $loginName]);
        } while ($login);

        $rowData = new RowData();
        $rowData->import([
            'login_name' => $loginName,
        ]);
        $loginDao->insert($rowData);

        return $loginName;
    }
    private static function insertJobRecord(Container $container) {
        /** @var JobDao $jobDao */
        $jobDao = $container->get(JobDao::class);
        // prefix + uniquid - bez zamykání db
        do {
            $loginName = self::$loginNamePrefix."_".uniqid();
            $login = $jobDao->get(['login_name' => $loginName]);
        } while ($login);

        $rowData = new RowData();
        $rowData->import([
            'login_name' => $loginName,
        ]);
        $jobDao->insert($rowData);

        return $loginName;
    }

    private static function deleteRecords(Container $container) {
        /** @var VisitorJobRequestDao $visitorJobRequestDao */
        $visitorJobRequestDao = $container->get(VisitorJobRequestDao::class);
        $prefix = self::$loginNamePrefix;
        $rows = $visitorJobRequestDao->find("login_login_name LIKE '$prefix%'", []);
        foreach($rows as $row) {
            $visitorJobRequestDao->delete($row);
        }
    }

    protected function setUp(): void {
        $this->container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        $this->visitorJobRequestRepo = $this->container->get(VisitorJobRequestRepo::class);
    }

    protected function tearDown(): void {
        $this->visitorJobRequestRepo->flush();
    }

    public static function tearDownAfterClass(): void {
        $container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );

        self::deleteRecords($container);
    }

    public function testSetUp() {
        $this->assertInstanceOf(VisitorJobRequestRepo::class, $this->visitorJobRequestRepo);
    }

    public function testGetNonExisted() {
        $visitorJobRequest = $this->visitorJobRequestRepo->get('dlksdhfweuih');
        $this->assertNull($visitorJobRequest);
    }

    public function testGetAfterSetup() {
        $visitorJobRequest = $this->visitorJobRequestRepo->get(self::$loginName);    // !!!! jenom po insertu v setUp - hodnotu vrací dao
        $this->assertInstanceOf(VisitorJobRequest::class, $visitorJobRequest);
    }

    public function testAdd() {
        self::$loginNameAdded = self::insertLoginRecord($this->container);

        $visitorJobRequest = new VisitorJobRequest();
        $visitorJobRequest->setLoginLoginName(self::$loginNameAdded);
        $visitorJobRequest->setPrefix("Bleble.");
        $visitorJobRequest->setName("Jméno");
        $visitorJobRequest->setSurname("Příjmení");
        $visitorJobRequest->setPostfix("Blabla.");
        $visitorJobRequest->setEmail("qwertzuio@twrqew.qt");
        $visitorJobRequest->setPhone('+999 888 777 666');
        $visitorJobRequest->setCvEducationText("Školy mám.");
        $visitorJobRequest->setCvSkillsText("Umím fčecko nejlýp.");

        $this->visitorJobRequestRepo->add($visitorJobRequest);
        $this->assertTrue($visitorJobRequest->isLocked());
        self::$visitorJobRequestAdded = $visitorJobRequest;

//        $cvFinfo = new \SplFileInfo($cvFilepathName);
//        $file = $cvFinfo->openFile();
    }

    public function testGetAfterAdd() {
        $visitorJobRequest = $this->visitorJobRequestRepo->get(self::$loginNameAdded);
        $this->assertInstanceOf(VisitorJobRequest::class, $visitorJobRequest);
    }

    public function testAddAndReread() {
        $loginName = self::insertLoginRecord($this->container);

        $visitorJobRequest = new VisitorJobRequest();
        $visitorJobRequest->setLoginLoginName($loginName);
        $visitorJobRequest->setPrefix("Trdlo.");
        $visitorJobRequest->setName("Julián");
        $visitorJobRequest->setSurname("Bublifuk");
        $this->visitorJobRequestRepo->add($visitorJobRequest);
        $this->visitorJobRequestRepo->flush();
        $visitorJobRequestRereaded = $this->visitorJobRequestRepo->get($loginName);
        $this->assertInstanceOf(VisitorJobRequest::class, $visitorJobRequestRereaded);
        $this->assertTrue($visitorJobRequestRereaded->isPersisted());
    }

    public function testFindAll() {
        $visitorJobRequest = $this->visitorJobRequestRepo->findAll();
        $this->assertTrue(is_array($visitorJobRequest));
    }

    public function testFind() {
        $prefix = self::$loginNamePrefix;
        $visitorsJobRequests = $this->visitorJobRequestRepo->find("login_login_name LIKE '$prefix%'", []);
        $this->assertTrue(is_array($visitorsJobRequests));
    }

    public function testRemove() {
        $visitorJobRequest = $this->visitorJobRequestRepo->get(self::$loginName);    // !!!! jenom po insertu v setUp - hodnotu vrací dao
        $this->assertInstanceOf(VisitorJobRequest::class, $visitorJobRequest);
        $this->visitorJobRequestRepo->remove($visitorJobRequest);
        $this->assertFalse($visitorJobRequest->isPersisted());
        $this->assertTrue($visitorJobRequest->isLocked());   // maže až při flush
        $this->visitorJobRequestRepo->flush();
        $this->assertFalse($visitorJobRequest->isLocked());
        // pokus o čtení
        $visitorJobRequest = $this->visitorJobRequestRepo->get(self::$loginName);
        $this->assertNull($visitorJobRequest);
    }

}
