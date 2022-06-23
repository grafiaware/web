<?php
declare(strict_types=1);

use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Dao\VisitorJobRequestDao;
use Events\Model\Dao\LoginDao;
use Events\Model\Dao\JobDao;

use Model\RowData\RowData;
use Model\RowData\RowDataInterface;

/**
 * Description of VisitorJobRequestTest
 *
 * @author vlse2610
 */
class VisitorJobRequestTest  extends AppRunner {

    private $container;
    /**
     *
     * @var  VisitorJobRequestDao
     */
    private $dao;
    
    private static $loginTouple;
    private static $companyIdTouple;
    private static $jobIdTouple;

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        
        $container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(
                    (new Container()   )
                )
            );
        
        // nový login login_name, company, job
        $prefix = "testVisitorJobRequestDaoTest";
        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        // prefix + uniquid - bez zamykání db
        do {
            $loginName = $prefix."_".uniqid();
            $login = $loginDao->get(['login_name' => $loginName]);
        } while ($login);
        $loginData = new RowData();
        $loginData->import(['login_name' => $loginName]);
        $loginDao->insert($loginData);
        self::$loginTouple = $loginDao->get(['login_name' => $loginName])['login_name'];
             
        /** @var CompanyDao $companyDao */
        $companyDao = $container->get(CompanyDao::class);    
        $companyData = new RowData();
        $companyData->offsetSet( 'name' , "pomocna pro VisitorJobRequestDaoTest"  );
        $companyDao->insert($companyData);
        self::$companyIdTouple = $companyDao->getLastInsertIdTouple();
                        
        /** @var JobDao $jobDao */
        $jobDao = $container->get(JobDao::class);
        $jobData = new RowData();
        $jobData->import( ['pozadovane_vzdelani_stupen' => 1, 'company_id' =>self::$companyIdTouple['id']] );
        $jobDao->insert($jobData);    
        self::$jobIdTouple = $jobDao->getLastInsertIdTouple();                                 
    }

    protected function setUp(): void {
        $this->container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(
                    (new Container(  )  )
                )
            );
        $this->dao = $this->container->get(VisitorJobRequestDao::class);  // vždy nový objekt
    }
    
    

    protected function tearDown(): void {
    }

    public static function tearDownAfterClass(): void {
        $container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(
                    (new Container( ) )
                )
            );
        
        //smaze company a job
        $companyDao = $container->get(CompanyDao::class);
        $companyRow = $companyDao->get( self::$companyIdTouple );
        $companyDao->delete($companyRow);      
            
                
        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        $loginRow  = $loginDao->get( ['login_name' => self::$loginTouple ] );
        $loginDao->delete($loginRow); 

    }

    public function testSetUp() {       
        $this->assertInstanceOf(VisitorJobRequestDao::class, $this->dao);

    }
//
//    public function testInsert() {
//        $rowData = new RowData();
//        $rowData->import(['login_login_name_fk' => self::$login_login_name_fk, 'event_id_fk' => self::$event_id_fk]);
//        $this->dao->insert($rowData);
//        $this->assertEquals(1, $this->dao->getRowCount());       
//                
//    }
//
//    public function testGet() {
//        $enrollRow = $this->dao->get(['login_login_name_fk' => self::$login_login_name_fk, 'event_id_fk' => self::$event_id_fk]);
//        $this->assertInstanceOf(RowDataInterface::class, $enrollRow);
//    }
//
//    public function test2Columns() {
//        $enrollRow = $this->dao->get(['login_login_name_fk' => self::$login_login_name_fk, 'event_id_fk' => self::$event_id_fk]);
//        $this->assertCount(2, $enrollRow);
//    }

    
    
    
    
    
//    public function testFindExistingRowsByLoginName() {
//        $enrollRows = $this->dao->findByLoginNameFk(['login_login_name_fk' => self::$login_login_name_fk]);
//        $this->assertIsArray($enrollRows);
//        $this->assertGreaterThan(0, count($enrollRows));
//        $this->assertInstanceOf(RowDataInterface::class, $enrollRows[0]);
//    }
//
//    public function testFindExistingRowsByEventId() {
//        $enrollRows = $this->dao->findByEventIdFk(['event_id_fk' => self::$event_id_fk]);
//        $this->assertIsArray($enrollRows);
//        $this->assertGreaterThan(0, count($enrollRows));
//        $this->assertInstanceOf(RowDataInterface::class, $enrollRows[0]);
//    }
//
//    public function testUpdate() {
//        $enrollRow = $this->dao->get(['login_login_name_fk' => self::$login_login_name_fk, 'event_id_fk' => self::$event_id_fk]);
//        $eventId = $enrollRow['event_id_fk'];
//        $this->assertIsString($enrollRow['login_login_name_fk']);
//        $this->assertIsInt($enrollRow['event_id_fk']);
//        //
//        $this->setUp();
//        $enrollRow['event_id_fk'] = self::$event_id_fk_2;
//        $this->dao->update($enrollRow);
//        $this->assertEquals(1, $this->dao->getRowCount());
//
//        $this->setUp();
//        $enrollRowRereaded = $this->dao->get(['login_login_name_fk' => self::$login_login_name_fk, 'event_id_fk' => self::$event_id_fk_2]);
//        $this->assertInstanceOf(RowDataInterface::class, $enrollRowRereaded);
//        $this->assertEquals(self::$event_id_fk_2, $enrollRowRereaded['event_id_fk']);
//
//    }
//
//    public function testFindByLoginNameFk() {
//        $enrollRowsRereaded = $this->dao->findByLoginNameFk(['login_login_name_fk' => self::$login_login_name_fk]);
//        $this->assertIsArray($enrollRowsRereaded);
//        $this->assertGreaterThanOrEqual(1, count($enrollRowsRereaded));
//        $this->assertInstanceOf(RowDataInterface::class, $enrollRowsRereaded[0]);
//    }
//
//    public function testFindByEventIdFk() {
//        $enrollRowsRereaded = $this->dao->findByEventIdFk(['event_id_fk' => self::$event_id_fk_2]);
//        $this->assertIsArray($enrollRowsRereaded);
//        $this->assertGreaterThanOrEqual(1, count($enrollRowsRereaded));
//        $this->assertInstanceOf(RowDataInterface::class, $enrollRowsRereaded[0]);
//    }
//
//    public function testFind() {
//        $enrollRowsArray = $this->dao->find();
//        $this->assertIsArray($enrollRowsArray);
//        $this->assertGreaterThanOrEqual(1, count($enrollRowsArray));
//        $this->assertInstanceOf(RowDataInterface::class, $enrollRowsArray[0]);
//    }
//
//    public function testDelete() {
//        $enrollRow = $this->dao->get(['login_login_name_fk' => self::$login_login_name_fk, 'event_id_fk' => self::$event_id_fk_2]);
//
//        $this->dao->delete($enrollRow);
//        $this->assertEquals(1, $this->dao->getRowCount());
//
//        $this->setUp();
//        $this->dao->delete($enrollRow);
//        $this->assertEquals(0, $this->dao->getRowCount());
//        
//      
//        
//        //kontrola RESTRICT
//        //smazal enroll,  nesmazal login  = OK
//        /** @var LoginDao $loginDao */
//        $loginDao = $this->container->get(LoginDao::class);
//        $loginRow  = $loginDao->get( ['login_name' => self::$login_login_name_fk  ] );       
//        $this->assertCount(1, $loginRow);
//
//         
//        //smazat v event vetu s  id=self::$event_id_fk - provede v tearDownAfterClass
//    }
}
