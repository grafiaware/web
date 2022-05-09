<?php
declare(strict_types=1);


use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Dao\EventContentTypeDao;
//use Model\Dao\Exception\DaoForbiddenOperationException;
use Model\Dao\Exception\DaoKeyVerificationFailedException;
use Model\RowData\RowData;
use Model\RowData\RowDataInterface;

/**
 *
 * @author pes2704
 */
class EventContentTypeDaoTest extends AppRunner {


    private $container;

    /**
     *
     * @var EventContentTypeDao
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
        $this->dao = $this->container->get(EventContentTypeDao::class);  // vždy nový objekt
    }

    protected function tearDown(): void {
    }

    public static function tearDownAfterClass(): void {

    }

    public function testSetUp() {
        $this->assertInstanceOf(EventContentTypeDao::class, $this->dao);

    }

    public function testInsert() {
        $type =  "testEventContentType";
        self::$id =  ['type'=>$type];

        $rowData = new RowData();
        $rowData->offsetSet('type', $type);
        $rowData->offsetSet('name', "test_name_" . (string) (random_int(0, 999)));
        $this->dao->insert($rowData);
        $this->assertEquals(1, $this->dao->getRowCount());
    }

    public function testInsertDaoKeyVerificationFailedException() {
        $type =  "testEventContentType";
        $rowData = new RowData();
        $rowData->offsetSet('type', $type);
        $rowData->offsetSet('name', "test_name_" . (string) (random_int(0, 999)));
        $this->expectException(DaoKeyVerificationFailedException::class);
        $this->dao->insert($rowData);
    }

    public function testGetExistingRow() {
        $eventContentTypeRow = $this->dao->get(self::$id);
        $this->assertInstanceOf(RowDataInterface::class, $eventContentTypeRow);
    }

    public function test2Columns() {
        $eventContentTypeRow = $this->dao->get(self::$id);
        $this->assertCount(2, $eventContentTypeRow);
    }

    public function testUpdate() {
        $eventContentTypeRow = $this->dao->get(self::$id);
        $name = $eventContentTypeRow['name'];
        $this->assertIsString($eventContentTypeRow['name']);
        //
        $this->setUp();
        $updated = str_replace('test_name_', 'test_name_updated_', $name);
        $eventContentTypeRow['name'] = $updated;
        $this->dao->update($eventContentTypeRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $eventContentTypeRowRereaded = $this->dao->get(self::$id);
        $this->assertEquals($eventContentTypeRow, $eventContentTypeRowRereaded);
        $this->assertContains('test_name_updated', $eventContentTypeRowRereaded['name']);
    }

    public function testFind() {
        $eventContentTypeRow = $this->dao->find();
        $this->assertIsArray($eventContentTypeRow);
        $this->assertGreaterThanOrEqual(1, count($eventContentTypeRow));
        $this->assertInstanceOf(RowDataInterface::class, $eventContentTypeRow[0]);
    }

    public function testDelete() {
        $eventContentTypeRow = $this->dao->get(self::$id);
        $this->dao->delete($eventContentTypeRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $eventContentTypeRow = $this->dao->get(self::$id);
        $this->assertNull($eventContentTypeRow);
    }
}
