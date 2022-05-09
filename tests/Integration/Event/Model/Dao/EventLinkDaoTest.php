<?php
declare(strict_types=1);

use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Dao\EventLinkDao;
use Model\RowData\RowData;
use Model\RowData\RowDataInterface;

/**
 * Description of EventLinkDaoTest
 *
 * @author vlse2610
 */
class EventLinkDaoTest extends AppRunner {


    private $container;

    /**
     *
     * @var EventLinkDao
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
        $this->dao = $this->container->get(EventLinkDao::class);  // vždy nový objekt
    }

    protected function tearDown(): void {
    }
    public static function tearDownAfterClass(): void {
    }

    public function testSetUp() {
        $this->assertInstanceOf(EventLinkDao::class, $this->dao);
    }


     public function testInsert() {
        $rowData = new RowData();
        $rowData->offsetSet('show', 1);
        $rowData->offsetSet('href', "httpassdrooosaasdas");
        $rowData->offsetSet('link_phase_id_fk', null);

        $this->dao->insert($rowData);
        self::$id =  $this->dao->getLastInsertIdTouple();
        $this->assertIsArray(self::$id);
        $numRows = $this->dao->getRowCount();
        $this->assertEquals(1, $numRows);

    }



    public function testGetExistingRow() {
        $eventRow = $this->dao->get(self::$id);
        $this->assertInstanceOf(RowDataInterface::class, $eventRow);
    }

    public function test4Columns() {
        $eventRow = $this->dao->get(self::$id);
        $this->assertCount(4, $eventRow);
    }



    public function testUpdate() {
        $eventRow = $this->dao->get(self::$id);
        $this->assertIsString( $eventRow['href']);
        $ret = $eventRow['href'];
        //
        $this->setUp();
        $retUpdated = str_replace('ooo', 'nahrada-ooo', $ret);
        $eventRow['href'] = $retUpdated;
        $this->dao->update($eventRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $eventRowRereaded = $this->dao->get(self::$id);
        $this->assertEquals($eventRow, $eventRowRereaded);
        $this->assertContains('nahrada-ooo', $eventRowRereaded['href']);
    }

    public function testFind() {
        $eventRow = $this->dao->find();
        $this->assertIsArray($eventRow);
        $this->assertGreaterThanOrEqual(1, count($eventRow));
        $this->assertInstanceOf(RowDataInterface::class, $eventRow[0]);
    }

    public function testDelete() {
        $eventRow = $this->dao->get(self::$id);
        $this->dao->delete($eventRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $eventRow = $this->dao->get(self::$id);
        $this->assertNull($eventRow);
    }


}
