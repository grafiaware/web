<?php
declare(strict_types=1);

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Test\AppRunner\AppRunner;
use PHPUnit\Framework\TestCase;

use Pes\Container\Container;

use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Dao\EventDao;
//use Model\Dao\Exception\DaoForbiddenOperationException;
//use Model\Dao\Exception\DaoKeyVerificationFailedException;
use Model\RowData\RowData;
use Model\RowData\RowDataInterface;

/**
 *
 * @author pes2704
 */
class EventDaoTest extends AppRunner {


    private $container;

    /**
     *
     * @var EventDao
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
        $this->dao = $this->container->get(EventDao::class);  // vždy nový objekt
    }

    protected function tearDown(): void {
    }

    public static function tearDownAfterClass(): void {

    }

    public function testSetUp() {
        $this->assertInstanceOf(EventDao::class, $this->dao);

    }
    public function testInsert() {
        $rowData = new RowData();
        $rowData->offsetSet('published', 1);
        $rowData->offsetSet('start', "2011-01-01 15:03:01" );
        $rowData->offsetSet('end', "2011-01-02 1:00:00");        
        
        $rowData->offsetSet('enroll_link_id_fk', null);
        $rowData->offsetSet('enter_link_id_fk', null);
        $rowData->offsetSet('event_content_id_fk', null);
     
        $this->dao->insert($rowData);
        self::$id =  $this->dao->getLastInsertedId();
        $this->assertGreaterThan(0, (int) self::$id);
        $numRows = $this->dao->getRowCount();
        $this->assertEquals(1, $numRows);
    }



    public function testGetExistingRow() {
        $eventRow = $this->dao->get(self::$id);
        $this->assertInstanceOf(RowDataInterface::class, $eventRow);
    }

    public function test7Columns() {
        $eventRow = $this->dao->get(self::$id);
        $this->assertCount(7, $eventRow);
    }
    
    

    public function testUpdate() {
        $eventRow = $this->dao->get(self::$id);
        
        //$this->asertIsString($eventRow['title']);  #############################
        $this->assertIsString( $eventRow['start']);
        $ret = $eventRow['start'];
        //
        $this->setUp();
        $retUpdated = str_replace('2011-01-01', '2011-03-03', $ret);
        $eventRow['start'] = $retUpdated;
        $this->dao->update($eventRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $eventRowRereaded = $this->dao->get(self::$id);
        $this->assertEquals($eventRow, $eventRowRereaded);
        $this->assertContains('2011-03-03', $eventRowRereaded['start']);
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
