<?php
declare(strict_types=1);

use Test\AppRunner\AppRunner;

use Pes\Container\Container;
use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Dao\CompanyDao;
use Events\Model\Dao\VisitorJobRequestDao;
use Events\Model\Dao\VisitorProfileDao;
use Events\Model\Dao\RepresentativeDao;
use Events\Model\Dao\JobDao;

use Events\Model\Dao\LoginDao;

use Model\RowData\RowData;
use Model\RowData\RowDataInterface;
use Pes\Database\Statement\Exception\ExecuteException;

/**
 * Description of LoginDaoTest
 *
 * @author vlse2610
 */
class LoginDaoTest extends AppRunner {
    private $container;
    
        

    /**
     *
     * @var LoginDao
     */
    private $dao;

    private static $loginNameTouple ;
    private static $loginNameTouple_poUpdate;
    private static $companyIdTouple;
    private static $jobidTouple;
    private static $visitorJobTouples;
    private static $visitorProfileTouples;
    private static $representativeIdTouple;
    
    private static $nameUpdated;



    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        $container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        
        // nova company - priprava potrebne propojene tabulky
        /** @var CompanyDao $companyDao */
        $companyDao = $container->get(CompanyDao::class);
        $rowData = new RowData();
        $rowData->offsetSet('name', "Company1");
        $rowData->offsetSet('eventInstitutionName30', 'ShortyCo.');
        $companyDao->insert($rowData);
        self::$companyIdTouple =  ['id' => $companyDao->lastInsertIdValue()];
        
        // nova job - priprava potrebne propojene tabulky
         /** @var JobDao $jobDao */
        $jobDao = $container->get(JobDao::class);
        $jobData = new RowData();
        $jobData->import([ 'company_id' => self::$companyIdTouple['id'],
                           'pozadovane_vzdelani_stupen' => 1
                         ]);
        $ok = $jobDao->insert($jobData);      
        self::$jobidTouple = ['id' => $jobDao->lastInsertIdValue() ]; 
             
        
        
        
        
//        /** @var LoginDao $loginDao */
//        $loginDao = $container->get(LoginDao::class);    
//        $companyData = new RowData();
//        $companyData->offsetSet( 'name' , "pomocna pro jobTagDaoTest"  );
//        $loginDao->insert($companyData);
//        self::$companyIdTouple = $loginDao->getLastInsertIdTouple();
                
    }

    protected function setUp(): void {
        $this->container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        $this->dao = $this->container->get(LoginDao::class);  // vždy nový objekt
    }

    protected function tearDown(): void {
    }

    public static function tearDownAfterClass(): void {
         $container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        /** @var CompanyDao $companyDao */
        $companyDao = $container->get(CompanyDao::class);        
        $companyRow = $companyDao->get( ['id' => self::$companyIdTouple ['id']  ]);
        $companyDao->delete($companyRow);
        
         //Job se smazalo uz kdyz se smazalo company
//        /** @var JobDao $jobDao */  
//        $jobDao = $container->get(JobDao::class);        
//        $jobRow = $jobDao->get( ['id' => self::$companyIdTouple ['id']  ]);
//        $jobDao->delete($jobRow);
        

    }

    public function testSetUp() {
        $this->assertInstanceOf(LoginDao::class, $this->dao);
    }
    
     
    public function testInsert() {  
        $rowData = new RowData();
        $rowData->offsetSet( 'login_name' , "Barbucha"  );
        $this->dao->insert($rowData);        
        $rowD =  $this->dao->get( ['login_name' => "Barbucha"] );
       
        /** @var RowData $rowD */
        $rowArray = $rowD->getArrayCopy();
        self::$loginNameTouple =  $this->dao->getPrimaryKeyTouples($rowArray);        
        $this->assertIsArray( self::$loginNameTouple );
                
        $numRows = $this->dao->getRowCount();
        $this->assertEquals(1, $numRows);
        //-------------------------------------------------        
        
        
        //nova representative
        /** @var RepresentativeDao $representativeDao */
        $representativeDao = $this->container->get(RepresentativeDao::class);
        $representativeData = new RowData();
        $representativeData->import( ['login_login_name' => $rowArray['login_name'], 'company_id' =>self::$companyIdTouple['id']] );
        $representativeDao->insert($representativeData);    
        /**  @var RowData  $row */
        $row = $representativeDao->get( ['login_login_name' => $rowArray['login_name'], 'company_id' =>self::$companyIdTouple['id']]  );
        self::$representativeIdTouple  = $representativeDao->getPrimaryKeyTouples($row->getArrayCopy());      

        
        
        // nova visitor_job_request
        /** @var VisitorJobRequestDao $visitorJobRequestDao */
        $visitorJobRequestDao = $this->container->get(VisitorJobRequestDao::class);  
        $visitorJobRequesData = new RowData();
        $visitorJobRequesData->import( [ 'login_login_name' => self::$loginNameTouple ['login_name']  , 'job_id'=>self::$jobidTouple['id'], 
                                        'position_name' => 'sedící spící'] );
        $visitorJobRequestDao->insert($visitorJobRequesData);   
        /**  @var RowData  $row */
        $row = $visitorJobRequestDao->get( [ 'login_login_name' => self::$loginNameTouple ['login_name']  , 'job_id'=>self::$jobidTouple['id'] ] );
        self::$visitorJobTouples  = $visitorJobRequestDao->getPrimaryKeyTouples($row->getArrayCopy());      
        
        // nova visitor_profile
        /** @var VisitorProfileDao $visitorProfileDao */
        $visitorProfileDao = $this->container->get(VisitorProfileDao::class);  
        $visitorProfileData = new RowData();
        $visitorProfileData->import( [ 'login_login_name' => self::$loginNameTouple ['login_name']   ] );
        $visitorProfileDao->insert($visitorProfileData);   
        /**  @var RowData  $row */
        $row = $visitorProfileDao->get( [ 'login_login_name' => self::$loginNameTouple ['login_name']  ] );
        self::$visitorProfileTouples  = $visitorProfileDao->getPrimaryKeyTouples($row->getArrayCopy());      
       
        // nova enroll - priprava potrebne propojene tabulky ...melo by byt take ...a neni udelano
        
        
   }

    public function testGetExistingRow() {
        $loginRow = $this->dao->get(self::$loginNameTouple);
        $this->assertInstanceOf(RowDataInterface::class, $loginRow);
    }

    public function test1Columns() {
        $loginRow = $this->dao->get( self::$loginNameTouple );
        $this->assertCount(1, $loginRow);
    }

   public function testUpdate_and_Exception() {
        $loginRow = $this->dao->get( ['login_name' => "Barbucha"] );
        $this->assertIsString( $loginRow['login_name'] );
              
        $this->setUp();
        self::$nameUpdated = str_replace('bucha', 'bubuchac',$loginRow['login_name']);
        $loginRow['login_name'] = self::$nameUpdated;
        $this->expectException(ExecuteException::class);
        $this->dao->update($loginRow);
       // neupdatuje, protoze nastaven RESTRICT na tabulkach propojenych pres login_name
       // vznikne Exception        
   }   
   
   
//        //kontrola CASCADE u update
//        //kontrola, ze v  je taky updatovany tag
//        /**  @var JobToTagDao  $jobToTagDao */
//        $jobToTagDao = $this->container->get(JobToTagDao::class);
//        $jobToTagRow = $jobToTagDao->get( [ 'job_tag_tag' => self::$jobTagTouple_poUpdate ['tag']  , 'job_id'=>self::$jobIdTouple['id'] ] );
//        $this->assertEquals( self::$jobTagTouple_poUpdate ['tag'], $jobToTagRow['job_tag_tag'] );
           

    public function testAfterUpdate_and_Exception()   {  
        //self::$loginNameTouple_poUpdate =  $this->dao->getPrimaryKeyTouples($rowArray);  
       // $loginRow = $this->dao->get( self::$loginNameTouple_poUpdate );
       // $rowArray = $loginRow->getArrayCopy();
//        self::$loginNameTouple_poUpdate =  $this->dao->getPrimaryKeyTouples($rowArray);  
//
//        $this->setUp();
//        $a = [ 'a' => self::$loginNameTouple_poUpdate ['login_name'] ] ;
//        $loginRowRereaded = $this->dao->get(  self::$loginNameTouple_poUpdate  );
//        $this->assertEquals($loginRow, $loginRowRereaded);
//        $this->assertStringContainsString('bubuchac', $loginRowRereaded['login_name']);  
    }    
    
    public function testFind() {
        $loginRow = $this->dao->find();
        $this->assertIsArray($loginRow);
        $this->assertGreaterThanOrEqual(1, count($loginRow));
        $this->assertInstanceOf(RowDataInterface::class, $loginRow[0]);
    }

   
//    public function testDeleteException() {   
//        // kontrola RESTRICT = že nevymaže job_tag, kdyz je  pouzit v job_to_tag
//        $jobTagRow = $this->dao->get(self::$jobTagTouple_poUpdate);
//        $this->expectException(ExecuteException::class);
//        $this->dao->delete($jobTagRow);                
//    }
    
    public function testDelete() {
         //napred smazat vsechny zavisle, pak login
        
        
        
        
        
        
        
        
        
        
//        //delete Company - amze job, jobToTag
//        /** @var LoginDao $companyDao */
//        $companyDao = $this->container->get(CompanyDao::class);    
//        $companyData = $companyDao->get(self::$companyIdTouple );
//        $ok = $companyDao->delete($companyData);              
//                
//        //pak smazat jobTag  
//        $this->setUp();
//        $jobTagRow = $this->dao->get(self::$jobTagTouple_poUpdate);
//        $this->dao->delete($jobTagRow);
//        $this->assertEquals(1, $this->dao->getRowCount());
//
//        //kontrola, ze smazano
//        $this->setUp();
//        $jobTagRow = $this->dao->get(self::$jobTagTouple_poUpdate);
//        $this->assertNull($jobTagRow);
//         
   }
}

