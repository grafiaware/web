<?php
declare(strict_types=1);
namespace Test\Integration\Dao;

use Test\AppRunner\AppRunner;

use Pes\Container\Container;
use Pes\Database\Statement\Exception\ExecuteException;

use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Dao\InstitutionDao;
use Events\Model\Dao\EventContentDao;
use Model\RowData\RowDataInterface;
use Model\RowData\RowData;


/**
 *
 * @author pes2704
 */
class InstitutionDaoTest extends AppRunner {
    private $container;
    /**
     *
     * @var InstitutionDao
     */
    private $dao;

    private static $idTouple;
    private static $eventContentTouple;    

    public static function setUpBeforeClass(): void {        
        self::bootstrapBeforeClass();                        
        
    }

    protected function setUp(): void {
        $this->container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        $this->dao = $this->container->get(InstitutionDao::class);  // vždy nový objekt
    }

    protected function tearDown(): void {
    }

    
    public static function tearDownAfterClass(): void {

    }

    public function testSetUp() {
        $this->assertInstanceOf(InstitutionDao::class, $this->dao);
    }
    
    
    public function testInsert() {
        $rowData = new RowData();
        $rowData->offsetSet('name', "testInstitutionDao-NNN");
        $rowData->offsetSet('institution_type_id', null );
        $this->dao->insert($rowData);
        self::$idTouple =  $this->dao->getLastInsertIdTouple();
        $this->assertIsArray(self::$idTouple);
        $numRows = $this->dao->getRowCount();
        $this->assertEquals(1, $numRows);
        
        //vyrobit EventContent vetu
       /** @var  EventContentDao $eventContentDao */
        $eventContentDao = $this->container->get(EventContentDao::class);
        $evenContentData = new RowData();
        $evenContentData->import( ['title' => 'pro InstitutionDao test',
                                   'institution_id_fk' => self::$idTouple['id']
                                  ] );
        $eventContentDao->insert($evenContentData);    
        self::$eventContentTouple = $eventContentDao->getLastInsertIdTouple();            
    }

    public function testGetExistingRow() {
        $institutionRow = $this->dao->get(self::$idTouple);
        $this->assertInstanceOf(RowDataInterface::class, $institutionRow);
    }

    public function test3Columns() {
        $institutionRow = $this->dao->get(self::$idTouple);
        $this->assertCount(3, $institutionRow);
    }

    public function testUpdate() {
        $institutionRow = $this->dao->get(self::$idTouple);
        $name = $institutionRow['name'];
        $this->assertIsString($institutionRow['name']);
        //
        $this->setUp();
        $updated = str_replace('NNN', 'NNN-updated', $name);
        $institutionRow['name'] = $updated;
        $this->dao->update($institutionRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $institutionRowRereaded = $this->dao->get(self::$idTouple);
        $this->assertEquals($institutionRow, $institutionRowRereaded);
        $this->assertStringContainsString('NNN-updated', $institutionRowRereaded['name']);
    }

    public function testFind() {
        $institutionRow = $this->dao->find();
        $this->assertIsArray($institutionRow);
        $this->assertGreaterThanOrEqual(1, count($institutionRow));
        $this->assertInstanceOf(RowDataInterface::class, $institutionRow[0]);
    }

    
     public function testDeleteException() {        
        //naplneno event_content.institution_event_fk
        $institutionRow = $this->dao->get(self::$idTouple);
        $this->expectException(ExecuteException::class);
        $this->dao->delete($institutionRow);        
    }
    
    
    public function testDelete() {
        //smazat event_content
        /** @var  EventContentDao $eventContentDao */
        $eventContentDao = $this->container->get(EventContentDao::class);
        $evenContentData = $eventContentDao->get(self::$eventContentTouple);
        $eventContentDao->delete($evenContentData);                
        
        //pak jde mazat institution
        $institutionRow = $this->dao->get(self::$idTouple);
        $this->dao->delete($institutionRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $institutionRow = $this->dao->get(self::$idTouple);
        $this->assertNull($institutionRow);
    }
}
