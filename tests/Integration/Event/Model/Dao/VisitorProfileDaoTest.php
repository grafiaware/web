<?php
declare(strict_types=1);
namespace Test\Integration\Dao;

use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Test\Integration\Event\Container\EventsModelContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Dao\LoginDao;
use Events\Model\Dao\VisitorProfileDao;
use Events\Model\Dao\DocumentDao;

use Model\RowData\RowData;
use Model\RowData\RowDataInterface;

/**
 * Description of VisitorProfileDaoTest
 * 
 * @author vlse2610
 */
class VisitorProfileDaoTest extends AppRunner {
    private $container;
    /**
     *
     * @var VisitorProfileDao
     */
    private $dao;
    
    private static $login_login_name_fk;
    private static $documentTouple;
    

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        $container =
            (new EventsModelContainerConfigurator())->configure( 
                    (new DbEventsContainerConfigurator())->configure(  (new Container( ) ) )
            );
        // nový login_name  pro TestCase
        $prefix = "testVisitorProfileDao";
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
        self::$login_login_name_fk = $loginDao->get(['login_name' => $loginName])['login_name'];
        
        /** @var DocumentDao $documentDao */
        $documentDao = $container->get(DocumentDao::class);
        $documentData = new RowData();
        $documentData->import( ["document_filename" => 'jmenoFile_VisitorProfileTest'
                               ]);
        $documentDao->insert( $documentData );  // id je autoincrement
        self::$documentTouple = $documentDao->getLastInsertIdTouple();       
    }

    protected function setUp(): void {
        $this->container =
            (new EventsModelContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(
                    (new Container( ) ) )
            );
        $this->dao = $this->container->get(VisitorProfileDao::class);  // vždy nový objekt
    }

    protected function tearDown(): void {
    }

    public static function tearDownAfterClass(): void {
        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(
                    (new Container( ) )  )
            );
        
        //smaze document
        /** @var DocumentDao $documentDao */
        $documentDao = $container->get(DocumentDao::class);
        $documentRow = $documentDao->get( self::$documentTouple );
        $documentDao->delete($documentRow);                                 
        
        //smaze login
        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        $loginRow  = $loginDao->get( ['login_name' => self::$login_login_name_fk ] );
        $loginDao->delete($loginRow); 

    }

    public function testSetUp() {
        $this->assertInstanceOf(VisitorProfileDao::class, $this->dao);

    }

    public function testInsert() {
        $rowData = new RowData();
        $rowData->import([ 'login_login_name' => self::$login_login_name_fk, 
                           'cv_document' => self::$documentTouple['id'],
                           'letter_document' => self::$documentTouple['id']
                        ]);
        $this->dao->insert($rowData);
        $this->assertEquals(1, $this->dao->getRowCount());       
                
    }

    public function testGet() {
        $visitorProfileRow = $this->dao->get(['login_login_name' => self::$login_login_name_fk]);
        $this->assertInstanceOf(RowDataInterface::class, $visitorProfileRow);
    }

    public function test11Columns() {
        $visitorProfilelRow = $this->dao->get(['login_login_name' => self::$login_login_name_fk]);
        $this->assertCount(11, $visitorProfilelRow);
    }

   
    public function testUpdate() {              
        
        $visitorProfileRow = $this->dao->get(['login_login_name' => self::$login_login_name_fk]);
            $loginName = $visitorProfileRow['login_login_name'];
        $this->assertIsString( $visitorProfileRow['login_login_name'] );
        
        $this->setUp();
        $visitorProfileRow['cv_education_text'] = '***zkusebni text';
        $this->dao->update($visitorProfileRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $visitorProfileRowRereaded = $this->dao->get(['login_login_name' => self::$login_login_name_fk]);
        $this->assertInstanceOf(RowDataInterface::class, $visitorProfileRowRereaded);
        $this->assertEquals( '***zkusebni text', $visitorProfileRowRereaded['cv_education_text']);

    }

   

    public function testFind() {
        $visitorProfilelRowsArray = $this->dao->find();
        $this->assertIsArray($visitorProfilelRowsArray);
        $this->assertGreaterThanOrEqual(1, count($visitorProfilelRowsArray));
        $this->assertInstanceOf(RowDataInterface::class, $visitorProfilelRowsArray[0]);
    }
    

    public function testDelete() {
        $visitorProfileRow = $this->dao->get(['login_login_name' => self::$login_login_name_fk]);
        $this->dao->delete($visitorProfileRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $this->dao->delete($visitorProfileRow);
        $this->assertEquals(0, $this->dao->getRowCount());              
        
    }
}

