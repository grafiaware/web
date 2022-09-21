<?php
declare(strict_types=1);
namespace Test\Integration\Dao;

use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Test\Integration\Event\Container\EventsModelContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Model\RowData\RowData;
use Model\RowData\RowDataInterface;

use Events\Model\Dao\CompanyDao;
use Events\Model\Dao\CompanyAddressDao;

/**
 *
 */
class CompanyAddressDaoTest  extends AppRunner {
    private $container;
    /**
     *
     * @var CompanyAddressDao
     */
    private $dao;       
    
    private static $company_id_addressTouple;
    private static $company_id2_addressTouple;
    private static $prefix = 'CompanyAddressTest ';

    
    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure( (new Container( ) ) ) );

        // nova company - priprava potrebne propojene tabulky
        /** @var CompanyDao $companyDao */
        $companyDao = $container->get(CompanyDao::class);

        $rowData = new RowData();
        $rowData->offsetSet('name', self::$prefix."Company1");
        $rowData->offsetSet('eventInstitutionName30', 'ShortyCo.');
        $companyDao->insert($rowData);
        self::$company_id_addressTouple =  ['company_id' => $companyDao->lastInsertIdValue()];

        $rowData = new RowData();
        $rowData->offsetSet('name', self::$prefix."Company2");
        $rowData->offsetSet('eventInstitutionName30', 'Circus.');
        $companyDao->insert($rowData);
        self::$company_id2_addressTouple =  ['company_id' => $companyDao->lastInsertIdValue()];
    }

    //-------------------------------------------------------------------------
    protected function setUp(): void {
        $this->container =
            (new EventsModelContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(  (new Container(  )  ) ) );
        
        $this->dao = $this->container->get(CompanyAddressDao::class);  // vždy nový objekt
    }

    protected function tearDown(): void {
    }

    public static function tearDownAfterClass(): void {  
        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        /** @var CompanyDao $companyDao */
        $companyDao = $container->get(CompanyDao::class);        
        $companyRow = $companyDao->get( ['id' => self::$company_id_addressTouple ['company_id']  ]);
        $companyDao->delete($companyRow);
        $companyRow = $companyDao->get( ['id' => self::$company_id2_addressTouple ['company_id'] ]);
        $companyDao->delete($companyRow);
    }

    public function testSetUp() {
        $this->assertInstanceOf(CompanyAddressDao::class, $this->dao);

    }

    public function testInsert() {
        $rowData = new RowData();
        $rowData->import(
               ['company_id' => self::$company_id_addressTouple['company_id'],
                'name' => self::$prefix.'VelkaFirma',
                'lokace' => 'Mars ',
                'psc' => '02020' ] );
        $this->dao->insert($rowData);
        $this->assertEquals(1, $this->dao->getRowCount());
    }

    public function testGetExistingRow() {
        $companyAddressRow = $this->dao->get(self::$company_id_addressTouple);
        $this->assertInstanceOf(RowDataInterface::class, $companyAddressRow);
    }

    public function test5Columns() {
        $companyAddressRow = $this->dao->get(self::$company_id_addressTouple);
        $this->assertCount(5, $companyAddressRow);
    }

    public function testUpdate() {
        $companyAddressRow = $this->dao->get(self::$company_id_addressTouple);
        $companyAddressId = $companyAddressRow['company_id'];
        $this->assertIsInt($companyAddressRow['company_id']);
        //
        $this->setUp(); //nove dao
        $companyAddressRow['name'] = "mala firma";
        $this->dao->update($companyAddressRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp(); //nove dao
        $rowRereaded = $this->dao->get(self::$company_id_addressTouple);
        $this->assertEquals("mala firma", $rowRereaded['name']);
    }

    
    public function testFind() {
        $companyAddressRowArray = $this->dao->find();
        $this->assertIsArray($companyAddressRowArray);
        $this->assertGreaterThanOrEqual(1, count($companyAddressRowArray));
        $this->assertInstanceOf(RowDataInterface::class, $companyAddressRowArray[0]);
    }

    public function testDelete() {
        $companyAddressRow = $this->dao->get(self::$company_id_addressTouple);
        $this->dao->delete($companyAddressRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $this->dao->delete($companyAddressRow);
        $this->assertEquals(0, $this->dao->getRowCount());

        $this->setUp();
        $companyAddressRow = $this->dao->get(self::$company_id_addressTouple);
        $this->assertNull($companyAddressRow);

    }
}
