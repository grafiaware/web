<?php
declare(strict_types=1);
namespace Test\Integration\Dao;

use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Container\EventsModelContainerConfigurator;
use Test\Integration\Event\Container\TestDbEventsContainerConfigurator;

use Events\Model\Dao\EventDao;
use Events\Model\Dao\LoginDao;
use Events\Model\Dao\EnrollDao;
use Events\Model\Dao\EventContentDao;

use Model\RowData\RowData;
use Model\RowData\PdoRowData;
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

    private static $eventPrimaryKey;
    private static $lastEventPrimaryKey; //vznikne v testFindByEventContentId

    private static $loginPrimaryKey; //pomocne
    private static $eventContentPrimaryKey; //pomocne

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(new Container())
            );

         // nový login login_name  pro TestCase
        $prefix = "ForEventDaoTest";
        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
                // prefix + uniquid - bez zamykání db
        do {
            $loginName = $prefix."_".uniqid();
            $login = $loginDao->get(['login_name' => $loginName]);
        } while ($login);
        $loginData = new RowData();
        $loginData->import(['login_name' => $loginName]);
        $loginDao->insert($loginData);
        self::$loginPrimaryKey = $loginDao->getLastInsertedPrimaryKey();

        // nový event_content - aje to id  3 Pohovor
        /** @var EventContentDao $eventContentDao */
        $eventContentDao = $container->get( EventContentDao::class);
        $rowData = new RowData();
        $rowData->import( ['title' => 'proEventDaoTest' ,
                                    'perex' => 'AAAA',
                                    'party' => 'bbbb',
                                    'event_content_type_id_fk' => 3 ] );
        $eventContentDao->insert($rowData);
        self::$eventContentPrimaryKey = $eventContentDao->getLastInsertedPrimaryKey(); //pro autoincrement
    }

    protected function setUp(): void {
        $this->container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(new Container())
            );
        $this->dao = $this->container->get(EventDao::class);  // vždy nový objekt
    }

    protected function tearDown(): void {
    }
    public static function tearDownAfterClass(): void {
        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(new Container())
            );

        //odstranit veta z event_Content, event , login
        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        $rowPdoData = new RowData(self::$loginPrimaryKey);
        $loginDao->delete($rowPdoData);

        /** @var  EventContentDao $eventContentDao  */
        $eventContentDao = $container->get( EventContentDao::class);
        $rowPdoData = new RowData(self::$eventContentPrimaryKey);
        $eventContentDao->delete($rowPdoData);

        /** @var  EventDao $eventDao  */
        $eventDao = $container->get( EventDao::class);
        $rowPdoData = new RowData(self::$lastEventPrimaryKey);
        $eventDao->delete($rowPdoData);

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
        self::$eventPrimaryKey =  $this->dao->getLastInsertedPrimaryKey();
        $this->assertGreaterThan(0, (int) self::$eventPrimaryKey[$this->dao->getAutoincrementFieldName()]);
        $numRows = $this->dao->getRowCount();
        $this->assertEquals(1, $numRows);

        //vyrobit enroll  - pro test delete
        /** @var EnrollDao $enrollDao */
        $enrollDao = $this->container->get(EnrollDao::class);
        $enrollRowData = new RowData();
        //funguje oboji - vzdy vzniknou "nova"  data
        // $enrollRowData->import( ['login_login_name_fk' => self::$login_login_name_fk,  'event_id_fk' => self::$eventIdTouple ['id']  ]);
        $enrollRowData->offsetSet( 'login_login_name_fk', self::$loginPrimaryKey['login_name']  );
        $enrollRowData->offsetSet( 'event_id_fk', self::$eventPrimaryKey ['id'] );
        $enrollDao->insert($enrollRowData);

//pomocne
        $numRowsEnroll = $enrollDao->getRowCount();
        $this->assertEquals(1, $numRowsEnroll);
        $rowEnroll = $enrollDao->get( ['login_login_name_fk' => self::$loginPrimaryKey['login_name'],  'event_id_fk' => self::$eventPrimaryKey ['id'] ] );

    }



    public function testGetExistingRow() {
        $eventRow = $this->dao->get(self::$eventPrimaryKey);
        $this->assertInstanceOf(RowDataInterface::class, $eventRow);
    }

    public function test7Columns() {
        $eventRow = $this->dao->get(self::$eventPrimaryKey);
        $this->assertCount(7, $eventRow);
    }



    public function testUpdate() {
        $eventRow = $this->dao->get(self::$eventPrimaryKey);
        $this->assertIsString( $eventRow['start']);
        $ret = $eventRow['start'];
        //
        $this->setUp();
        $retUpdated = str_replace('2011-01-01', '2011-03-03', $ret);
        $eventRow['start'] = $retUpdated;
        $this->dao->update($eventRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $eventRowRereaded = $this->dao->get(self::$eventPrimaryKey);
        $this->assertEquals($eventRow, $eventRowRereaded);
        $this->assertStringContainsString('2011-03-03', $eventRowRereaded['start']);
    }

    public function testFind() {
        $eventRow = $this->dao->find();
        $this->assertIsArray($eventRow);
        $this->assertGreaterThanOrEqual(1, count($eventRow));
        $this->assertInstanceOf(RowDataInterface::class, $eventRow[0]);
    }


    public function testFindByEventContentIdFk() {
        //vlozi dalsi vetu event, s event_content_id_fk naplnenym
        $rowData = new RowData();
        $rowData->offsetSet('published', 1);
        $rowData->offsetSet('start', "2011-01-01 15:03:01" );
        $rowData->offsetSet('end', "2011-01-02 1:00:00");

        $rowData->offsetSet('enroll_link_id_fk', null);
        $rowData->offsetSet('enter_link_id_fk', null);
        $rowData->offsetSet('event_content_id_fk', self::$eventContentPrimaryKey['id'] );

        $this->dao->insert($rowData);
        self::$lastEventPrimaryKey = $this->dao->getLastInsertedPrimaryKey();
        $this->assertGreaterThan(0, (int) self::$lastEventPrimaryKey[$this->dao->getAutoincrementFieldName()] );

        //schovat id
         $eventRow = $this->dao->get(self::$eventPrimaryKey);


        //hleda tu jednu  vetu s  event_content_id_fk
        $eventRows = $this->dao->findByEventContentIdFk(['event_content_id_fk' => self::$eventContentPrimaryKey['id'] ]);
        $this->assertIsArray($eventRows);
        $this->assertGreaterThan(0, count($eventRows));
        $this->assertEquals( 1, count($eventRows));
        $this->assertEquals( self::$eventContentPrimaryKey['id'], $eventRows[0]['event_content_id_fk'] );
        //$this->assertInstanceOf(RowDataInterface::class, $eventRows[0]);

        //schovat id
         $eventRow = $this->dao->get(self::$eventPrimaryKey);

    }



    public function testDelete() {
        $eventRow = $this->dao->get(self::$eventPrimaryKey);
        $this->dao->delete($eventRow);          //smaze prvni zapsanou
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $eventRow = $this->dao->get(self::$eventPrimaryKey);
        $this->assertNull($eventRow);


        //zkontrolovat, ze smazal i radku v enroll = hledat a nenajit
        // kontrola CASCADE
         /** @var EnrollDao $enrollDao */
        $enrollDao = $this->container->get(EnrollDao::class);
        $this->assertCount( 0 ,$enrollDao->findByEventIdFk( ['event_id_fk' => self::$eventPrimaryKey['id']  ] ) );
    }





    // test getContextConditions

}
