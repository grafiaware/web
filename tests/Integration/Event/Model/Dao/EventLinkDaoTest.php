<?php
declare(strict_types=1);
namespace Test\Integration\Dao;

use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Container\EventsModelContainerConfigurator;
use Test\Integration\Event\Container\TestDbEventsContainerConfigurator;

use Events\Model\Dao\EventLinkDao;
use Events\Model\Dao\EventLinkPhaseDao;

use Model\RowData\RowData;
use Model\RowData\RowDataInterface;
use Events\Model\Dao\EventDao;

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

    private static $eventLink1PrimaryKey;
    private static $eventLink2PrimaryKey;

    private static $eventPrimaryKey;
    private static $eventLinkPhasePrimaryKey;


    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(new Container())
            );

        /** @var EventLinkPhaseDao $eventLinkPhaseDao */
        $eventLinkPhaseDao = $container->get(EventLinkPhaseDao::class);
        $rowData = new RowData();
        $rowData->import( ['text' => 'Konečná fáze'   ] );
        $eventLinkPhaseDao->insert($rowData);
        self::$eventLinkPhasePrimaryKey = $eventLinkPhaseDao->getLastInsertedPrimaryKey();
    }


    protected function setUp(): void {
        $this->container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(new Container())
            );
        $this->dao = $this->container->get(EventLinkDao::class);  // vždy nový objekt
    }


    protected function tearDown(): void {
    }


    public static function tearDownAfterClass(): void {
        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(new Container())
            );


        //smazat -- id2 ----
        /** @var EventLinkDao $eventLinkDao */
        $eventLinkDao = $container->get(EventLinkDao::class);
        $eventRow = $eventLinkDao->get(self::$eventLink2PrimaryKey);  //PS nově mažu oba linky v testDelete
        $eventLinkDao->delete($eventRow);

        //smazat -- event ----
        /** @var EventDao $eventDao */
        $eventDao = $container->get(EventDao::class);
        $eventRow = $eventDao->get(self::$eventPrimaryKey);
        $eventDao->delete($eventRow);

        //smazat -- event_link_phase ----
         /** @var EventLinkPhaseDao $eventLinkPhaseDao */
        $eventLinkPhaseDao = $container->get(EventLinkPhaseDao::class);
        $eventLinkPhaseRow = $eventLinkPhaseDao->get(self::$eventLinkPhasePrimaryKey);
        $eventLinkPhaseDao->delete($eventLinkPhaseRow);

    }


    public function testSetUp() {
        $this->assertInstanceOf(EventLinkDao::class, $this->dao);
    }


     public function testInsert() {
        //-----------------id1-------
        $rowData = new RowData();
        $rowData->offsetSet('show', 1);
        $rowData->offsetSet('href', "EventLinkDaoTesthttpassdrooosaasdas_1");
        $rowData->offsetSet('link_phase_id_fk', self::$eventLinkPhasePrimaryKey ['id'] ) ;
        $this->dao->insert($rowData);
        self::$eventLink1PrimaryKey =  $this->dao->getLastInsertedPrimaryKey();
        $this->assertIsArray(self::$eventLink1PrimaryKey);
        $numRows = $this->dao->getRowCount();
        $this->assertEquals(1, $numRows);

        //-----------------id2-------
        $rowData = new RowData();
        $rowData->offsetSet('show', 2);
        $rowData->offsetSet('href', "EventLinkDaoTesthttpassdrooo_2");
        $rowData->offsetSet('link_phase_id_fk', self::$eventLinkPhasePrimaryKey ['id'] );
        $this->dao->insert($rowData);
        self::$eventLink2PrimaryKey =  $this->dao->getLastInsertedPrimaryKey();
        $this->assertIsArray(self::$eventLink2PrimaryKey);
        $numRows = $this->dao->getRowCount();
        $this->assertEquals(1, $numRows);


        //-- pripravi vetu do tabulky event
        /** @var EventDao $eventDao */
        $eventDao = $this->container->get( EventDao::class );
        $rowData = new RowData();
        $rowData->offsetSet('published', 1);
        $rowData->offsetSet('start', "2011-01-01 15:03:01" );
        $rowData->offsetSet('end', "2011-01-02 1:00:00");
        $rowData->offsetSet('enroll_link_id_fk', self::$eventLink1PrimaryKey['id'] );
        $rowData->offsetSet('enter_link_id_fk', self::$eventLink2PrimaryKey['id'] );
        $rowData->offsetSet('event_content_id_fk', null);
        $eventDao->insert($rowData);
        self::$eventPrimaryKey =  $eventDao->getLastInsertedPrimaryKey();
    }


    public function testGetExistingRow() {
        $eventRow = $this->dao->get(self::$eventLink1PrimaryKey);
        $this->assertInstanceOf(RowDataInterface::class, $eventRow);
    }

    public function test4Columns() {
        $eventRow = $this->dao->get(self::$eventLink1PrimaryKey);
        $this->assertCount(4, $eventRow);
    }



    public function testUpdate() {
        $eventLinkRow = $this->dao->get(self::$eventLink1PrimaryKey);
        $this->assertIsString( $eventLinkRow['href']);
        $ret = $eventLinkRow['href'];
        //
        $this->setUp();
        $retUpdated = str_replace('EventLinkDaoTest', 'nahrada-ooo-', $ret);
        $eventLinkRow['href'] = $retUpdated;
        $this->dao->update($eventLinkRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $eventRowRereaded = $this->dao->get(self::$eventLink1PrimaryKey);
        $this->assertEquals($eventLinkRow, $eventRowRereaded);
        $this->assertStringContainsString('nahrada-ooo-', $eventRowRereaded['href']);
    }

    public function testFind() {
        $eventLinkRow = $this->dao->find();
        $this->assertIsArray($eventLinkRow);
        $this->assertGreaterThanOrEqual(1, count($eventLinkRow));
        $this->assertInstanceOf(RowDataInterface::class, $eventLinkRow[0]);
    }

    public function testDelete() {
        $eventRow = $this->dao->get(self::$eventLink1PrimaryKey);
        $this->dao->delete($eventRow);
        $this->assertEquals(1, $this->dao->getRowCount());
        $eventRow = $this->dao->get(self::$eventLink2PrimaryKey);
        $this->dao->delete($eventRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();

        $eventRow = $this->dao->get(self::$eventLink1PrimaryKey);
        $this->assertNull($eventRow);
        $eventRow = $this->dao->get(self::$eventLink2PrimaryKey);
        $this->assertNull($eventRow);

        // kontrola SET
        // kontrola, že  se deletem v event_link tabulce se  nastavilo v tabulce event  event.x_link_id_fk = null
        /** @var EventDao $eventDao */
        $eventDao = $this->container->get( EventDao::class );
        $eventData =  $eventDao->get (self::$eventPrimaryKey);
        $this->assertNull( $eventData['enroll_link_id_fk'] );
        $this->assertNull( $eventData['enter_link_id_fk'] );


        //---------------------------------------
        //smazat v event  id2 vetu - provede tearDownAfterClass
    }


}
