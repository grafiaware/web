<?php
declare(strict_types=1);

use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Dao\CompanyDao;
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
        //$rowData->offsetSet('id', "e");
        $rowData->offsetSet('name', "testCompany-nameNNN");
        $rowData->offsetSet('eventInstitutionName30', null);

        $this->dao->insert($rowData);
        self::$id = $this->dao->getLastInsertIdTouple();
        $this->assertIsArray(self::$id);
        $numRows = $this->dao->getRowCount();
        $this->assertEquals(1, $numRows);
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

        $this->setUp();
        $companyRow = $this->dao->get(self::$id);
        $this->assertNull($companyRow);
    }
}
