<?php
declare(strict_types=1);

use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Dao\DocumentDao;
use Events\Model\Dao\VisitorJobRequestDao;
use Events\Model\Dao\VisitorProfileDao;

use Events\Model\Dao\LoginDao;
use Events\Model\Dao\JobDao;
use Events\Model\Dao\CompanyDao;

use Model\RowData\RowData;
use Model\RowData\RowDataInterface;


/**
 * Description of DocumentDaoTest
 *
 * @author vlse2610
 */
class DocumentDaoTest  extends AppRunner {
    private $container;
    
    /**
     *
     * @var  DocumentDao
     */
    private $dao;
    
    private static $loginName;
    private static $companyIdTouple;
    private static $jobIdTouple;
    private static $documentIdTouple;
    
    private static $visitorProfileTouple;
    private static $visitorJobRequestTouple;

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        
        $container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(   (new Container()   )   )
            );
        
        // nový login login_name, company, job
        $prefix = "proDocumentDaoTest";
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
        self::$loginName = $loginDao->get(['login_name' => $loginName])['login_name'];
             
        /** @var CompanyDao $companyDao */
        $companyDao = $container->get( CompanyDao::class);    
        $companyData = new RowData();
        $companyData->offsetSet( 'name' , "pomocna pro proDocumentDaoTest"  );
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
        $this->dao = $this->container->get(DocumentDao::class);  // vždy nový objekt
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
        
        //smaze company a job a visitor_job_request
        /** @var CompanyDao $companyDao */
        $companyDao = $container->get(CompanyDao::class);
        $companyRow = $companyDao->get( self::$companyIdTouple );
        $companyDao->delete($companyRow);  
        
        //smazat  visitor_profile    
        /** @var VisitorProfileDao $visitorProfileDao */
        $visitorProfileDao = $container->get(VisitorProfileDao::class);
        $visitorProfileRow  = $visitorProfileDao->get( self::$visitorProfileTouple );
        $visitorProfileDao->delete($visitorProfileRow); 
        
        
        //smaze login
        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        $loginRow  = $loginDao->get( ['login_name' => self::$loginName ] );
        $loginDao->delete($loginRow); 
    }

    
    public function testSetUp() {       
        $this->assertInstanceOf(DocumentDao::class, $this->dao);
    }

    
    public function testInsert() {
        $rowData = new RowData();
        $rowData->import( [  'document_filename' => 'jmenoFilename'                              
                          ] );        
        $this->dao->insert($rowData);
        self::$documentIdTouple = $this->dao->getLastInsertIdTouple();
        $this->assertIsArray(self::$documentIdTouple);
        $numRows = $this->dao->getRowCount();
        $this->assertEquals(1, $numRows);
                
    }

    public function testGet() {
        $documentRow = $this->dao->get( self::$documentIdTouple );
        $this->assertInstanceOf(RowDataInterface::class, $documentRow);
    }

    public function test4Columns() {
        $documentRow = $this->dao->get( self::$documentIdTouple );
        $this->assertCount(4, $documentRow);
    }

    
         
    public function testUpdate() {
        $documentRow = $this->dao->get( self::$documentIdTouple );
            $documentName = $documentRow['document_filename'];
        $this->assertIsString( $documentRow['document_filename'] );

        $this->setUp();
        $documentRow['document_filename'] = "d:\jmeno nevim čeho_1\x.doc";
        $documentRow['document_mimetype'] = "application/msword";
        $this->dao->update($documentRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $documentRowRereaded = $this->dao->get(  self::$documentIdTouple );
        $this->assertInstanceOf( RowDataInterface::class, $documentRowRereaded );
        $this->assertEquals(  "application/msword", $documentRowRereaded['document_mimetype']);

    }
          
     
     
    public function testFind() {
        $documentRowsArray = $this->dao->find();
        $this->assertIsArray($documentRowsArray);
        $this->assertGreaterThanOrEqual(1, count($documentRowsArray));
        $this->assertInstanceOf(RowDataInterface::class, $documentRowsArray[0]);
    }

    
    
    public function testDelete_proVisitorJobRequest() {   
        $documentRow = new RowData();    
        $documentRow->import( [ 'document_filename' => 'jmenoFilename_testDeleteVJR'                              
                          ] );        
        $this->dao->insert($documentRow);
        $documentIdTouple = $this->dao->getLastInsertIdTouple();
        
        
        /** @var VisitorJobRequestDao $visitorJobRequestDao */
        $visitorJobRequestDao = $this->container->get(VisitorJobRequestDao::class);
        /** @var RowData $visitorJobRequestRow */
        $visitorJobRequestRow = new RowData();        
        $visitorJobRequestRow->import( ['job_id'  => self::$jobIdTouple['id'],
                                        'login_login_name'  => self::$loginName,
                                        'letter_document'  => $documentIdTouple['id'],
                                        'cv_document'  => $documentIdTouple['id'],
                                        'position_name'  => 'jméno pozice'
                                       ] );
        $visitorJobRequestDao->insert( $visitorJobRequestRow );       
        self::$visitorJobRequestTouple = $visitorJobRequestDao->getPrimaryKeyTouples( $visitorJobRequestRow->getArrayCopy() );
      
        // kontrola SET NULL = že nastavi null ve visitor_job_request
        $documentRowReaded = $this->dao->get(  $documentIdTouple );
        $this->dao->delete($documentRowReaded);                 
        //kontrola null
        /** @var RowData $visitorJobRequestRow2 */
        $visitorJobRequestRow2 = $visitorJobRequestDao->get( self::$visitorJobRequestTouple);
        $this->assertNull( $visitorJobRequestRow2['cv_document']);
        $this->assertNull( $visitorJobRequestRow2['letter_document']);   
        
        //zustava věta  ve visitor_job_request
    }
       
    
     public function testDelete_proVisitorProfile() {   
        $documentRow = new RowData();    
        $documentRow->import( [ 'document_filename' => 'jmenoFilename_testDeleteVP'                              
                          ] );        
        $this->dao->insert($documentRow);
        $documentIdTouple = $this->dao->getLastInsertIdTouple();
        
        
        /** @var VisitorProfileDao $visitorProfileDao */
        $visitorProfileDao = $this->container->get(VisitorProfileDao::class);
        /** @var RowData $visitorProfileRow */
        $visitorProfileRow = new RowData();        
        $visitorProfileRow->import( [   'login_login_name'  => self::$loginName,
                                        'letter_document'  => $documentIdTouple['id'],
                                        'cv_document'  => $documentIdTouple['id'],
                                       ] );
        $visitorProfileDao->insert( $visitorProfileRow );     
        self::$visitorProfileTouple = $visitorProfileDao->getPrimaryKeyTouples( $visitorProfileRow->getArrayCopy() );
      
        // kontrola SET NULL = že nastavi null ve visitor_job_request
        $documentRowReaded = $this->dao->get(  $documentIdTouple );
        $this->dao->delete($documentRowReaded);                 
        //kontrola null
        /** @var RowData $visitorProfileRow2 */
        $visitorProfileRow2 = $visitorProfileDao->get( self::$visitorProfileTouple );
        $this->assertNull( $visitorProfileRow2['cv_document']);
        $this->assertNull( $visitorProfileRow2['letter_document']);  
        
        //zustava věta  ve visitor_profile
    }        
    

    
    public function testDelete() {
        $documentRow = $this->dao->get( self::$documentIdTouple );
        $this->dao->delete($documentRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $this->dao->delete($documentRow);
        $this->assertEquals(0, $this->dao->getRowCount());                     

   }
   
   
}
