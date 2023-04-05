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

    private static $eventLink1PrimaryKeyTouple;
    private static $eventLink2PrimaryKeyTouple;

    private static $eventPrimaryKeyTouple;
    private static $eventLinkPhasePrimaryKeyTouple;


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
        self::$eventLinkPhasePrimaryKeyTouple = $eventLinkPhaseDao->getLastInsertedPrimaryKey();
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

        //smazat -- id ----
        /** @var EventLinkDao $eventLinkDao */
        $eventLinkDao = $container->get(EventLinkDao::class);
        $eventRow = $eventLinkDao->get(self::$eventLink1PrimaryKeyTouple);  //PS nově mažu oba linky v testDelete
        if ($eventRow) {
            $eventLinkDao->delete($eventRow);
        }       
        //smazat -- id2 ----
        /** @var EventLinkDao $eventLinkDao */
        $eventLinkDao = $container->get(EventLinkDao::class);
        $eventRow = $eventLinkDao->get(self::$eventLink2PrimaryKeyTouple);  //PS nově mažu oba linky v testDelete
        if ($eventRow) {
            $eventLinkDao->delete($eventRow);
        }
        //smazat -- event ----
        /** @var EventDao $eventDao */
        $eventDao = $container->get(EventDao::class);
        $eventRow = $eventDao->get(self::$eventPrimaryKeyTouple);
        if ($eventRow) {
            $eventDao->delete($eventRow);
        }
        
        //smazat -- event_link_phase ----
         /** @var EventLinkPhaseDao $eventLinkPhaseDao */
        $eventLinkPhaseDao = $container->get(EventLinkPhaseDao::class);
        $eventLinkPhaseRow = $eventLinkPhaseDao->get(self::$eventLinkPhasePrimaryKeyTouple);
        if ($eventLinkPhaseRow) {
         $eventLinkPhaseDao->delete($eventLinkPhaseRow);
        }
    }


    public function testSetUp() {
        $this->assertInstanceOf(EventLinkDao::class, $this->dao);
    }


     public function testInsert() {
        //-----------------id1-------
        $rowData = new RowData();
//        $rowData->offsetSet('show', 1);            //slo by zde taky takto nastavit
//        $rowData->offsetSet('href', "EventLinkDaoTesthttpassdrooosaasdas_1");
//        $rowData->offsetSet('link_phase_id_fk', self::$eventLinkPhasePrimaryKey ['id'] ) ;
        $rowData->import( [ 'show' => 1, 
                            'href' => "EventLinkDaoTesthttpassdrooosaasdas_1", 
                            'link_phase_id_fk' => self::$eventLinkPhasePrimaryKeyTouple ['id'] ] );        
        $this->dao->insert($rowData);
        self::$eventLink1PrimaryKeyTouple =  $this->dao->getLastInsertedPrimaryKey();
        $this->assertIsArray(self::$eventLink1PrimaryKeyTouple);
        $numRows = $this->dao->getRowCount();
        $this->assertEquals(1, $numRows);

        //-----------------id2-------
        $rowData = new RowData();
//        $rowData->offsetSet('show', 2);            //slo by zde taky takto nastavit
//        $rowData->offsetSet('href', "EventLinkDaoTesthttpassdrooo_2");
//        $rowData->offsetSet('link_phase_id_fk', self::$eventLinkPhasePrimaryKey ['id'] );
        $rowData->import( [ 'show' => 2, 
                            'href' => "EventLinkDaoTesthttpassdrooo_2", 
                            'link_phase_id_fk' => self::$eventLinkPhasePrimaryKeyTouple ['id'] ] );        

        $this->dao->insert($rowData);
        self::$eventLink2PrimaryKeyTouple =  $this->dao->getLastInsertedPrimaryKey();
        $this->assertIsArray(self::$eventLink2PrimaryKeyTouple);
        $numRows = $this->dao->getRowCount();
        $this->assertEquals(1, $numRows);


        //-- pripravi vetu do tabulky event
        /** @var EventDao $eventDao */
        $eventDao = $this->container->get( EventDao::class );
        $rowData = new RowData();
//        $rowData->offsetSet('published' , 1);                         //slo by zde taky takto nastavit
//        $rowData->offsetSet('start', "2011-01-01 15:03:01" );
//        $rowData->offsetSet('end', "2011-01-02 1:00:00");
//        $rowData->offsetSet('enroll_link_id_fk', self::$eventLink1PrimaryKey['id'] );
//        $rowData->offsetSet('enter_link_id_fk', self::$eventLink2PrimaryKey['id'] );
//        $rowData->offsetSet('event_content_id_fk', null);
        $rowData->import( [ 'published' => 1, 
                            'start' => "2011-01-01 15:03:01", 
                            'end' => "2011-01-02 1:00:00",
                            'enroll_link_id_fk' => self::$eventLink1PrimaryKeyTouple['id'],
                            'enter_link_id_fk' => self::$eventLink2PrimaryKeyTouple['id'],
                            'event_content_id_fk' => null  ] );        
        
        $eventDao->insert($rowData);
        self::$eventPrimaryKeyTouple =  $eventDao->getLastInsertedPrimaryKey();
    }


    public function testGetExistingRow() {
        $eventRow = $this->dao->get(self::$eventLink1PrimaryKeyTouple);
        $this->assertInstanceOf(RowDataInterface::class, $eventRow);
    }

    public function test4Columns() {
        $eventRow = $this->dao->get(self::$eventLink1PrimaryKeyTouple);
        $this->assertCount(4, $eventRow);
    }



    public function testUpdate() {
        $eventLinkRow = $this->dao->get(self::$eventLink1PrimaryKeyTouple);
        $this->assertIsString( $eventLinkRow['href']);
        $ret = $eventLinkRow['href'];
        //
        $this->setUp();
        $retUpdated = str_replace('EventLinkDaoTest', 'nahrada-ooo-', $ret);
        $eventLinkRow['href'] = $retUpdated;
        $this->dao->update($eventLinkRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $eventRowRereaded = $this->dao->get(self::$eventLink1PrimaryKeyTouple);
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
        $eventRow = $this->dao->get(self::$eventLink1PrimaryKeyTouple);
        $this->dao->delete($eventRow);
        $this->assertEquals(1, $this->dao->getRowCount());
        $eventRow = $this->dao->get(self::$eventLink2PrimaryKeyTouple);
        $this->dao->delete($eventRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();

        $eventRow = $this->dao->get(self::$eventLink1PrimaryKeyTouple);
        $this->assertNull($eventRow);
        $eventRow = $this->dao->get(self::$eventLink2PrimaryKeyTouple);
        $this->assertNull($eventRow);

        // kontrola SET
        // kontrola, že  se deletem v event_link tabulce se  nastavilo v tabulce event  event.x_link_id_fk = null
        /** @var EventDao $eventDao */
        $eventDao = $this->container->get( EventDao::class );
        $eventData =  $eventDao->get (self::$eventPrimaryKeyTouple);
        $this->assertNull( $eventData['enroll_link_id_fk'] );
        $this->assertNull( $eventData['enter_link_id_fk'] );


        //---------------------------------------
        //smazat v event  vetu - provede tearDownAfterClass
        //smazat v eventlink 2.vetu - provede tearDownAfterClass
    }


}
