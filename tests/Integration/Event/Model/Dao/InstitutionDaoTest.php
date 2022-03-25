<?php
declare(strict_types=1);


use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Dao\InstitutionDao;
use Model\Dao\Exception\DaoForbiddenOperationException;
use Model\Dao\Exception\DaoKeyVerificationFailedException;
use Model\RowData\RowData;
use Model\RowData\RowDataInterface;

/**
 *
 * @author pes2704
 */
class InstitutionDaoTest extends AppRunner {


    private $container;

    /**
     *
     * @var InstitutionDao
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
        $this->dao = $this->container->get(InstitutionDao::class);  // vždy nový objekt
    }

    protected function tearDown(): void {
    }

    public static function tearDownAfterClass(): void {

    }

    public function testSetUp() {
        $this->assertInstanceOf(InstitutionDao::class, $this->dao);

    }
      public function testInsert() {
        $rowData = new RowData();
        //$rowData->offsetSet('id', "e");
        $rowData->offsetSet('name', "testInstitutionDao-nameNNN");        
        $rowData->offsetSet('institution_type_id', null);
        $this->dao->insert($rowData);
        self::$id =  $this->dao->getLastInsertedId();
        $this->assertGreaterThan(0, (int) self::$id);
        $numRows = $this->dao->getRowCount();
        $this->assertEquals(1, $numRows);
    }

    public function testGetExistingRow() {
        $institutionRow = $this->dao->get(self::$id);
        $this->assertInstanceOf(RowDataInterface::class, $institutionRow);
    }

    public function test3Columns() {
        $institutionRow = $this->dao->get(self::$id);
        $this->assertCount(3, $institutionRow);
    }

    public function testUpdate() {
        $institutionRow = $this->dao->get(self::$id);
        $name = $institutionRow['name'];
        $this->assertIsString($institutionRow['name']);
        //
        $this->setUp();
        $updated = str_replace('NNN', 'NNN-updated', $name);
        $institutionRow['name'] = $updated;
        $this->dao->update($institutionRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $institutionRowRereaded = $this->dao->get(self::$id);
        $this->assertEquals($institutionRow, $institutionRowRereaded);
        $this->assertContains('NNN-updated', $institutionRowRereaded['name']);
    }

    public function testFind() {
        $institutionRow = $this->dao->find();
        $this->assertIsArray($institutionRow);
        $this->assertGreaterThanOrEqual(1, count($institutionRow));
        $this->assertInstanceOf(RowDataInterface::class, $institutionRow[0]);
    }

    public function testDelete() {
        $institutionRow = $this->dao->get(self::$id);
        $this->dao->delete($institutionRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $institutionRow = $this->dao->get(self::$id);
        $this->assertNull($institutionRow);
    }
}
