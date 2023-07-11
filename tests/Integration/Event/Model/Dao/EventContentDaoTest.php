<?php
declare(strict_types=1);
namespace Test\Integration\Dao;

use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Container\EventsModelContainerConfigurator;
use Test\Integration\Event\Container\TestDbEventsContainerConfigurator;

use Events\Model\Dao\EventContentDao;
use Model\RowData\RowData;
use Model\RowData\RowDataInterface;
use Events\Model\Dao\InstitutionDao;
use Events\Model\Dao\EventDao;
use Events\Model\Dao\EventContentTypeDao;


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

    private static $eventContenTypePrimaryKey;
    private static $eventContentPrimaryKey;
    private static $institutionPrimaryKey;
    private static $eventPrimaryKey;

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();

        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(new Container())
            );

        //tabulka institution
        /** @var InstitutionDao $institutionDao */
        $institutionDao = $container->get(InstitutionDao::class);
        $rowData = new RowData();
        $rowData->import( [ 'name' => "testEventContentDao-name" , 'institution_type_id' => null ]);
        $institutionDao->insert($rowData);
        self::$institutionPrimaryKey =  $institutionDao->getLastInsertedPrimaryKey(); //pro autoincrement
        
         //tabulka EventContentType
        /** @var EventContentTypeDao $eventContenTypeDao */
        $eventContenTypeDao = $container->get(EventContentTypeDao::class);
        $rowData1 = new RowData();
        $rowData1->import( [ 'type' => "testEventContentDao-type" , 'name' => "testEventContentDao-type" ]);
        $eventContenTypeDao->insert($rowData1);
        self::$eventContenTypePrimaryKey =  $eventContenTypeDao->getLastInsertedPrimaryKey(); //pro autoincrement



    }

    protected function setUp(): void {
        $this->container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(new Container())
            );
        $this->dao = $this->container->get(EventContentDao::class);  // vždy nový objekt
    }

    protected function tearDown(): void {
    }

    public static function tearDownAfterClass(): void {
        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(new Container())
            );

        //v event  zbývá záznam uklidit
        /** @var EventDao $eventDao */
        $eventDao = $container->get(EventDao::class);
        $eventRow = $eventDao->get(  self::$eventPrimaryKey  );
        if (isset($eventRow)) {
            $eventDao->delete($eventRow);
        }
       
        //uklidit eventContenType
        /** @var EventContentTypeDao $eventContenTypeDao */
        $eventContenTypeDao = $container->get(EventContentTypeDao::class);
        $eventContenTypeRow = $eventContenTypeDao->get(self::$eventContenTypePrimaryKey);
        if (isset($eventContenTypeRow)) {
            $eventContenTypeDao->delete($eventContenTypeRow);
        }
        
        //uklidit institution
        /** @var InstitutionDao $institutionDao */
        $institutionDao = $container->get(InstitutionDao::class);
        $institutionRow = $institutionDao->get(self::$institutionPrimaryKey);
        if (isset($institutionRow)) {
            $institutionDao->delete($institutionRow);
        }
        
    }

    public function testSetUp() {
        $this->assertInstanceOf(EventContentDao::class, $this->dao);

    }
    public function testInsert() {
        $rowData = new RowData();
        $rowData->offsetSet('title', "testEventContentDao-title");
        $rowData->offsetSet('perex', "testEventContentDao-perex");
        $rowData->offsetSet('party', "testEventContentDao-party");
        $rowData->offsetSet('event_content_type_fk', self::$eventContenTypePrimaryKey );
        $rowData->offsetSet('institution_id_fk', self::$institutionPrimaryKey ['id'] ) ;
        $this->dao->insert($rowData);
        self::$eventContentPrimaryKey =  $this->dao->getLastInsertedPrimaryKey();
        $this->assertGreaterThan(0, (int) self::$eventContentPrimaryKey);
        $numRows = $this->dao->getRowCount();
        $this->assertEquals(1, $numRows);

        //tabulka event
        /** @var EventDao $eventDao */
        $eventDao = $this->container->get( EventDao::class );
        $eventData = new RowData();
        $eventData->import( [ "published" => 1, 'event_content_id_fk' =>  self::$eventContentPrimaryKey ['id'] ]);
        $eventDao->insert($eventData);  // id je autoincrement
        self::$eventPrimaryKey = $eventDao->getLastInsertedPrimaryKey();
    }

    public function testGetExistingRow() {
        $eventContentRow = $this->dao->get(self::$eventContentPrimaryKey);
        $this->assertInstanceOf(RowDataInterface::class, $eventContentRow);
    }

    public function test6Columns() {
        $eventContentRow = $this->dao->get(self::$eventContentPrimaryKey);
        $this->assertCount(6, $eventContentRow);
    }

    public function testUpdate() {
        $eventContentRow = $this->dao->get(self::$eventContentPrimaryKey);
        $name = $eventContentRow['title'];
        $this->assertIsString($eventContentRow['title']);
        //
        $this->setUp();
        $updated = str_replace('-title', '-title_updated', $name);
        $eventContentRow['title'] = $updated;
        $this->dao->update($eventContentRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $eventContentRowRereaded = $this->dao->get(self::$eventContentPrimaryKey);
        $this->assertEquals($eventContentRow, $eventContentRowRereaded);
        $this->assertStringContainsString('-title_updated', $eventContentRowRereaded['title']) ;
    }

    public function testFind() {
        $eventContentRow = $this->dao->find();
        $this->assertIsArray($eventContentRow);
        $this->assertGreaterThanOrEqual(1, count($eventContentRow));
        $this->assertInstanceOf(RowDataInterface::class, $eventContentRow[0]);
    }

    public function testDelete() {
        $eventContentRow = $this->dao->get(self::$eventContentPrimaryKey);
        $this->dao->delete($eventContentRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $eventContentRow = $this->dao->get(self::$eventContentPrimaryKey);
        $this->assertNull($eventContentRow);

        // kontrola SET
        // zda se nastavil v event   event_content_id_fk na NULL
        /** @var EventDao $eventDao */
        $eventDao = $this->container->get(EventDao::class);
        $eventData = $eventDao->get( self::$eventPrimaryKey  );
        $this->assertNull($eventData [ 'event_content_id_fk' ]);

    }
}
