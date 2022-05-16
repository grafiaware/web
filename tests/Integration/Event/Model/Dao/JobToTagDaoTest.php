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
    private static $job_tag_tag_fk2;

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
        $pozadovaneVzdelaniData = new RowData();
        $pozadovaneVzdelaniData->import(['stupen' => "999" ]);
        $pozadovaneVzdelaniData->import(['vzdelani' => "vzdelani 999" ]);
        $pozadovaneVzdelaniDao->insert($pozadovaneVzdelaniData);      
        self::$stupen_fk = $pozadovaneVzdelaniDao->get(['stupen' => "999" ]) ['stupen'] ;
       

         /** @var JobDao $jobDao */
        $jobDao = $container->get(JobDao::class);
        $jobData = new RowData();
        $jobData->import([ 'company_id' => self::$company_id,
                           'pozadovane_vzdelani_stupen' => self::$stupen_fk
                         ]);
        $ok = $jobDao->insert($jobData);      
        self::$job_id_fk = $jobDao->lastInsertIdValue();

        /** @var JobTagDao $jobTagDao */
        $jobTagDao = $container->get(JobTagDao::class); 
        $jobTagData = new RowData();                
        $jobTagData->import(["tag" => "nalepka moje"  ]);
        $ok =  $jobTagDao->insert($jobTagData);  
        self::$job_tag_tag_fk = "nalepka moje";
        
        $jobTagData = new RowData(); 
        $jobTagData->import(["tag" => "nalepka moje druha"  ]);
        $ok =  $jobTagDao->insert($jobTagData);  
        self::$job_tag_tag_fk2 = "nalepka moje druha";
        //$jobTagDao->lastInsertIdValue(); //to je vlastne "nalepka"  //lastInsertId vraci z DB string

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
        
        
        
        //maze po sobe  vyrobene věty v tabulkach 
        /** @var $jobToTagDao $jobToTagDao */
        $jobToTagDao = $container->get(JobToTagDao::class);  ////aby slo smazat  job a job_tag
        $jobToTagData = $jobToTagDao->get(['job_tag_tag' =>  self::$job_tag_tag_fk, 'job_id' => self::$job_id_fk  ]);
        if ($jobToTagData) {
                $ok = $jobToTagDao->delete($jobToTagData);}
        $jobToTagData = $jobToTagDao->get(['job_tag_tag' =>  self::$job_tag_tag_fk2, 'job_id' => self::$job_id_fk  ]);
        if ($jobToTagData) {
                $ok = $jobToTagDao->delete($jobToTagData);}                
                
        /** @var JobDao $jobDao */  //aby slo smazat   company, pozadovane_vzdelani
        $jobDao = $container->get(JobDao::class);    
        $jobRow = $jobDao->get( [ 'id' => self::$job_id_fk ] );
        $ok = $jobDao->delete($jobRow);                       
        /** @var $jobTagDao $jobTagDao */ 
        $jobTagDao = $container->get(JobTagDao::class);
        $jobTagData = $jobTagDao->get(['tag' =>  self::$job_tag_tag_fk ]);
        $ok = $jobTagDao->delete($jobTagData);           
        $jobTagData = $jobTagDao->get(['tag' =>  self::$job_tag_tag_fk2 ]);
        $ok = $jobTagDao->delete($jobTagData);   
        /** @var PozadovaneVzdelaniDao $pozadovaneVzdelaniDao */
        $pozadovaneVzdelaniDao = $container->get(PozadovaneVzdelaniDao::class);    
        $pozadovaneVzdelaniData = $pozadovaneVzdelaniDao->get(['stupen' => 999]);
        $ok = $pozadovaneVzdelaniDao->delete($pozadovaneVzdelaniData);         
        /** @var CompanyDao $companyDao */
        $companyDao = $container->get(CompanyDao::class);    
        $companyData = $companyDao->get(['id' =>  self::$company_id ]);
        $ok = $companyDao->delete($companyData);
                       
    }

    public function testSetUp() {
        $this->assertIsString(self::$job_id_fk);   //lastInsertId vraci z DB string
        $this->assertIsString(self::$job_tag_tag_fk);
        $this->assertInstanceOf(JobToTagDao::class, $this->dao);
    }

//**    
    
    public function testInsert() {
        $rowData = new RowData();
        $rowData->import(['job_id' =>  self::$job_id_fk ]);
        $rowData->import(['job_tag_tag' =>  self::$job_tag_tag_fk ]);        
        $this->dao->insert($rowData);
        $this->assertEquals(1, $this->dao->getRowCount());
    }
  
    public function test2Columns() {
        $jobToTagRow = $this->dao->get(['job_id' =>  self::$job_id_fk, 'job_tag_tag' =>  self::$job_tag_tag_fk] );
        $this->assertCount(2, $jobToTagRow);
    }

       
    
    public function testGet() {
        $jobToTagRows = $this->dao->get( ['job_id' =>  self::$job_id_fk ,  'job_tag_tag' =>  self::$job_tag_tag_fk ] );
        $this->assertInstanceOf(RowDataInterface::class, $jobToTagRows );
    }
    
    
    
    public function testFindExistingRowsByJobId() {
        $jobToTagRows = $this->dao->findByJobIdFk( ['job_id' => self::$job_id_fk ]);
        $this->assertIsArray($jobToTagRows);
        $this->assertGreaterThan(0, count($jobToTagRows));
        $this->assertInstanceOf(RowDataInterface::class, $jobToTagRows[0]);
    }

    public function testFindExistingRowsByJobTagTag() {
        $jobToTagRows = $this->dao->findByJobTagFk( ['job_tag_tag' => self::$job_tag_tag_fk] );
        $this->assertIsArray($jobToTagRows);
        $this->assertGreaterThan(0, count($jobToTagRows));
        $this->assertInstanceOf(RowDataInterface::class, $jobToTagRows[0]);
    }

    
    
    public function testUpdate() {
        $jobToTagRows = $this->dao->get( ['job_id' =>  self::$job_id_fk ,  'job_tag_tag' =>  self::$job_tag_tag_fk ] );
        //$eventId = $jobToTagRows['event_id_fk'];
        $this->assertIsInt($jobToTagRows['job_id']);
        $this->assertIsString($jobToTagRows['job_tag_tag']);
        
        $this->setUp(); //nove dao
        $jobToTagRows['job_tag_tag'] = self::$job_tag_tag_fk2;
        $this->dao->update( $jobToTagRows );
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $jobToTagRowsRereaded = $this->dao->get( ['job_id' =>  self::$job_id_fk ,  'job_tag_tag' =>  self::$job_tag_tag_fk2 ] );
        $this->assertInstanceOf(RowDataInterface::class, $jobToTagRowsRereaded);
        $this->assertEquals(self::$job_tag_tag_fk2, $jobToTagRowsRereaded['job_tag_tag']);

    }
   

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
