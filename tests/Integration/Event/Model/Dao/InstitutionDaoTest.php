<?php
declare(strict_types=1);
namespace Test\Integration\Dao;

use Test\AppRunner\AppRunner;

use Pes\Container\Container;
use Pes\Database\Statement\Exception\ExecuteException;

use Container\EventsModelContainerConfigurator;
use Test\Integration\Event\Container\TestDbEventsContainerConfigurator;

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

    private static $institutionPrimaryKey;
    private static $eventContentPrimaryKey;

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();

    }

    protected function setUp(): void {
        $this->container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(new Container())
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
        self::$institutionPrimaryKey =  $this->dao->getLastInsertedPrimaryKey();
        $this->assertIsArray(self::$institutionPrimaryKey);
        $numRows = $this->dao->getRowCount();
        $this->assertEquals(1, $numRows);

        //vyrobit EventContent vetu
       /** @var  EventContentDao $eventContentDao */
        $eventContentDao = $this->container->get(EventContentDao::class);
        $evenContentData = new RowData();
        $evenContentData->import( ['title' => 'pro InstitutionDao test',
                                   'event_content_type_fk' => 'Pohovor ',
                                   'institution_id_fk' => self::$institutionPrimaryKey['id']
                                  ] );
        $eventContentDao->insert($evenContentData);
        self::$eventContentPrimaryKey = $eventContentDao->getLastInsertedPrimaryKey();
    }

    public function testGetExistingRow() {
        $institutionRow = $this->dao->get(self::$institutionPrimaryKey);
        $this->assertInstanceOf(RowDataInterface::class, $institutionRow);
    }

    public function test3Columns() {
        $institutionRow = $this->dao->get(self::$institutionPrimaryKey);
        $this->assertCount(3, $institutionRow);
    }

    public function testUpdate() {
        $institutionRow = $this->dao->get(self::$institutionPrimaryKey);
        $name = $institutionRow['name'];
        $this->assertIsString($institutionRow['name']);
        //
        $this->setUp();
        $updated = str_replace('NNN', 'NNN-updated', $name);
        $institutionRow['name'] = $updated;
        $this->dao->update($institutionRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $institutionRowRereaded = $this->dao->get(self::$institutionPrimaryKey);
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
        $institutionRow = $this->dao->get(self::$institutionPrimaryKey);
        $this->expectException(ExecuteException::class);
        $this->dao->delete($institutionRow);
    }


    public function testDelete() {
        //smazat event_content
        /** @var  EventContentDao $eventContentDao */
        $eventContentDao = $this->container->get(EventContentDao::class);
        $evenContentData = $eventContentDao->get(self::$eventContentPrimaryKey);
        $eventContentDao->delete($evenContentData);

        //pak jde mazat institution
        $institutionRow = $this->dao->get(self::$institutionPrimaryKey);
        $this->dao->delete($institutionRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $institutionRow = $this->dao->get(self::$institutionPrimaryKey);
        $this->assertNull($institutionRow);
    }
}
