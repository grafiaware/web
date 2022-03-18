<?php
declare(strict_types=1);

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Dao\EnrollDao;

use Model\RowData\RowData;
use Model\RowData\RowDataInterface;

/**
 *
 * @author pes2704
 */
class EnrollDaoTest extends AppRunner {


    private $container;

    /**
     *
     * @var EnrollDao
     */
    private $dao;

    private static $id;

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
    }

    protected function setUp(): void {
        $this->container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(
                    (new Container(
                        )
                    )
                )
            );
        $this->dao = $this->container->get(EnrollDao::class);  // vždy nový objekt
    }

    protected function tearDown(): void {
    }

    public static function tearDownAfterClass(): void {

    }

    public function testSetUp() {
        $this->assertInstanceOf(EnrollDao::class, $this->dao);

    }

    public function testInsert() {
        $rowData = new RowData();
        $rowData->offsetSet('login_name', "testEnroll login name");
        $rowData->offsetSet('eventid', "test_eventid_" . (string) (1000+random_int(0, 999)));
        $this->dao->insert($rowData);
        self::$id = $this->dao->getLastInsertedId();
        $this->assertIsString(self::$id);  // lastInsertId je vždy string
        $this->assertIsInt((int) self::$id);
        $this->assertEquals(1, $this->dao->getRowCount());
    }

    public function testGetExistingRow() {
        $enrollRow = $this->dao->get(self::$id);
        $this->assertInstanceOf(RowDataInterface::class, $enrollRow);
    }

    public function test3Columns() {
        $enrollRow = $this->dao->get(self::$id);
        $this->assertCount(3, $enrollRow);
    }

    public function testUpdate() {
        $enrollRow = $this->dao->get(self::$id);
        $eventId = $enrollRow['eventid'];
        $this->assertIsInt($enrollRow['id']);
        //
        $this->setUp();
        $updated = str_replace('eventid', 'eventid_updated', $eventId);
        $enrollRow['eventid'] = $updated;
        $this->dao->update($enrollRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $enrollRowRereaded = $this->dao->get(self::$id);
        $this->assertEquals($enrollRow, $enrollRowRereaded);
        $this->assertContains('eventid_updated', $enrollRowRereaded['eventid']);

    }

    public function testFind() {
        $enrollRow = $this->dao->find();
        $this->assertIsArray($enrollRow);
        $this->assertGreaterThanOrEqual(1, count($enrollRow));
        $this->assertInstanceOf(RowDataInterface::class, $enrollRow[0]);
    }

    public function testDelete() {
        $enrollRow = $this->dao->get(self::$id);
        $this->dao->delete($enrollRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $enrollRow = $this->dao->get(self::$id);
        $this->assertNull($enrollRow);
    }
}
