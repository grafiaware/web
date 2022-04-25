<?php
declare(strict_types=1);


use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Dao\EventLinkPhaseDao;
use Model\Dao\Exception\DaoForbiddenOperationException;
use Model\Dao\Exception\DaoKeyVerificationFailedException;
use Model\RowData\RowData;
use Model\RowData\RowDataInterface;

/**
 * Description of EventLinkPhaseDaoTest
 *
 * @author vlse2610
 */
class EventLinkPhaseDaoTest extends AppRunner {


    private $container;

    /**
     *
     * @var EventLinkPhaseDao
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
        $this->dao = $this->container->get(EventLinkPhaseDao::class);  // vždy nový objekt
    }

    protected function tearDown(): void {
    }

    public static function tearDownAfterClass(): void {

    }

    public function testSetUp() {
        $this->assertInstanceOf(EventLinkPhaseDao::class, $this->dao);

    }
    public function testInsert() {
        $rowData = new RowData();
        $rowData->offsetSet('text', "testEventLinkPhaseDao texxt");

        $this->dao->insert($rowData);
        self::$id =  $this->dao->getLastInsertIdTouple();
        $this->assertIsArray(self::$id);
        $numRows = $this->dao->getRowCount();
        $this->assertEquals(1, $numRows);
    }

    public function testGetExistingRow() {
        $eventLinkPhaseTypeRow = $this->dao->get(self::$id);
        $this->assertInstanceOf(RowDataInterface::class, $eventLinkPhaseTypeRow);
    }

    public function test2Columns() {
        $eventLinkPhaseTypeRow = $this->dao->get(self::$id);
        $this->assertCount(2, $eventLinkPhaseTypeRow);
    }

    public function testUpdate() {
        $eventLinkPhaseTypeRow = $this->dao->get(self::$id);
        $name = $eventLinkPhaseTypeRow['text'];
        $this->assertIsString($eventLinkPhaseTypeRow['text']);
        //
        $this->setUp();
        $updated = str_replace('texxt', '--text--', $name);
        $eventLinkPhaseTypeRow['text'] = $updated;
        $this->dao->update($eventLinkPhaseTypeRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $eventLinkPhaseTypeRowRereaded = $this->dao->get(self::$id);
        $this->assertEquals($eventLinkPhaseTypeRow, $eventLinkPhaseTypeRowRereaded);
        $this->assertContains('--text--', $eventLinkPhaseTypeRowRereaded['text']);
    }

    public function testFind() {
        $eventLinkPhaseTypeRow = $this->dao->find();
        $this->assertIsArray($eventLinkPhaseTypeRow);
        $this->assertGreaterThanOrEqual(1, count($eventLinkPhaseTypeRow));
        $this->assertInstanceOf(RowDataInterface::class, $eventLinkPhaseTypeRow[0]);
    }

    public function testDelete() {
        $eventLinkPhaseTypeRow = $this->dao->get(self::$id);
        $this->dao->delete($eventLinkPhaseTypeRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $eventLinkPhaseTypeRow = $this->dao->get(self::$id);
        $this->assertNull($eventLinkPhaseTypeRow);
    }
}

