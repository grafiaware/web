<?php
declare(strict_types=1);
namespace Test\Integration\Dao;

use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Container\EventsModelContainerConfigurator;
use Test\Integration\Event\Container\TestDbEventsContainerConfigurator;

use Events\Model\Dao\InstitutionTypeDao;
use Events\Model\Dao\InstitutionDao;
use Model\RowData\RowData;
use Model\RowData\RowDataInterface;
use Pes\Database\Statement\Exception\ExecuteException;

/**
 *
 * @author pes2704
 */
class InstitutionTypeDaoTest extends AppRunner {
    private $container;

    /**
     *
     * @var InstitutionTypeDao
     */
    private $dao;

    private static $institutionTypePrimaryKey;
    private static $institutionPrimaryKey;

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
    }

    protected function setUp(): void {
        $this->container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(new Container())
            );
        $this->dao = $this->container->get(InstitutionTypeDao::class);  // vždy nový objekt
    }

    protected function tearDown(): void {
    }

    public static function tearDownAfterClass(): void {

    }

    public function testSetUp() {
        $this->assertInstanceOf(InstitutionTypeDao::class, $this->dao);

    }

      public function testInsert() {
        $rowData = new RowData();
        $rowData->offsetSet('institution_type', "testInstitutionTypeDao-tt-");
        $this->dao->insert($rowData);
        self::$institutionTypePrimaryKey =  $this->dao->getLastInsertedPrimaryKey();
        $this->assertIsArray(self::$institutionTypePrimaryKey);
        $numRows = $this->dao->getRowCount();
        $this->assertEquals(1, $numRows);

        //vyrobit Institution vetu
        /** @var InstitutionDao $institutionDao */
        $institutionDao = $this->container->get( InstitutionDao::class);
        $institutionData = new RowData();
        $institutionData->import( ['name' => 'proInstitutionTypeDaoTest'   ] );
        $institutionData->import( ['institution_type_id' => self::$institutionTypePrimaryKey['id']  ] );
        $institutionDao->insert($institutionData);
        self::$institutionPrimaryKey = $institutionDao->getLastInsertedPrimaryKey();
    }

    public function testGetExistingRow() {
        $institutionTypeRow = $this->dao->get(self::$institutionTypePrimaryKey);
        $this->assertInstanceOf(RowDataInterface::class, $institutionTypeRow);
    }

    public function test2Columns() {
        $institutionTypeRow = $this->dao->get(self::$institutionTypePrimaryKey);
        $this->assertCount(2, $institutionTypeRow);
    }

    public function testUpdate() {
        $institutionTypeRow = $this->dao->get(self::$institutionTypePrimaryKey);
        $name = $institutionTypeRow['institution_type'];
        $this->assertIsString($institutionTypeRow['institution_type']);
        //
        $this->setUp();
        $updated = str_replace('-tt-', '-tt-updated', $name);
        $institutionTypeRow['institution_type'] = $updated;
        $this->dao->update($institutionTypeRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $institutionTypeRowRereaded = $this->dao->get(self::$institutionTypePrimaryKey);
        $this->assertEquals($institutionTypeRow, $institutionTypeRowRereaded);
        $this->assertStringContainsString('-tt-updated', $institutionTypeRowRereaded['institution_type']);
    }

    public function testFind() {
        $institutionTypeRow = $this->dao->find();
        $this->assertIsArray($institutionTypeRow);
        $this->assertGreaterThanOrEqual(1, count($institutionTypeRow));
        $this->assertInstanceOf(RowDataInterface::class, $institutionTypeRow[0]);
    }

    public function testDeleteException() {
        // kontrola RESTRICT = ze nevymaže institution, zustane
        $institutionTypeRow = $this->dao->get(self::$institutionTypePrimaryKey);
        $this->expectException(ExecuteException::class);
        $this->dao->delete($institutionTypeRow);
    }

    public function testDelete() {
        /**  @var InstitutionDao  $institutionDao */
        $institutionDao = $this->container->get( InstitutionDao::class);
        $institutionRow = $institutionDao->get(self::$institutionPrimaryKey);
        $this->assertEquals(  self::$institutionTypePrimaryKey['id'], $institutionRow['institution_type_id'] );

         //smazat napred Institution
        $institutionRow = $institutionDao->get(self::$institutionPrimaryKey);
        $institutionDao->delete($institutionRow);
        $this->assertEquals(1, $institutionDao->getRowCount());

        //pak smazat InstitutionType
        //$this->setUp();
        $institutionTypeRow = $this->dao->get(self::$institutionTypePrimaryKey);
        $this->dao->delete($institutionTypeRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $institutionTypeRow = $this->dao->get(self::$institutionTypePrimaryKey) ;
        $this->assertNull($institutionTypeRow);

    }
}
