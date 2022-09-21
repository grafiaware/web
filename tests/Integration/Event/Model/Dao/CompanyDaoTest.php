<?php
declare(strict_types=1);
namespace Test\Integration\Dao;

use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Test\Integration\Event\Container\EventsModelContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Dao\CompanyDao;
use Events\Model\Dao\CompanyAddressDao;
use Events\Model\Dao\CompanyContactDao;
use Events\Model\Dao\RepresentativeDao;
use Events\Model\Dao\LoginDao;

use Model\RowData\RowData;
use Model\RowData\RowDataInterface;

/**
 * Description of CompanyDaoTest
 *
 * @author vlse2610
 */
class CompanyDaoTest  extends AppRunner {
    private $container;
    /**
     *
     * @var CompanyDao
     */
    private $dao;
    
    private static $login_login_name;
    private static $id;
    

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        
        // nový login login_name a company_id pro TestCase
        $prefix = "CompanyDaoTestVelkaOsoba";
        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        do {
            $loginName = $prefix."_".uniqid();
            $loginPouzit = $loginDao->get(['login_name' => $loginName]);
        } while ($loginPouzit);
        $loginData = new RowData();
        $loginData->import(['login_name' => $loginName]);
        $loginDao->insert($loginData);
        self::$login_login_name = $loginDao->get(['login_name' => $loginName])['login_name'];         
        
        
    }

    protected function setUp(): void {
        $this->container =
            (new EventsModelContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        $this->dao = $this->container->get(CompanyDao::class);  // vždy nový objekt     
    }

    protected function tearDown(): void {
    }

    public static function tearDownAfterClass(): void {
        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );         
        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);   
        $loginRow = $loginDao->get(['login_name' => self::$login_login_name ]);
        $loginDao->delete($loginRow);
    }
    

    public function testSetUp() {
        $this->assertInstanceOf(CompanyDao::class, $this->dao);
    }
    
    
    public function testInsert() {                    
        $rowData = new RowData();
        $rowData->offsetSet('name', "testCompany-nameNNN");
        $rowData->offsetSet('eventInstitutionName30', null);

        $this->dao->insert($rowData);
        self::$id = $this->dao->getLastInsertIdTouple();
        $this->assertIsArray(self::$id);
        $numRows = $this->dao->getRowCount();
        $this->assertEquals(1, $numRows);
        
        //zavisla tabulka company_address    
        /** @var CompanyAddressDao $companyAddressDao */
        $companyAddressDao = $this->container->get(CompanyAddressDao::class);  // vždy nový objekt
        $rowDataCompanyAddress = new RowData();
        $rowDataCompanyAddress->import(
               ['company_id' => self::$id ['id'] ,
                'name' => 'VelkaFirma',
                'lokace' => 'Mars ',
                'psc' => '02020' ] );
        $companyAddressDao->insert($rowDataCompanyAddress);
        $this->assertEquals(1, $companyAddressDao->getRowCount());
        
        //zavisla tabulka company_contact       
        /** @var CompanyContactDao $companyContactDao */
        $companyContactDao = $this->container->get(CompanyContactDao::class);  // vždy nový objekt
        $rowDataCompanyContact = new RowData();
        $rowDataCompanyContact->import(
               ['company_id' => self::$id ['id'] ,
                'name' => 'VelkaOsoba',
                'phones' => '123456789'
                ] );
        $companyContactDao->insert($rowDataCompanyContact);
        $this->assertEquals(1, $companyContactDao->getRowCount());
        
        //zavisla tabulka representative          
        /** @var RepresentativeDao $representativeDao */
        $representativeDao = $this->container->get(RepresentativeDao::class ) ; 
        $rowDataRepresentative = new RowData();
        $rowDataRepresentative->import(
               ['company_id' => self::$id ['id'] ,
                'login_login_name' => self::$login_login_name                
               ] );
        $representativeDao->insert($rowDataRepresentative);
        $this->assertEquals(1, $representativeDao->getRowCount());
        
        
    }

    public function testGetExistingRow() {
        $companyRow = $this->dao->get(self::$id);
        $this->assertInstanceOf(RowDataInterface::class, $companyRow);
    }

    public function test3Columns() {
        $companyRow = $this->dao->get(self::$id);
        $this->assertCount(3, $companyRow);
    }

    public function testUpdate() {
        $companyRow = $this->dao->get(self::$id);
        $name = $companyRow['name'];
        $this->assertIsString($companyRow['name']);
        //
        $this->setUp();
        $updated = str_replace('NNN', 'NNN-updated', $name);
        $companyRow['name'] = $updated;
        $this->dao->update($companyRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $companyRowRereaded = $this->dao->get(self::$id);
        $this->assertEquals($companyRow, $companyRowRereaded);
        $this->assertStringContainsString('NNN-updated', $companyRowRereaded['name']);
    }

    public function testFind() {
        $companyRow = $this->dao->find();
        $this->assertIsArray($companyRow);
        $this->assertGreaterThanOrEqual(1, count($companyRow));
        $this->assertInstanceOf(RowDataInterface::class, $companyRow[0]);
    }

    public function testDelete() {
        $companyRow = $this->dao->get(self::$id);
        $this->dao->delete($companyRow);
        $this->assertEquals(1, $this->dao->getRowCount());
        
        //je-li nastaveno cascade na company_address.company_id, smaže i zavislou radku v company_address
        /** @var CompanyAddressDao $companyAddressDao */
        $companyAddressDao = $this->container->get(CompanyAddressDao::class);  
        $companyAddressRow = $companyAddressDao->get( [  'company_id'=> self::$id['id'] ] );
        $this->assertNull($companyAddressRow);        
        //je-li nastaveno cascade na company_contact.company_id, smaže i zavislou radku v company_contact
        /** @var CompanyContactDao $companyContactDao */
        $companyContactDao = $this->container->get(CompanyContactDao::class);  
        $companyContactRows = $companyContactDao->find(  $whereClause = " company_id = :company_id", $touplesToBind =  [  'company_id'=> self::$id['id'] ]  );
        $this->assertIsArray( $companyContactRows) ;
        $this->assertEquals( 0, count($companyContactRows) ) ;
        
        //je-li nastavenao cascade na representaive.company_id, smaže i zavislou radku v representative
        /** @var RepresentativeDao $representativeDao */
        $representativeDao = $this->container->get(RepresentativeDao::class);  
        $representativeRows = $representativeDao->find(  $whereClause = " company_id = :company_id", $touplesToBind =  [  'company_id'=> self::$id['id'] ]  );
        $this->assertIsArray( $representativeRows) ;
        $this->assertEquals( 0, count($representativeRows) ) ;
        //nebo take
        $representativeRow = $representativeDao->get(  [  'company_id' => self::$id['id'], 'login_login_name'  => self::$login_login_name ] ) ;
        $this->assertNull( $representativeRow );
                
        
        $this->setUp();
        $companyRow = $this->dao->get(self::$id);
        $this->assertNull($companyRow);
    }
}
