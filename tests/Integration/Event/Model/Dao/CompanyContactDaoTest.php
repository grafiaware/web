<?php
declare(strict_types=1);
namespace Test\Integration\Dao;

use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Container\EventsModelContainerConfigurator;
use Test\Integration\Event\Container\TestDbEventsContainerConfigurator;

use Events\Model\Dao\CompanyContactDao;
use Events\Model\Dao\CompanyDao;
use Model\RowData\RowData;
use Model\RowData\RowDataInterface;

/**
 *
 */
class CompanyContactDaoTest extends AppRunner {
    private $container;
    /**
     *
     * @var CompanyContactDao
     */
    private $dao;

    private static $companyPrimaryKey;
    private static $id;     //treba tvorit touple dvojice

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();

        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure( (new Container(  )  ) )
            );

        // nova company
        /** @var CompanyDao $companyDao */
        $companyDao = $container->get(CompanyDao::class);

        $rowData = new RowData();
        $rowData->offsetSet('name', "CompanyName");
        $companyDao->insert($rowData);
        self::$companyPrimaryKey =  $companyDao->getLastInsertedPrimaryKey();
    }


    //----------------------------------------------------------------------------
    protected function setUp(): void {
        $this->container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(new Container())
            );
        $this->dao = $this->container->get(CompanyContactDao::class);  // vždy nový objekt
    }


    protected function tearDown(): void {
    }

    public static function tearDownAfterClass(): void {
        $container =
            (new EventsModelContainerConfigurator())->configure(
                 (new TestDbEventsContainerConfigurator())->configure(new Container())
            );
        /** @var CompanyDao $companyDao */
        $companyDao = $container->get(CompanyDao::class);
        $companyRow = $companyDao->get(self::$companyPrimaryKey);
        $companyDao->delete($companyRow);

    }

    //-------------------------------------------------------------------------
    public function testSetUp() {
        $this->assertInstanceOf(CompanyContactDao::class, $this->dao);
    }


    public function testInsert() {
        $rowData = new RowData();
        $rowData->import(
               ['company_id' => self::$companyPrimaryKey['id'],  // cizí klíč
                'name' => null,
                'phones' => null,
                'mobiles' => null,
                'emails' => null] );

        $this->dao->insert($rowData);
        self::$id =  $this->dao->getLastInsertedPrimaryKey(); //pro autoincrement
        $this->assertIsArray(self::$id);

        $numRows = $this->dao->getRowCount();
        $this->assertEquals(1, $numRows);
    }

    public function testGetExistingRow() {
        $companyContactRow = $this->dao->get(self::$id);
        $this->assertInstanceOf(RowDataInterface::class, $companyContactRow);
    }

    public function test6Columns() {
        $companyContactRow = $this->dao->get(self::$id);
        $this->assertCount(6, $companyContactRow);
    }

    public function testUpdate() {
        $companyContactRow = $this->dao->get(self::$id);
        $name = $companyContactRow['name'];
        $this->assertNull($companyContactRow['name']);
        //
        $this->setUp(); //nove dao
        $companyContactRow['name'] = 'name_updated';
        $this->dao->update($companyContactRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp(); //nove dao
        $companyContactRowRereaded = $this->dao->get(self::$id);
        $this->assertEquals($companyContactRow, $companyContactRowRereaded);
        $this->assertStringContainsString('name_updated', $companyContactRowRereaded['name']);
    }

    public function testFind() {
        $companyContactRow = $this->dao->find();
        $this->assertIsArray($companyContactRow);
        $this->assertGreaterThanOrEqual(1, count($companyContactRow));
        $this->assertInstanceOf(RowDataInterface::class, $companyContactRow[0]);
    }

    public function testDelete() {
        $companyContactRow = $this->dao->get(self::$id);
        $this->dao->delete($companyContactRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $this->dao->delete($companyContactRow);
        $this->assertEquals(0, $this->dao->getRowCount());

        $this->setUp();
        $companyContactRow = $this->dao->get(self::$id);
        $this->assertNull($companyContactRow);
    }
}
