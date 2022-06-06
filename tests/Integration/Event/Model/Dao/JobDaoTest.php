<?php
declare(strict_types=1);

use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Dao\JobDao;
use Events\Model\Dao\PozadovaneVzdelaniDao;
use Events\Model\Dao\CompanyDao;
use Events\Model\Dao\JobToTagDao;
use Events\Model\Dao\VisitorJobRequestDao;
use Events\Model\Dao\LoginDao;

use Model\RowData\RowData;
use Model\RowData\RowDataInterface;

/**
 * Description of JobDaoTest
 *
 * @author vlse2610
 */
class JobDaoTest  extends AppRunner {
    private $container;

    /**
     *
     * @var JobDao
     */
    private $dao;

    private static $idTouple; 
    private static $stupen_fk;
    private static $company_id;
    private static $jobToTag_jobId;
   // private static $visitorPrimaryKeyTouples;
    private static $login_login_name;
    private static $jobTagTouple;

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
        
        // do company ulozit -- potrebuju company_id pro job
        // do pozadovane vzdelani ulozit -- potrebuji stupen  pro job 
        // v job - id je autoincrement
        /** @var CompanyDao $companyDao */
        $companyDao = $container->get(CompanyDao::class);
        $companyData = new RowData();
        $companyData->import(['eventInstitutionName30' => "chacha" ]);
        $companyData->import(['name' => "CompanyName pro JobDaoTest" ]);
        $companyDao->insert($companyData);
        self::$company_id = $companyDao->lastInsertIdValue();
        
         /** @var PozadovaneVzdelaniDao $pozadovaneVzdelaniDao */
        $pozadovaneVzdelaniDao = $container->get(PozadovaneVzdelaniDao::class);
        $pozadovaneVzdelaniData = new RowData();
        $pozadovaneVzdelaniData->import(['stupen' => "999" ]);
        $pozadovaneVzdelaniData->import(['vzdelani' => "vzdelani 999" ]);
        $pozadovaneVzdelaniDao->insert($pozadovaneVzdelaniData);      
        self::$stupen_fk = $pozadovaneVzdelaniDao->get(['stupen' => "999" ]) ['stupen'] ;      
        
        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        do {
            $loginName = /*$prefix."_".*/ uniqid();
            $loginPouzit = $loginDao->get(['login_name' => $loginName]);
        } while ($loginPouzit);
        $loginData = new RowData();
        $loginData->import(['login_name' => $loginName]);
        $loginDao->insert($loginData);
        self::$login_login_name = $loginDao->get(['login_name' => $loginName])['login_name'];
        
        self::$jobTagTouple = [ 'job_tag_tag' => 'technická'];
    }

    protected function setUp(): void {
        $this->container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        $this->dao = $this->container->get(JobDao::class);  // vždy nový objekt
    }

    protected function tearDown(): void {
    }

    public static function tearDownAfterClass(): void {
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
        $jobData = $jobDao->get(['id' => self::$idTouple['id'] ]);
        if  ($jobData) {
            $jobDao->delete($jobData);
        }
        
        /** @var PozadovaneVzdelaniDao $pozadovaneVzdelaniDao */
        $pozadovaneVzdelaniDao = $container->get(PozadovaneVzdelaniDao::class);    
        $pozadovaneVzdelaniData = $pozadovaneVzdelaniDao->get(['stupen' => "999"]);
        $ok = $pozadovaneVzdelaniDao->delete($pozadovaneVzdelaniData);
         
        /** @var CompanyDao $companyDao */
        $companyDao = $container->get(CompanyDao::class);    
        $companyData = $companyDao->get(['id' =>  self::$company_id ]);
        $ok = $companyDao->delete($companyData);
        
        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);        
        $loginData = $loginDao->get(['login_name' => self::$login_login_name]);
        $loginDao->delete($loginData);
           
//        //smazat job_to_tag
//        /** @var JobToTagDao $jobToTagDao */
//        $jobToTagDao = $container->get(JobToTag::class);      
//        $jobToTagDao->get(self::$jobToTag_jobId);        
//        $jobToTagDao->insert($jobToTagData);    
//        self::$jobToTag_jobId = $jobToTagDao->getJobId();        
//                
//        //smayat visitor_job_request
//        /** @var VisitorJobRequestDao $visitorJobRequestDao */
//        $visitorJobRequestDao = $container->get( VisitorJobRequestDao::class);  
//        $visitorJobRequestData = new RowData();
//        $visitorJobRequestData->import( ['job_tag_tag' => 'Věda a technika'  ] );
//        $visitorJobRequestDao->insert( $visitorJobRequestData );    
//        self::$visitorPrima
       
        
        
    }

    public function testSetUp() {
        $this->assertIsNumeric(self::$company_id);
        $this->assertIsNumeric(self::$stupen_fk);
      
        $this->assertInstanceOf(JobDao::class, $this->dao);

    }
      public function testInsert() {      
        //vvrobit job
        $rowData = new RowData();
        $rowData->import( ['company_id' => self::$company_id, 'pozadovane_vzdelani_stupen' => self::$stupen_fk  ]);
        $this->dao->insert($rowData);        
        self::$idTouple = $this->dao->getLastInsertIdTouple();
        $this->assertIsArray(self::$idTouple);
        $numRows = $this->dao->getRowCount();
        $this->assertEquals(1, $numRows);
                
        
        //vyrobit job_to_tag
        /** @var JobToTagDao $jobToTagDao */
        $jobToTagDao = $this->container->get(JobToTagDao::class);  
        $jobToTagData = new RowData();
        $jobToTagData->import( [ self::$jobTagTouple ['job_tag_tag']  , 'job_id'=>self::$idTouple['id'] ] );
        $jobToTagDao->insert($jobToTagData);                            
        //vyrobit visitor_job_request
        /** @var VisitorJobRequestDao $visitorJobRequestDao */
        $visitorJobRequestDao =  $this->container->get( VisitorJobRequestDao::class);  
        $visitorJobRequestData = new RowData();        
        $visitorJobRequestData->import( ['job_id'=>self::$idTouple['id'], 'login_login_name' => self::$login_login_name,
                                         'position_name'=>"název pozice" ] );
        $visitorJobRequestDao->insert( $visitorJobRequestData );    
        
    }

    public function testGetExistingRow() {
        $jobRow = $this->dao->get(self::$idTouple);
        $this->assertInstanceOf(RowDataInterface::class, $jobRow);
    }

    
    public function test8Columns() {
        $jobRow = $this->dao->get(self::$idTouple);
        $this->assertCount(8, $jobRow);
    }

    
    public function testUpdate() {
        $jobRow = $this->dao->get(self::$idTouple);
        $name = $jobRow['nazev'];
        $this->assertNull($jobRow['nazev'] );
        
        $this->setUp();
        $updated = 'updated name'; //. $name;
        $jobRow['nazev'] = $updated;
        $this->dao->update($jobRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $jobRowRereaded = $this->dao->get(self::$idTouple);
        $this->assertEquals($jobRow, $jobRowRereaded);
        $this->assertContains('updated', $jobRowRereaded['nazev']);
    }

    public function testFind() {
        $jobRow = $this->dao->find();
        $this->assertIsArray($jobRow);
        $this->assertGreaterThanOrEqual(1, count($jobRow));
        $this->assertInstanceOf(RowDataInterface::class, $jobRow[0]);
    }

    public function testDelete() {
        $jobRow = $this->dao->get(self::$idTouple);
        $this->dao->delete($jobRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $companyRow = $this->dao->get(self::$idTouple);
        $this->assertNull($companyRow);
        
        
        //kontrola CASCADE
        //job_to_tag
        /** @var JobToTagDao $jobToTagDao */
        $jobToTagDao = $this->container->get(JobToTagDao::class);      
        $jobToTagData = $jobToTagDao->get( ['job_id' => self::$jobToTag_jobId,  self::$jobTagTouple  ] );  
        $this->assertNull($jobToTagData); 
                                    
        //smazat visitor_job_request
        /** @var VisitorJobRequestDao $visitorJobRequestDao */
        $visitorJobRequestDao = $this->container->get( VisitorJobRequestDao::class);  
        $visitorJobRequestData = $visitorJobRequestDao->get( ['login_login_name' => self::$login_login_name] );        
        $this->assertNull($visitorJobRequestData); 

    }
}
