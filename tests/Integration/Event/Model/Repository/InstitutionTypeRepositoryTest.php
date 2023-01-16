<?php
declare(strict_types=1);
namespace Test\Integration\Event\Model\Repository;

use Test\AppRunner\AppRunner;
use Pes\Container\Container;

use Container\EventsModelContainerConfigurator;
use Test\Integration\Event\Container\TestDbEventsContainerConfigurator;

use Events\Model\Entity\InstitutionType;
use Events\Model\Entity\InstitutionTypeInterface;
use Events\Model\Dao\InstitutionTypeDao;
use Events\Model\Repository\InstitutionTypeRepo;

use Model\Repository\Exception\OperationWithLockedEntityException;
use Model\RowData\RowData;



/**
 * Description of IntegrationTypeRepositoryTest
 *
 * @author vlse2610
 */
class InstitutionTypeRepositoryTest extends AppRunner {
    private $container;

    /**
     *
     * @var InstitutionTypeRepo
     */
    private $institutionTypeRepo;

    private static $institutionTypeId;
    private static $institutionType = "testInstitutionTypeRepo";




    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(new Container())
            );

        // mazání - zde jen pro případ, že minulý test nebyl dokončen
        self::deleteRecords($container);

        self::insertRecords($container);
    }

    private static function insertRecords(Container $container) {
        /** @var InstitutionTypeDao $institutionTypeDao */
        $institutionTypeDao = $container->get(InstitutionTypeDao::class);

        $rowData = new RowData();
        $rowData->import([
            'institution_type' => self::$institutionType
        ]);
        $institutionTypeDao->insert($rowData);

        self::$institutionTypeId = $institutionTypeDao->getLastInsertedPrimaryKey()[$institutionTypeDao->getAutoincrementFieldName()];
    }

    private static function deleteRecords(Container $container) {
        /** @var InstitutionTypeDao $institutionTypeDao */
        $institutionTypeDao = $container->get(InstitutionTypeDao::class);

        $rows = $institutionTypeDao->find( "institution_type LIKE '". self::$institutionType . "%'", [] );
        foreach($rows as $row) {
            $ok = $institutionTypeDao->delete($row);
        }
    }

    protected function setUp(): void {
        $this->container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(new Container())
            );
        $this->institutionTypeRepo = $this->container->get(InstitutionTypeRepo::class);
    }

    protected function tearDown(): void {
        //$this->institutionTypeRepo->flush();
        $this->institutionTypeRepo->__destruct();
    }

    public static function tearDownAfterClass(): void {
        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(new Container())
            );

        self::deleteRecords($container);
    }

    public function testSetUp() {
        $this->assertInstanceOf(InstitutionTypeRepo::class, $this->institutionTypeRepo);
    }

    public function testGetNonExisted() {
        $institutionType = $this->institutionTypeRepo->get(-1);
        $this->assertNull($institutionType);
    }

    public function testGetAfterSetup() {
        $institutionType = $this->institutionTypeRepo->get(self::$institutionTypeId);
        $this->assertInstanceOf(InstitutionTypeInterface::class, $institutionType);
    }


    public function testAdd() {
        $institutionType = new InstitutionType();
        $institutionType->setInstitutionType(self::$institutionType);

        $this->institutionTypeRepo->add($institutionType);
        $this->assertTrue($institutionType->isPersisted());  // !!!!!! InstitutionTypeDao ma DaoEditAutoincrementKeyInterface, k zápisu dojde ihned !!!!!!!
        // pro automaticky|generovany klic (tento pripad zde ) a  pro  overovany klic  - !!! zapise se hned !!!
        $this->assertFalse($institutionType->isLocked());
    }


    public function testAddAndReread() {
        $institutionType = new InstitutionType();
        $institutionType->setInstitutionType(self::$institutionType);

        $this->institutionTypeRepo->add($institutionType);
        $this->institutionTypeRepo->flush();
        $institutionTypeRereaded = $this->institutionTypeRepo->get($institutionType->getId());
        $this->assertInstanceOf(InstitutionTypeInterface::class, $institutionTypeRereaded);
        $this->assertTrue($institutionTypeRereaded->isPersisted());
        $this->assertFalse($institutionTypeRereaded->isLocked());
    }


    public function testFindAll() {
        $institutionTypesArray = $this->institutionTypeRepo->findAll();
        $this->assertTrue(is_array($institutionTypesArray));
        $this->assertGreaterThan(0,count($institutionTypesArray)); //jsou tam minimalne 2
    }


    public function testFind() {
        $institutionTypesArray = $this->institutionTypeRepo->find( "institution_type LIKE '" . self::$institutionType . "%'", []);
        $this->assertTrue(is_array($institutionTypesArray));
        $this->assertGreaterThan(0,count($institutionTypesArray)); //jsou tam minimalne 2

    }

    public function testRemove_OperationWithLockedEntity() {
        $institutionType = $this->institutionTypeRepo->get(self::$institutionTypeId);
        $this->assertInstanceOf(InstitutionTypeInterface::class, $institutionType);
        $this->assertTrue($institutionType->isPersisted());
        $this->assertFalse($institutionType->isLocked());

        $institutionType->lock();
        $this->expectException( OperationWithLockedEntityException::class);
        $this->institutionTypeRepo->remove($institutionType);
    }


    public function testRemove() {
        $institutionType = $this->institutionTypeRepo->get(self::$institutionTypeId);
        $this->assertInstanceOf(InstitutionTypeInterface::class, $institutionType);
        $this->assertTrue($institutionType->isPersisted());
        $this->assertFalse($institutionType->isLocked());

        $this->institutionTypeRepo->remove($institutionType);

        $this->assertTrue($institutionType->isPersisted());
        $this->assertTrue($institutionType->isLocked());   // maže až při flush
        $this->institutionTypeRepo->flush();
        // document uz neni locked
        $this->assertFalse($institutionType->isLocked());

        // pokus o čtení, institutionType uz  neni
        $institutionType = $this->institutionTypeRepo->get(self::$institutionTypeId);
        $this->assertNull($institutionType);
    }

}
