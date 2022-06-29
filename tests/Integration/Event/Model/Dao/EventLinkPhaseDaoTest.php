<?php
declare(strict_types=1);

use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Dao\EventLinkPhaseDao;
use Events\Model\Dao\EventLinkDao;
use Model\RowData\RowData;
use Model\RowData\RowDataInterface;
use Pes\Database\Statement\Exception\ExecuteException;

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

    private static $eventLinkPhaseIdTouple;
    private static $eventLinkIdTouple;


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
        self::$eventLinkPhaseIdTouple =  $this->dao->getLastInsertIdTouple();
        $this->assertIsArray(self::$eventLinkPhaseIdTouple);
        $numRows = $this->dao->getRowCount();
        $this->assertEquals(1, $numRows);
        
        //vyrobit EventLink vetu
        /** @var EventLinkDao $eventLinkDao */
        $eventLinkDao = $this->container->get( EventLinkDao::class);
        $eventLinkData = new RowData();
        $eventLinkData->import( ['show' => 1 ] );
        $eventLinkData->import( ['link_phase_id_fk' => self::$eventLinkPhaseIdTouple['id']  ] );
        $eventLinkDao->insert($eventLinkData);    
        self::$eventLinkIdTouple = $eventLinkDao->getLastInsertIdTouple();
    }

    public function testGetExistingRow() {
        $eventLinkPhaseRow = $this->dao->get(self::$eventLinkPhaseIdTouple);
        $this->assertInstanceOf(RowDataInterface::class, $eventLinkPhaseRow);
    }

    public function test2Columns() {
        $eventLinkPhaseRow = $this->dao->get(self::$eventLinkPhaseIdTouple);
        $this->assertCount(2, $eventLinkPhaseRow);
    }

    public function testUpdate() {
        $eventLinkPhaseRow = $this->dao->get(self::$eventLinkPhaseIdTouple);
        $this->assertIsString($eventLinkPhaseRow['text']);
        //
        $this->setUp();
        $updated = str_replace('texxt', '--text--',$eventLinkPhaseRow['text']);
        $eventLinkPhaseRow['text'] = $updated;
        $this->dao->update($eventLinkPhaseRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $eventLinkPhaseRowRereaded = $this->dao->get(self::$eventLinkPhaseIdTouple);
        $this->assertEquals($eventLinkPhaseRow, $eventLinkPhaseRowRereaded);
        $this->assertStringContainsString('--text--', $eventLinkPhaseRowRereaded['text']);
    }

    public function testFind() {
        $eventLinkPhaseRow = $this->dao->find();
        $this->assertIsArray($eventLinkPhaseRow);
        $this->assertGreaterThanOrEqual(1, count($eventLinkPhaseRow));
        $this->assertInstanceOf(RowDataInterface::class, $eventLinkPhaseRow[0]);
    }

    public function testDeleteException() {   
        // kontrola RESTRICT = ze nevymaže event_link, zustane
        $eventLinkPhaseRow = $this->dao->get(self::$eventLinkPhaseIdTouple);
        $this->expectException(ExecuteException::class);
        $this->dao->delete($eventLinkPhaseRow);                
    }
    
    public function testDelete() {
        /**  @var EventLinkDao  $eventLinkDao */
        $eventLinkDao = $this->container->get( EventLinkDao::class);
        $eventLinkRow = $eventLinkDao->get(self::$eventLinkIdTouple);        
        $this->assertEquals(  self::$eventLinkPhaseIdTouple['id'], $eventLinkRow['link_phase_id_fk'] );
        
         //smazat napred Institution  
        $eventLinkRow = $eventLinkDao->get(self::$eventLinkIdTouple);
        $eventLinkDao->delete($eventLinkRow);
        $this->assertEquals(1, $eventLinkDao->getRowCount());
                        
        
        //pak smazat event_link_phase
        //$this->setUp();
        $eventLinkPhaseTypeRow = $this->dao->get(self::$eventLinkPhaseIdTouple);
        $this->dao->delete($eventLinkPhaseTypeRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $eventLinkPhaseTypeRow = $this->dao->get(self::$eventLinkPhaseIdTouple);
        $this->assertNull($eventLinkPhaseTypeRow);
    }
}

