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

use Events\Model\Dao\EventContentDao;
use Model\Dao\Exception\DaoForbiddenOperationException;
use Model\Dao\Exception\DaoKeyVerificationFailedException;
use Model\RowData\RowData;
use Model\RowData\RowDataInterface;

/**
 *
 * @author pes2704
 */
class EventContentDaoTest extends AppRunner {


    private $container;

    /**
     *
     * @var EventContentDao
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
        $this->dao = $this->container->get(EventContentDao::class);  // vždy nový objekt
    }

    protected function tearDown(): void {
    }

    public static function tearDownAfterClass(): void {

    }

    public function testSetUp() {
        $this->assertInstanceOf(EventContentDao::class, $this->dao);

    }
    public function testInsert() {
        $rowData = new RowData();
        $rowData->offsetSet('title', "testEventContentDao-title");
        $rowData->offsetSet('perex', "testEventContentDao-perex");
        $rowData->offsetSet('party', "testEventContentDao-party");
        $rowData->offsetSet('event_content_type_type_fk', null);
        $rowData->offsetSet('institution_id_fk', null);
        $this->dao->insert($rowData);
        self::$id =  $this->dao->getLastInsertedId();
        $this->assertGreaterThan(0, (int) self::$id);
        $numRows = $this->dao->getRowCount();
        $this->assertEquals(1, $numRows);
    }

    public function testGetExistingRow() {
        $eventContentTypeRow = $this->dao->get(self::$id);
        $this->assertInstanceOf(RowDataInterface::class, $eventContentTypeRow);
    }

    public function test6Columns() {
        $eventContentTypeRow = $this->dao->get(self::$id);
        $this->assertCount(6, $eventContentTypeRow);
    }

    public function testUpdate() {
        $eventContentTypeRow = $this->dao->get(self::$id);
        $name = $eventContentTypeRow['title'];
        $this->assertIsString($eventContentTypeRow['title']);
        //
        $this->setUp();
        $updated = str_replace('-title', '-title_updated', $name);
        $eventContentTypeRow['title'] = $updated;
        $this->dao->update($eventContentTypeRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $eventContentTypeRowRereaded = $this->dao->get(self::$id);
        $this->assertEquals($eventContentTypeRow, $eventContentTypeRowRereaded);
        $this->assertContains('-title_updated', $eventContentTypeRowRereaded['title']);
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
