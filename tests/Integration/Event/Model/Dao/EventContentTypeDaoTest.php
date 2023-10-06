<?php
declare(strict_types=1);
namespace Test\Integration\Dao;

use Test\AppRunner\AppRunner;
use Container\EventsModelContainerConfigurator;
use Test\Integration\Event\Container\TestDbEventsContainerConfigurator;

use Events\Model\Dao\EventContentTypeDao;
use Events\Model\Dao\EventContentDao;

use Model\Dao\Exception\DaoKeyVerificationFailedException;
use Model\Dao\Exception\DaoParamsBindNamesMismatchException;

use Model\RowData\RowData;
use Model\RowData\RowDataInterface;
use Model\RowData\PdoRowData;

use Pes\Container\Container;
use Pes\Database\Statement\Exception\ExecuteException;

/**
 *
 * @author pes2704
 */
class EventContentTypeDaoTest extends AppRunner {
    private $container;
    /**
     *
     * @var EventContentTypeDao
     */
    private $dao;

    private static $eventContentTypeTouple;
    private static $eventContentIdTouple;
    private static $eventContenTypePrimaryKeyTouple;
    

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
    }

    protected function setUp(): void {
        $this->container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(new Container())
            );
        $this->dao = $this->container->get(EventContentTypeDao::class);  // vždy nový objekt
    }

    protected function tearDown(): void {
    }

    public static function tearDownAfterClass(): void {
        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(new Container())
        );

    //event_content uklidit -  neni treba

    }

    public function testSetUp() {
        $this->assertInstanceOf(EventContentTypeDao::class, $this->dao);

    }

    public function testInsert() {
        $type =  "testEventContentType". uniqid();
        self::$eventContentTypeTouple =  ['type'=>$type];

        $rowData = new RowData();
        $rowData->offsetSet('type', $type);
        $rowData->offsetSet('name', "test_name_" . (string) (random_int(0, 999)));
        $this->dao->insert($rowData);
        $this->assertEquals(1, $this->dao->getRowCount());
        self::$eventContenTypePrimaryKeyTouple = $this->dao->getLastInsertedPrimaryKey();


        //event_content
        /** @var EventContentDao $eventContentDao */
        $eventContentDao = $this->container->get(EventContentDao::class);
        $rowData = new RowData();
        $rowData->offsetSet('title', "testEventTypeContentDao-title");
        $rowData->offsetSet('perex', "-perex");
        $rowData->offsetSet('party', "-party");
        $rowData->offsetSet('event_content_type_id_fk', self::$eventContenTypePrimaryKeyTouple ['id'] );
        $rowData->offsetSet('institution_id_fk',null ) ;
        $eventContentDao->insert($rowData);
        self::$eventContentIdTouple =  $eventContentDao->getLastInsertedPrimaryKey(); //pro autoincrement
    }

   


    public function testGetExistingRow() {
        $eventContentTypeRow = $this->dao->get(self::$eventContentTypeTouple);
        $this->assertInstanceOf(RowDataInterface::class, $eventContentTypeRow);
    }

    public function test3Columns() {
        $eventContentTypeRow = $this->dao->get(self::$eventContentTypeTouple);
        $this->assertCount(3, $eventContentTypeRow);
    }

    public function testUpdate() {
        $eventContentTypeRow = $this->dao->get(self::$eventContentTypeTouple);
        $name = $eventContentTypeRow['name'];
        $this->assertIsString($eventContentTypeRow['name']);
        //
        $this->setUp();
        $updated = str_replace('test_name_', 'test_name_updated_', $name);
        $eventContentTypeRow['name'] = $updated;
        $this->dao->update($eventContentTypeRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $eventContentTypeRowRereaded = $this->dao->get(self::$eventContentTypeTouple);
        $this->assertEquals($eventContentTypeRow, $eventContentTypeRowRereaded);
        $this->assertStringContainsString('test_name_updated', $eventContentTypeRowRereaded['name']);
    }

    public function testFind() {
        $eventContentTypeRow = $this->dao->find();
        $this->assertIsArray($eventContentTypeRow);
        $this->assertGreaterThanOrEqual(1, count($eventContentTypeRow));
        $this->assertInstanceOf(RowDataInterface::class, $eventContentTypeRow[0]);
    }


    // Test , ze nejde smazat věta v  event_content_type, kdyz je pouzito hodnota type v event_content.event_content_type_fk
    // problem import x forcedSet
    //- nelze mazat pomoci postupu  new RowData + RowData->import!,
    //protože v RowData jsou takto vytvorena "nova" data, a  "nova" data nelze mazat  metodou ->delete!
    //- pro takovy zpusob mazaní nutno použít buď:  objekt PdoRowData a metodou ->forcedSet nastavit přslušná data jako "stará"
    //nebo postup: !napřed přečíst, pak smazat!

    //kontrola RESTRICT
    public function testDeleteException1() {
        $eventContentTypeRowPdo = new PdoRowData();
        $eventContentTypeRowPdo->forcedSet( 'type', self::$eventContentTypeTouple['type'] );
        $this->expectException(ExecuteException::class);
        $this->dao->delete($eventContentTypeRowPdo);
    }
    public function testDeleteException2() {
        $eventContentTypeRow = $this->dao->get(self::$eventContentTypeTouple);
        $this->expectException(ExecuteException::class);
        $this->dao->delete($eventContentTypeRow);
    }


    public function testDelete() {
        /** @var EventContentDao $eventContentDao */
        $eventContentDao = $this->container->get(EventContentDao::class);
        $eventContentRow = $eventContentDao->get( self::$eventContentIdTouple );
        $this->assertEquals (self::$eventContentTypeTouple['type'], $eventContentRow['event_content_type_fk']);

        //napred vymazu Content
        /** @var EventContentDao $eventContentDao */
        $eventContentRow = $eventContentDao->get( self::$eventContentIdTouple );
        $eventContentDao->delete($eventContentRow);
        $this->assertEquals(1, $eventContentDao->getRowCount());

        //pak jde smazet ContentType
        $this->setUp();  //nove dao
        $eventContentTypeRow = $this->dao->get(self::$eventContentTypeTouple);
        $this->dao->delete($eventContentTypeRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();  //nove dao
        $eventContentTypeRowRev = $this->dao->get(self::$eventContentTypeTouple);
        $this->assertNull($eventContentTypeRowRev);

    }
}
