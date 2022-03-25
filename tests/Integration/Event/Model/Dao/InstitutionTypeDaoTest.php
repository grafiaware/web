<?php
declare(strict_types=1);

use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Dao\InstitutionTypeDao;
use Model\Dao\Exception\DaoForbiddenOperationException;
use Model\Dao\Exception\DaoKeyVerificationFailedException;
use Model\RowData\RowData;
use Model\RowData\RowDataInterface;

/**
 *
 * @author pes2704
 */
class InstitutionTypeDaoTest extends AppRunner {


    private $container;

    /**
     *
     * @var InstitutionTypeDao
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
        $this->dao = $this->container->get(InstitutionTypeDao::class);  // vždy nový objekt
    }

    protected function tearDown(): void {
    }

    public static function tearDownAfterClass(): void {

    }

    public function testSetUp() {
        $this->assertInstanceOf(InstitutionTypeDao::class, $this->dao);

    }
      public function testInsert() {
        $rowData = new RowData();
        //$rowData->offsetSet('id', "e");
        $rowData->offsetSet('institution_type', "testInstitutionTypeDao-tt--type");
        
        $this->dao->insert($rowData);
        self::$id =  $this->dao->getLastInsertedId();
        $this->assertGreaterThan(0, (int) self::$id);
        $numRows = $this->dao->getRowCount();
        $this->assertEquals(1, $numRows);
    }

    public function testGetExistingRow() {
        $institutionTypeRow = $this->dao->get(self::$id);
        $this->assertInstanceOf(RowDataInterface::class, $institutionTypeRow);
    }

    public function test2Columns() {
        $institutionTypeRow = $this->dao->get(self::$id);
        $this->assertCount(2, $institutionTypeRow);
    }

    public function testUpdate() {
        $institutionTypeRow = $this->dao->get(self::$id);
        $name = $institutionTypeRow['institution_type'];
        $this->assertIsString($institutionTypeRow['institution_type']);
        //
        $this->setUp();
        $updated = str_replace('-tt-', '-tt-updated', $name);
        $institutionTypeRow['institution_type'] = $updated;
        $this->dao->update($institutionTypeRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $institutionTypeRowRereaded = $this->dao->get(self::$id);
        $this->assertEquals($institutionTypeRow, $institutionTypeRowRereaded);
        $this->assertContains('-tt-updated', $institutionTypeRowRereaded['institution_type']);
    }

    public function testFind() {
        $institutionTypeRow = $this->dao->find();
        $this->assertIsArray($institutionTypeRow);
        $this->assertGreaterThanOrEqual(1, count($institutionTypeRow));
        $this->assertInstanceOf(RowDataInterface::class, $institutionTypeRow[0]);
    }

    public function testDelete() {
        $institutionTypeRow = $this->dao->get(self::$id);
        $this->dao->delete($institutionTypeRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $institutionTypeRow = $this->dao->get(self::$id);
        $this->assertNull($institutionTypeRow);
    }
}
