<?php
declare(strict_types=1);
namespace Test\Integration\Dao;

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
    private static $companyIdTouple;
    private static $jobIdTouple;
    private static $visitorJobTouples;
    private static $visitorProfileTouples;
    private static $representativeIdTouple;    


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
        self::$jobIdTouple = ['id' => $jobDao->lastInsertIdValue() ]; 
                             
        
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
        $visitorJobRequesData->import( ['login_login_name' => self::$loginNameTouple ['login_name']  , 'job_id'=>self::$jobIdTouple['id'], 
                                        'position_name' => 'sedící spící'] );
        $visitorJobRequestDao->insert($visitorJobRequesData);   
        /**  @var RowData  $row */
        $row = $visitorJobRequestDao->get( [ 'login_login_name' => self::$loginNameTouple ['login_name']  , 'job_id'=>self::$jobIdTouple['id'] ] );
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
        // neupdatuje, protoze nastaven RESTRICT na tabulkach propojenych pres login_name
        // vznikne Exception       
        $loginRow = $this->dao->get( ['login_name' => "Barbucha"] );
        $this->assertIsString( $loginRow['login_name'] );
              
        $this->setUp();
        $nameUpdated = str_replace('bucha', 'bubuchac',$loginRow['login_name']);
        $loginRow['login_name'] = $nameUpdated;
        $this->expectException(ExecuteException::class);
        $this->dao->update($loginRow);
       
   }   
   

           

    public function testAfterUpdate_and_Exception()   {  
        // testuji, zda  login_name v login, a login_login_name ve visitor_profile  zustalo stejne         
        $loginRowRereaded = $this->dao->get( self::$loginNameTouple  );         
        
        /** @var VisitorProfileDao $visitorProfileDao */
        $visitorProfileDao = $this->container->get(VisitorProfileDao::class);  
        /**  @var RowData  $row */
        $row = $visitorProfileDao->get( [ 'login_login_name' => self::$loginNameTouple ['login_name']  ] );        
                
        $this->assertEquals( $row['login_login_name'], $loginRowRereaded['login_name'] );
    }    
    
    
    public function testFind() {
        $loginRows = $this->dao->find();
        $this->assertIsArray($loginRows);
        $this->assertGreaterThanOrEqual(1, count($loginRows));
        $this->assertInstanceOf(RowDataInterface::class, $loginRows[0]);
    }

   
    public function testDeleteException() {   
        // kontrola RESTRICT = že nevymaže login, kdyz je  pouzit v jine tabulce (yde napr. v representative,... atd.)
        /**  @var RowData  $loginRow */    
        $loginRow = $this->dao->get( self::$loginNameTouple );
        $this->expectException(ExecuteException::class);
        $this->dao->delete($loginRow);                
       }
    
    public function testDelete() {
        //napred smazat vsechny zavisle, pak smazat login        
        /** @var VisitorProfileDao $visitorProfileDao */
        $visitorProfileDao = $this->container->get(VisitorProfileDao::class);  
        /**  @var RowData  $visitorData */
        $visitorData = $visitorProfileDao->get( [ 'login_login_name' => self::$loginNameTouple ['login_name']  ] );       
        $ok1 = $visitorProfileDao->delete($visitorData);  
                        
        /** @var CompanyDao $companyDao */
        $companyDao = $this->container->get(CompanyDao::class);        
        $companyRow = $companyDao->get( ['id' => self::$companyIdTouple ['id']  ]);
        $ok2 = $companyDao->delete($companyRow);       
        //smazani company smazalo job, company_contact, company_address, representative
        //smazani job smazalo visitor_job_request, job_to_tag
        
        //pak smazat login
        $this->setUp();
        $loginRow = $this->dao->get( self::$loginNameTouple );
        $this->dao->delete($loginRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        //kontrola, ze smazano
        $this->setUp();
        $loginRow = $this->dao->get( self::$loginNameTouple );
        $this->assertNull($loginRow);                         
                        
   }
}

