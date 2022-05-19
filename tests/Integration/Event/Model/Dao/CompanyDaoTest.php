<?php
declare(strict_types=1);

use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Test\Integration\Event\Container\EventsContainerConfigurator;
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
    
    /**
     *
     * @var CompanyAddressDao
     */
    private $companyAddressDao;
    /**
     * 
     * @var CompanyContactDao
     */
    private $companyContactDao;
    /**
     * 
     * @var ReperesentativeDao
     */
    private $representativeDao;
    /**
     * 
     * @var LoginDao
     */
    private $loginDao;

    
    
    
    
    
    private static $id;

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        
        
        
    }

    protected function setUp(): void {
        $this->container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        $this->dao = $this->container->get(CompanyDao::class);  // vždy nový objekt
        $this->companyAddressDao = $this->container->get(CompanyAddressDao::class);  // vždy nový objekt
        $this->companyContactDao = $this->container->get(CompanyContactDao::class);  // vždy nový objekt

//        $this->representativeDao = $this->container->get(RepresentativeDao::class);  // vždy nový objekt
//        $this->loginDao = $this->container->get(LoginDao::class);  // vždy nový objekt

    }

    protected function tearDown(): void {
    }

    public static function tearDownAfterClass(): void {

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
        $rowDataCompanyAddress = new RowData();
        $rowDataCompanyAddress->import(
               ['company_id' => self::$id ['id'] ,
                'name' => 'VelkaFirma',
                'lokace' => 'Mars ',
                'psc' => '02020' ] );
        $this->companyAddressDao->insert($rowDataCompanyAddress);
        $this->assertEquals(1, $this->companyAddressDao->getRowCount());
        
        //zavisla tabulka company_contact        
        $rowDataCompanyContact = new RowData();
        $rowDataCompanyContact->import(
               ['company_id' => self::$id ['id'] ,
                'name' => 'VelkaOsoba',
                'phones' => '123456789'
                ] );
        $this->companyContactDao->insert($rowDataCompanyContact);
        $this->assertEquals(1, $this->companyContactDao->getRowCount());
        
//        //zavisla tabulka representative a  login             
//        $rowDataLogin = new RowData();
//        $rowDataLogin->import(
//               [ 'login_name' => 'VelkaOsoba'  ] );        
//        $this->LoginDao->insert($rowDataLogin);
//        $this->assertEquals(1, $this->LoginDao->getRowCount());
//        
//        $rowDataRepresentative = new RowData();
//        $rowDataRepresentative->import(
//               ['company_id' => self::$id ['id'] ,
//                'login_login_name' => 'VelkaOsoba'                
//                ] );
//        $this->representativeDao->insert($rowDataRepresentative);
//        $this->assertEquals(1, $this->representativeDao->getRowCount());
        
        
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
        $this->assertContains('NNN-updated', $companyRowRereaded['name']);
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
        //je-li nastavenao cascade na company_address.company_id, smaže i zavislou radku v company_address
        $companyAddressRow = $this->companyAddressDao->get( [  'company_id'=> self::$id['id'] ] );
        $this->assertNull($companyAddressRow);        
        //je-li nastavenao cascade na company_contact.company_id, smaže i zavislou radku v company_contact
        $companyContactRows = $this->companyContactDao->find(  $whereClause = " company_id = :company_id", $touplesToBind =  [  'company_id'=> self::$id['id'] ]  );
        $this->assertIsArray( $companyContactRows) ;
        $this->assertEquals( 0, count($companyContactRows) ) ;

        
                
        $this->setUp();
        $companyRow = $this->dao->get(self::$id);
        $this->assertNull($companyRow);
    }
}
