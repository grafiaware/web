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

use Events\Model\Entity\VisitorProfile;
use Events\Model\Dao\VisitorProfileDao;
use Events\Model\Repository\VisitorProfileRepo;



 /**
 *
 * @author vlse2610
 */
class VisitorProfileRepositoryTest extends AppRunner {
    private $container;

    /**
     *
     * @var VisitorProfileRepo
     */
    private $visitorProfileRepo;
    
    
    private static $loginNameTest = "testVisitorProfileRepo";
    private static $loginNameAdded;
    private static $visitorProfileAdded;

    
    
    //private static $positionNameAdded;
    
//    private static $companyId;
//    private static $jobId1;

    

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
            $loginName = self::$loginNameTest . "_"  .uniqid();
            $login = $loginDao->get(['login_name' => $loginName]);
        } while ($login);
        $rowData = new RowData();
        $rowData->import([
            'login_name' => $loginName,
        ]);
        $loginDao->insert($rowData);
        return $loginName;
    }
    

    private static function deleteRecords(Container $container) {
        /** @var VisitorJobRequestDao $visitorJobRequestDao */
        $visitorJobRequestDao = $container->get(VisitorJobRequestDao::class);        
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
                
         /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);        
        $rows = $loginDao->find( " login_name LIKE '". self::$loginNameTest  . "%'", []);      
        foreach($rows as $row) {
            $loginDao->delete($row);
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
        self::$positionNameAdded = "Krotitel dravé zvěře";

        $visitorJobRequest = new VisitorJobRequest();
        $visitorJobRequest->setLoginLoginName( self::$loginNameAdded );
        $visitorJobRequest->setJobId(self::$jobId1);        
        $visitorJobRequest->setPositionName ( self::$positionNameAdded );
                        
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

    }
    

    public function testGetAfterAdd() {
        $visitorJobRequest = $this->visitorJobRequestRepo->get(self::$loginNameAdded);
        $this->assertInstanceOf(VisitorJobRequest::class, $visitorJobRequest);
    }

    public function testAddAndReread() {
        $loginName = self::insertLoginRecord($this->container);
        
        /** @var VisitorJobRequest $visitorJobRequest */        
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
        $visitorJobRequests = $this->visitorJobRequestRepo->findAll();
        $this->assertTrue(is_array($visitorJobRequests));
    }

    
    public function testFind() {
        $name = self::$loginNameTest;
        $visitorsJobRequests = $this->visitorJobRequestRepo->find("login_login_name LIKE '$name%'", []);
        $this->assertTrue(is_array($visitorsJobRequests));
    }
    
    
    public function testFindByLoginNameAndPosition () {
        $visitorsJobRequests = $this->visitorJobRequestRepo->findByLoginNameAndPosition ( self::$loginNameAdded, self::$positionNameAdded );
        $this->assertTrue(is_array( $visitorsJobRequests ));    
    }
    
    
                

    
    public function testRemove_OperationWithLockedEntity() {
        $loginName = self::insertLoginRecord($this->container);
        
        /** @var VisitorJobRequest $visitorJobRequest */        
        $visitorJobRequest = new VisitorJobRequest();
        $visitorJobRequest->setLoginLoginName( $loginName );
        $visitorJobRequest->setJobId(self::$jobId1);
        $visitorJobRequest->setPositionName("DuchLocked");
        
        $this->visitorJobRequestRepo->add($visitorJobRequest);
       
        $this->assertFalse($visitorJobRequest->isPersisted());
        $this->assertTrue($visitorJobRequest->isLocked());

        $this->expectException( OperationWithLockedEntityException::class);
        $this->visitorJobRequestRepo->remove($visitorJobRequest);
   }


   
    public function testRemove() {
        /** @var VisitorJobRequest $visitorJobRequest */
        $visitorJobRequest = $this->visitorJobRequestRepo->get( self::$loginNameAdded );
               
        $this->assertInstanceOf(VisitorJobRequest::class, $visitorJobRequest);
        $this->assertTrue($visitorJobRequest->isPersisted());
        $this->assertFalse($visitorJobRequest->isLocked());
        
        $this-$this->visitorJobRequestRepo->remove($visitorJobRequest);
        
        $this->assertTrue($visitorJobRequest->isPersisted());
        $this->assertTrue($visitorJobRequest->isLocked());   // zatim zamcena entita, maže až při flush
        $this->visitorJobRequestRepo->flush();
        //  uz neni locked
        $this->assertFalse($visitorJobRequest->isLocked());
        
        // pokus o čtení, entita Login.self::$loginKlic  uz  neni
        $visitorJobRequest = $this->visitorJobRequestRepo->get(  self::$loginNameAdded );
        $this->assertNull($visitorJobRequest);
        
    }
    
       
    
    
    
    
    
    
    
    //----------------------------------------
//
//    private static $loginNamePrefix;
//    private static $loginName;
//    private static $loginNameAdded;
//    private static $visitorProfileAdded;
//
//    public static function setUpBeforeClass(): void {
//        self::bootstrapBeforeClass();
//        $container =
//            (new EventsContainerConfigurator())->configure(
//                (new DbEventsContainerConfigurator())->configure(new Container())
//            );
//        // mazání - zde jen pro případ, že minulý test nebyl dokončen
//        self::deleteRecords($container);
//
//        // toto je příprava testu
//        self::$loginNamePrefix = "testVisitorProfile";
//
//        // visitor profile record pro testy get
//        self::$loginName = self::insertLoginRecord($container);
//        self::insertRecord($container, self::$loginName);
//    }
//
//    private static function insertRecord(Container $container, $loginName) {
//        /** @var VisitorProfileDao $visitorProfileDao */
//        $visitorProfileDao = $container->get(VisitorProfileDao::class);
//
//        $rowData = new RowData();
//        $rowData->import([
//            'login_login_name' => $loginName,
//            'name' => "Name" . (string) (1000+random_int(0, 999)),
//            'surname' => "Name" . (string) (1000+random_int(0, 999)),
//            'email' => "mail" . (string) (1000+random_int(0, 999)).'@ztrewzqtrwzeq.cc',
//            'phone' => (string) (1000+random_int(0, 999)).' '.(string) (1000+random_int(0, 999)).' '.(string) (1000+random_int(0, 999))
//        ]);
//        $visitorProfileDao->insert($rowData);
//    }
//
//    private static function insertLoginRecord(Container $container) {
//        /** @var LoginDao $loginDao */
//        $loginDao = $container->get(LoginDao::class);
//        // prefix + uniquid - bez zamykání db
//        do {
//            $loginName = self::$loginNamePrefix."_".uniqid();
//            $login = $loginDao->get(['login_name' => $loginName]);
//        } while ($login);
//
//        $rowData = new RowData();
//        $rowData->import([
//            'login_name' => $loginName,
//        ]);
//        $loginDao->insert($rowData);
//
//        return $loginName;
//    }
//
//    private static function deleteRecords(Container $container) {
//        /** @var VisitorProfileDao $visitorDataDao */
//        $visitorDataDao = $container->get(VisitorProfileDao::class);
//        $prefix = self::$loginNamePrefix;
//        $rows = $visitorDataDao->find("login_login_name LIKE '$prefix%'", []);
//        foreach($rows as $row) {
//            $visitorDataDao->delete($row);
//        }
//    }
//
//    protected function setUp(): void {
//        $this->container =
//            (new EventsContainerConfigurator())->configure(
//                (new DbEventsContainerConfigurator())->configure(new Container())
//            );
//        $this->visitorProfileRepo = $this->container->get(VisitorProfileRepo::class);
//    }
//
//    protected function tearDown(): void {
//        $this->visitorProfileRepo->flush();
//    }
//
//    public static function tearDownAfterClass(): void {
//        $container =
//            (new EventsContainerConfigurator())->configure(
//                (new DbEventsContainerConfigurator())->configure(new Container())
//            );
//
//        self::deleteRecords($container);
//    }
//
//    public function testSetUp() {
//        $this->assertInstanceOf(VisitorProfileRepo::class, $this->visitorProfileRepo);
//    }
//
//    public function testGetNonExisted() {
//        $visitorProfile = $this->visitorProfileRepo->get('dlksdhfweuih');
//        $this->assertNull($visitorProfile);
//    }
//
//    public function testGetAfterSetup() {
//        $visitorProfile = $this->visitorProfileRepo->get(self::$loginName);    // !!!! jenom po insertu v setUp - hodnotu vrací dao
//        $this->assertInstanceOf(VisitorProfile::class, $visitorProfile);
//    }
//
//    public function testAdd() {
//        self::$loginNameAdded = self::insertLoginRecord($this->container);
//
//        $visitorProfile = new VisitorProfile();
//        $visitorProfile->setLoginLoginName(self::$loginNameAdded);
//        $visitorProfile->setPrefix("Bleble.");
//        $visitorProfile->setName("Jméno");
//        $visitorProfile->setSurname("Příjmení");
//        $visitorProfile->setPostfix("Blabla.");
//        $visitorProfile->setEmail("qwertzuio@twrqew.qt");
//        $visitorProfile->setPhone('+999 888 777 666');
//        $visitorProfile->setCvEducationText("Školy mám.");
//        $visitorProfile->setCvSkillsText("Umím fčecko nejlýp.");
//
//        $this->visitorProfileRepo->add($visitorProfile);
//        $this->assertTrue($visitorProfile->isLocked());
//        self::$visitorProfileAdded = $visitorProfile;
//
////        $cvFinfo = new \SplFileInfo($cvFilepathName);
////        $file = $cvFinfo->openFile();
//    }
//
//    public function testGetAfterAdd() {
//        $visitorProfile = $this->visitorProfileRepo->get(self::$loginNameAdded);
//        $this->assertInstanceOf(VisitorProfile::class, $visitorProfile);
//    }
//
//    public function testAddAndReread() {
//        $loginName = self::insertLoginRecord($this->container);
//
//        $visitorProfile = new VisitorProfile();
//        $visitorProfile->setLoginLoginName($loginName);
//        $visitorProfile->setPrefix("Trdlo.");
//        $visitorProfile->setName("Julián");
//        $visitorProfile->setSurname("Bublifuk");
//        $this->visitorProfileRepo->add($visitorProfile);
//        $this->visitorProfileRepo->flush();
//        $visitorProfileRereaded = $this->visitorProfileRepo->get($loginName);
//        $this->assertInstanceOf(VisitorProfile::class, $visitorProfileRereaded);
//        $this->assertTrue($visitorProfileRereaded->isPersisted());
//    }
//
//    public function testFindAll() {
//        $visitorProfile = $this->visitorProfileRepo->findAll();
//        $this->assertTrue(is_array($visitorProfile));
//    }
//
//    public function testFind() {
//        $prefix = self::$loginNamePrefix;
//        $visitorsProfile = $this->visitorProfileRepo->find("login_login_name LIKE '$prefix%'", []);
//        $this->assertTrue(is_array($visitorsProfile));
//    }
//
//    public function testRemove() {
//        $visitorProfile = $this->visitorProfileRepo->get(self::$loginName);    // !!!! jenom po insertu v setUp - hodnotu vrací dao
//        $this->assertInstanceOf(VisitorProfile::class, $visitorProfile);
//        $this->visitorProfileRepo->remove($visitorProfile);
//        $this->assertFalse($visitorProfile->isPersisted());
//        $this->assertTrue($visitorProfile->isLocked());   // maže až při flush
//        $this->visitorProfileRepo->flush();
//        $this->assertFalse($visitorProfile->isLocked());
//        // pokus o čtení
//        $visitorProfile = $this->visitorProfileRepo->get(self::$loginName);
//        $this->assertNull($visitorProfile);
//    }

}
