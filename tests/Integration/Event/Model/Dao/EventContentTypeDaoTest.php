<?php
declare(strict_types=1);


use Test\AppRunner\AppRunner;
use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

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

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
    }

    protected function setUp(): void {
        $this->container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        $this->dao = $this->container->get(EventContentTypeDao::class);  // vždy nový objekt
    }

    protected function tearDown(): void {
    }

    public static function tearDownAfterClass(): void {
        $container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
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
        
        //event_content
        /** @var EventContentDao $eventContentDao */         
        $eventContentDao = $this->container->get(EventContentDao::class);
        $rowData = new RowData();
        $rowData->offsetSet('title', "testEventTypeContentDao-title");
        $rowData->offsetSet('perex', "-perex");
        $rowData->offsetSet('party', "-party");
        $rowData->offsetSet('event_content_type_fk', $type);
        $rowData->offsetSet('institution_id_fk',null ) ;
        $eventContentDao->insert($rowData);
        self::$eventContentIdTouple =  $eventContentDao->getLastInsertIdTouple();

        
    }
    
    public function testInsertDaoKeyVerificationFailedException() {        
        $rowData = new RowData();
        $rowData->import( self::$eventContentTypeTouple);
        $rowData->offsetSet('name', "name_pro testContenTypeDao" );        
        $this->expectException(DaoKeyVerificationFailedException::class);
        $this->dao->insert($rowData);
    }

    
    public function testGetExistingRow() {
        $eventContentTypeRow = $this->dao->get(self::$eventContentTypeTouple);
        $this->assertInstanceOf(RowDataInterface::class, $eventContentTypeRow);
    }

    public function test2Columns() {
        $eventContentTypeRow = $this->dao->get(self::$eventContentTypeTouple);
        $this->assertCount(2, $eventContentTypeRow);
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
    
    
    // Test , ze nejde smazat věta v  event_content_type, kdyz je pouzito type v event_content.event_content_type_fk
    // problem import x forcedSet
    public function testDeleteException() {
        //nelze mazat pomoci new RowData + RowData->import!
        //protože v RowData jsou pak "nova" data, a  "nova" data nelze mazat  metodou ->delete!
        //pro takovy zpusob mazaní nutno použít buď:  objekt PdoRowData a metodou ->forcedSet nastavit přříslušná data jako "stará"
        //nebo postup: napřed přečíst, pak smazat
        
        $eventContentTypeRowPdo = new PdoRowData();
        $eventContentTypeRowPdo->forcedSet( 'type', self::$eventContentTypeTouple['type'] );
        $this->expectException(ExecuteException::class);
        $this->dao->delete($eventContentTypeRowPdo);
        
        $eventContentTypeRow = $this->dao->get(self::$eventContentTypeTouple);
        $this->expectException(ExecuteException::class);
        $this->dao->delete($eventContentTypeRow);

    }
    
    public function testDelete() {
        
        //kontrola RESTRICT       
        $eventContentTypeRow = $this->dao->get(self::$eventContentTypeTouple);
        $this->expectException(ExecuteException::class);
        $this->dao->delete($eventContentTypeRow);
        
        /** @var EventContentDao $eventContentDao */         
        $eventContentDao = $this->container->get(EventContentDao::class);
        $eventContentRow = $eventContentDao->get( self::$eventContentIdTouple );
        $this->assertEquals (self::$eventContentTypeTouple['type'], $eventContentRow['event_content_type_fk']);
        
        
        
        
        //napred vymazu Content
        /** @var EventContentDao $eventContentDao */         
        $eventContentDao = $this->container->get(EventContentDao::class);
        $eventContentRow = $eventContentDao->get( self::$eventContentIdTouple );
        $eventContentDao->delete($eventContentRow);                
        
        //pak jde smazet ContentType
        $eventContentTypeRow = $this->dao->get(self::$eventContentTypeTouple);
        $this->dao->delete($eventContentTypeRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $eventContentTypeRowRev = $this->dao->get(self::$eventContentTypeTouple);
        $this->assertNull($eventContentTypeRowRev);
                
    }
}
