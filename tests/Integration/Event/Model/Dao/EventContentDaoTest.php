<?php
declare(strict_types=1);

use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Dao\EventContentDao;
use Model\RowData\RowData;
use Model\RowData\RowDataInterface;
use Events\Model\Dao\InstitutionDao;

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
    private static $idInstitutionTouple;

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        
        $container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        
        //tabulka institution
        /** @var InstitutionDao $institutionDao */         
        $institutionDao = $container->get(InstitutionDao::class);
        $rowData = new RowData();        
        //$rowData->offsetSet('name', "testEventContentDao-name");
        //$rowData->offsetSet('institution_type_id', null);
        $rowData->import( [ 'name' => "testEventContentDao-name" , 'institution_type_id' => null ]);
        $institutionDao->insert($rowData);
        self::$idInstitutionTouple =  $institutionDao->getLastInsertIdTouple();
        
        
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
        $container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        //uklidit institution ?
        /** @var InstitutionDao $institutionDao */         
        $institutionDao = $container->get(InstitutionDao::class);
        $institutionRow = $institutionDao->get(self::$idInstitutionTouple);     
        $institutionDao->delete($institutionRow);
    }

    public function testSetUp() {
        $this->assertInstanceOf(EventContentDao::class, $this->dao);

    }
    public function testInsert() {
        $rowData = new RowData();
        $rowData->offsetSet('title', "testEventContentDao-title");
        $rowData->offsetSet('perex', "testEventContentDao-perex");
        $rowData->offsetSet('party', "testEventContentDao-party");
        $rowData->offsetSet('event_content_type_fk', null);
        $rowData->offsetSet('institution_id_fk', self::$idInstitutionTouple ['id'] ) ;
        $this->dao->insert($rowData);
        self::$id =  $this->dao->getLastInsertIdTouple();
        $this->assertGreaterThan(0, (int) self::$id);
        $numRows = $this->dao->getRowCount();
        $this->assertEquals(1, $numRows);
    }

    public function testGetExistingRow() {
        $eventContentRow = $this->dao->get(self::$id);
        $this->assertInstanceOf(RowDataInterface::class, $eventContentRow);
    }

    public function test6Columns() {
        $eventContentRow = $this->dao->get(self::$id);
        $this->assertCount(6, $eventContentRow);
    }

    public function testUpdate() {
        $eventContentRow = $this->dao->get(self::$id);
        $name = $eventContentRow['title'];
        $this->assertIsString($eventContentRow['title']);
        //
        $this->setUp();
        $updated = str_replace('-title', '-title_updated', $name);
        $eventContentRow['title'] = $updated;
        $this->dao->update($eventContentRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $eventContentRowRereaded = $this->dao->get(self::$id);
        $this->assertEquals($eventContentRow, $eventContentRowRereaded);
        $this->assertContains('-title_updated', $eventContentRowRereaded['title']) ;
    }

    public function testFind() {
        $eventContentRow = $this->dao->find();
        $this->assertIsArray($eventContentRow);
        $this->assertGreaterThanOrEqual(1, count($eventContentRow));
        $this->assertInstanceOf(RowDataInterface::class, $eventContentRow[0]);
    }

    public function testDelete() {
        $eventContentRow = $this->dao->get(self::$id);
        $this->dao->delete($eventContentRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $eventContentRow = $this->dao->get(self::$id);
        $this->assertNull($eventContentRow);
                
        //v institution  zbývá záznam
        /** @var InstitutionDao $institutionDao */         
        $institutionDao = $this->container->get(InstitutionDao::class);
        $institutionRow = $institutionDao->get(self::$idInstitutionTouple);        
        $this->assertInstanceOf(RowDataInterface::class, $institutionRow);
        
    }
}
