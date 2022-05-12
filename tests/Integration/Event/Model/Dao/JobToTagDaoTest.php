<?php

declare(strict_types=1);

use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Dao\JobDao;
use Events\Model\Dao\JobTagDao;
use Events\Model\Dao\JobToTagDao;

use Events\Model\Dao\PozadovaneVzdelaniDao;
use Events\Model\Dao\CompanyDao;


use Model\RowData\RowData;
use Model\RowData\RowDataInterface;

/**
 *
 * @author 
 */
class JobToTagDaoTest extends AppRunner {

    private $container;
    /**
     *
     * @var JobToTagDao
     */
    private $dao;
    
    private static $company_id;
    private static $stupen_fk;
    private static $job_id_fk;
    private static $job_tag_tag_fk;

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        $container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(
                    (new Container(
                        )
                    )
                )
            );
               
        
        // nový  Company, PozadovaneVzdelani, Job, JobTtag,
        $companyDao = $container->get(CompanyDao::class);
        $companyData = new RowData();
        $companyData->import(['eventInstitutionName30' => "chacha" ]);
        $companyData->import(['name' => "CompanyName pro JobToTagDaoTest" ]);
        $companyDao->insert($companyData);
        self::$company_id = $companyDao->lastInsertIdValue();
        
         /** @var PozadovaneVzdelaniDao $pozadovaneVzdelaniDao */
        $pozadovaneVzdelaniDao = $container->get(PozadovaneVzdelaniDao::class);
        if (!($pozadovaneVzdelaniDao->get(['stupen' => "999" ]) ['stupen'] )) {        
            $pozadovaneVzdelaniData = new RowData();
            $pozadovaneVzdelaniData->import(['stupen' => "999" ]);
            $pozadovaneVzdelaniData->import(['vzdelani' => "vzdelani 999" ]);
            $pozadovaneVzdelaniDao->insert($pozadovaneVzdelaniData);      
            self::$stupen_fk = $pozadovaneVzdelaniDao->get(['stupen' => "999" ]) ['stupen'] ;
        }
        else  {
            self::$stupen_fk = $pozadovaneVzdelaniDao->get(['stupen' => "999" ]) ['stupen'] ;            
        }

        
        $jobDao = $container->get(JobDao::class);
        $jobData = new RowData();
        $jobData->import([ 'company_id' => self::$company_id,
                           'pozadovane_vzdelani_stupen' => self::$stupen_fk
            ]);
        $ok = $jobDao->insert($jobData);      
        self::$job_id_fk = $jobDao->lastInsertIdValue();

     
        $jobTagDao = $container->get(JobTagDao::class);
        $jobTagData = new RowData();                
        $jobTagData->import(["tag" => "nalepka moje"  ]);
        $ok =  $jobTagDao->insert($jobTagData);  
        self::$job_tag_tag_fk = $jobTagDao->lastInsertIdValue(); //to je vlastne "nalepka" . 

    }

    protected function setUp(): void {
        $this->container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(
                    (new Container(
                        )
                    )
                )
            );
        $this->dao = $this->container->get( JobToTagDao::class);  // vždy nový objekt
    }

    
    protected function tearDown(): void {
    }

    public static function tearDownAfterClass(): void {
        self::bootstrapBeforeClass();
        $container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(
                    (new Container(
                        )
                    )
                )
            );
        
        /** @var JobDao $jobDao */
        $jobDao = $container->get(JobDao::class);    
        $jobRow = $jobDao->get(self::$job_id_fk);
        $ok = $this->dao->delete($jobRow);
        
        /** @var PozadovaneVzdelaniDao $pozadovaneVzdelaniDao */
        $pozadovaneVzdelaniDao = $container->get(PozadovaneVzdelaniDao::class);    
        $pozadovaneVzdelaniData = $pozadovaneVzdelaniDao->get(['stupen' => "999"]);
        $ok = $pozadovaneVzdelaniDao->delete($pozadovaneVzdelaniData);
         
        /** @var CompanyDao $companyDao */
        $companyDao = $container->get(CompanyDao::class);    
        $companyData = $companyDao->get(['id' =>  self::$company_id ]);
        $ok = $companyDao->delete($companyData);
        
        /** @var $jobTagDao $jobTagDao */
        $jobTagDao = $container->get(JobTagDao::class);
        $jobTagData->get(['tag' =>  self::$job_tag_tag_fk ]);
        $ok = $jobTagDao->delete($jobTagData);
       
    }

    public function testSetUp() {
        $this->assertIsInt(self::$job_id_fk);
        $this->assertIsString(self::$job_tag_tag_fk);
        $this->assertInstanceOf(JobToTag::class, $this->dao);
    }

//**    
    
//    public function testInsert() {
//        $rowData = new RowData();
//        $rowData->import(['login_login_name_fk' => self::$login_login_name_fk, 'event_id_fk' => self::$event_id_fk]);
//        $this->dao->insert($rowData);
//        $this->assertEquals(1, $this->dao->getRowCount());
//    }
//
//    public function testGetByPk() {
//        $enrollRow = $this->dao->get(['login_login_name_fk' => self::$login_login_name_fk, 'event_id_fk' => self::$event_id_fk]);
//        $this->assertInstanceOf(RowDataInterface::class, $enrollRow);
//    }
//
//    public function test2Columns() {
//        $enrollRow = $this->dao->get(['login_login_name_fk' => self::$login_login_name_fk, 'event_id_fk' => self::$event_id_fk]);
//        $this->assertCount(2, $enrollRow);
//    }
//
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
//    }
}
