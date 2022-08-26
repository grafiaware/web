<?php
declare(strict_types=1);
namespace Test\Integration\Event\Model\Repository;


use Test\AppRunner\AppRunner;
use Pes\Container\Container;
use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Model\Repository\Exception\OperationWithLockedEntityException;
use Model\RowData\RowData;

use Events\Model\Dao\LoginDao;
use Events\Model\Dao\JobDao;
use Events\Model\Dao\CompanyDao;

use Events\Model\Entity\VisitorJobRequest;
use Events\Model\Dao\VisitorJobRequestDao;
use Events\Model\Repository\VisitorJobRequestRepo;

use Model\Dao\Exception\DaoKeyVerificationFailedException;


/**
 * Description of VisitorJobRequestRepositoryTest
 *
 * @author vlse2610
 */
class VisitorJobRequestRepositoryTest extends AppRunner {
    private $container;

    /**
     *
     * @var VisitorJobRequestRepo
     */
    private $visitorJobRequestRepo;

    private static $loginNameTest = "testVisitJobReqRepo";
    private static $loginNameAdded;
    
    private static $companyId;
    private static $jobId1;

    private static $visitorJobRequestAdded;
    

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        $container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        // mazání - zde jen pro případ, že minulý test nebyl dokončen
        self::deleteRecords( $container );
       
        self::insertRecord( $container );
    }
    
    private static function insertRecord(Container $container ) {        
        // toto je příprava testu

            // login
        self::$loginNameTest = self::insertLoginRecord($container);
            //company
          /** @var CompanyDao $companyDao */
        $companyDao = $container->get(CompanyDao::class);  
        $rowData = new RowData();
        $rowData->import([
            'name' => self::$loginNameTest ]);
        $companyDao->insert($rowData);                
        self::$companyId = $companyDao->lastInsertIdValue();         
            //job
        /** @var JobDao $jobDao */
        $jobDao = $container->get(JobDao::class);  
        $rowData = new RowData();
        $rowData->import([
            'pozadovane_vzdelani_stupen' => 1,  
            'company_id'  =>   self::$companyId,
            'nazev'   => self::$loginNameTest . "1" ]);
        $jobDao->insert($rowData);                
              self::$jobId1 = $jobDao->lastInsertIdValue(); 
//        $rowData = new RowData();
//        $rowData->import([
//            'pozadovane_vzdelani_stupen' => 1,  
//            'company_id'  =>   self::$companyId,
//            'nazev'   =>  self::$jobToTagName . "2" ]);
//        $jobDao->insert($rowData);                
//              self::$jobId2 = $jobDao->lastInsertIdValue(); 
//        $rowData = new RowData();
//        $rowData->import([
//            'pozadovane_vzdelani_stupen' => 1,  
//            'company_id'  =>   self::$companyId,
//            'nazev'   =>  self::$jobToTagName . "3" ]);
//        $jobDao->insert($rowData);                
//                self::$jobId3 = $jobDao->lastInsertIdValue(); 
//        $rowData = new RowData();
//        $rowData->import([
//            'pozadovane_vzdelani_stupen' => 1,  
//            'company_id'  =>   self::$companyId,
//            'nazev'   =>  self::$jobToTagName . "4" ]);
//        $jobDao->insert($rowData);                
//                self::$jobId4 = $jobDao->lastInsertIdValue();         
        
        
        
        //visitorJobRequest        
        /** @var VisitorJobRequestDao $visitorJobRequestDao */
        $visitorJobRequestDao = $container->get(VisitorJobRequestDao::class);
        $rowData = new RowData();
        $rowData->import([
            'login_login_name' => self::$loginNameTest,
            'job_id' => self::$jobId1,
            'position_name' => self::$loginNameTest
        ]);
        $visitorJobRequestDao->insert($rowData);
    }

    private static function insertLoginRecord(Container $container) {
        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        // prefix + uniquid - bez zamykání db
        do {
            $loginName = self::$loginNameTest ."_".uniqid();
            $login = $loginDao->get(['login_name' => $loginName]);
        } while ($login);
        $rowData = new RowData();
        $rowData->import([
            'login_name' => $loginName,
        ]);
        $loginDao->insert($rowData);
        return $loginName;
    }
    
//    private static function insertJobRecord(Container $container) {
//        /** @var JobDao $jobDao */
//        $jobDao = $container->get(JobDao::class);
//        // prefix + uniquid - bez zamykání db
//        do {
//            $loginName = self::$loginNamePrefix."_".uniqid();
//            $login = $jobDao->get(['login_name' => $loginName]);
//        } while ($login);
//
//        $rowData = new RowData();
//        $rowData->import([
//            'login_name' => $loginName,
//        ]);
//        $jobDao->insert($rowData);
//
//        return $loginName;
//    }

    private static function deleteRecords(Container $container) {
        /** @var VisitorJobRequestDao $visitorJobRequestDao */
        $visitorJobRequestDao = $container->get(VisitorJobRequestDao::class);
        $prefix = self::$loginNameTest;
        $rows = $visitorJobRequestDao->find( "login_login_name LIKE '". self::$loginNameTest  . "%'", []);      
        foreach($rows as $row) {
            $visitorJobRequestDao->delete($row);
        }        
    
         /** @var JobDao $jobDao */  //job
        $jobDao = $container->get(JobDao::class);        
        $rows = $jobDao->find(  " nazev LIKE '". self::$loginNameTest. "%'", []);                   
        foreach($rows as $row) {
            $ok = $jobDao->delete($row);
        }               
        
         /** @var CompanyDao $companyDao */ 
        $companyDao = $container->get(CompanyDao::class);        
        $rows = $companyDao->find( " name LIKE '". self::$loginNameTest . "%'", [] );               
        foreach($rows as $row) {
            $ok = $companyDao->delete($row);
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
        $visitorJobRequest = $this->visitorJobRequestRepo->get(self::$loginNameTest);    // !!!! jenom po insertu v setUp - hodnotu vrací dao
        $this->assertInstanceOf(VisitorJobRequest::class, $visitorJobRequest);
    }

    
    public function testAdd() {
        self::$loginNameAdded = self::insertLoginRecord($this->container);

        $visitorJobRequest = new VisitorJobRequest();
        $visitorJobRequest->setLoginLoginName(self::$loginNameAdded);
        $visitorJobRequest->setJobId(self::$jobId1);
        $visitorJobRequest->setPositionName("Krotitel dravé zvěře");
                        
//        $visitorJobRequest->setPrefix("Bleble.");
//        $visitorJobRequest->setName("Jméno");
//        $visitorJobRequest->setSurname("Příjmení");
//        $visitorJobRequest->setPostfix("Blabla.");
//        $visitorJobRequest->setEmail("qwertzuio@twrqew.qt");
//        $visitorJobRequest->setPhone('+999 888 777 666');
//        $visitorJobRequest->setCvEducationText("Školy mám.");
//        $visitorJobRequest->setCvSkillsText("Umím fčecko nejlýp.");

        $this->visitorJobRequestRepo->add($visitorJobRequest);
        $this->assertTrue($visitorJobRequest->isLocked());
        self::$visitorJobRequestAdded = $visitorJobRequest;

    }
    

    public function testGetAfterAdd() {
        $visitorJobRequest = $this->visitorJobRequestRepo->get(self::$loginNameAdded);
        $this->assertInstanceOf(VisitorJobRequest::class, $visitorJobRequest);
    }

    public function testAddAndReread() {
        $loginName = self::insertLoginRecord($this->container);

        $visitorJobRequest = new VisitorJobRequest();
        $visitorJobRequest->setLoginLoginName($loginName);
        $visitorJobRequest->setJobId(self::$jobId1);
        $visitorJobRequest->setPositionName("Duch");
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
//
//    public function testRemove() {
//        $visitorJobRequest = $this->visitorJobRequestRepo->get(self::$loginName);    // !!!! jenom po insertu v setUp - hodnotu vrací dao
//        $this->assertInstanceOf(VisitorJobRequest::class, $visitorJobRequest);
//        $this->visitorJobRequestRepo->remove($visitorJobRequest);
//        $this->assertFalse($visitorJobRequest->isPersisted());
//        $this->assertTrue($visitorJobRequest->isLocked());   // maže až při flush
//        $this->visitorJobRequestRepo->flush();
//        $this->assertFalse($visitorJobRequest->isLocked());
//        // pokus o čtení
//        $visitorJobRequest = $this->visitorJobRequestRepo->get(self::$loginName);
//        $this->assertNull($visitorJobRequest);
//    }

}
