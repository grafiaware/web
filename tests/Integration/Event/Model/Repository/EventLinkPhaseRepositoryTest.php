<?php
declare(strict_types=1);
namespace Test\Integration\Event\Model\Repository;

use Test\AppRunner\AppRunner;
use Pes\Container\Container;

use Container\EventsModelContainerConfigurator;
use Test\Integration\Event\Container\TestDbEventsContainerConfigurator;

use Events\Model\Dao\EventLinkPhaseDao;
use Events\Model\Repository\EventLinkPhaseRepo;
use Events\Model\Entity\EventLinkPhase;
use Events\Model\Entity\EventLinkPhaseInterface;

use Model\Repository\Exception\OperationWithLockedEntityException;
use Model\RowData\RowData;



/**
 * Description of EventLinkPhaseRepositoryTest
 *
 * @author vlse2610
 */
class EventLinkPhaseRepositoryTest extends AppRunner {
    private $container;

    /**
     *
     * @var EventLinkPhaseRepo
     */
    private $eventLinkPhaseRepo;

    private static $eventLinkPhaseId;
    private static $eventLinkPhaseText = "testEventLinkPhaseRepo";




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
        /** @var EventLinkPhaseDao $eventLinkPhaseDao */
        $eventLinkPhaseDao = $container->get(EventLinkPhaseDao::class);

        $rowData = new RowData();
        $rowData->import([
            'text' => self::$eventLinkPhaseText
        ]);
        $eventLinkPhaseDao->insert($rowData);
        self::$eventLinkPhaseId = $eventLinkPhaseDao->lastInsertIdValue();
    }

    private static function deleteRecords(Container $container) {
        /** @var EventLinkPhaseDao $eventLinkPhaseDao */
        $eventLinkPhaseDao = $container->get(EventLinkPhaseDao::class);

        $rows = $eventLinkPhaseDao->find( " text LIKE '". self::$eventLinkPhaseText . "%'", [] );
        foreach($rows as $row) {
            $ok = $eventLinkPhaseDao->delete($row);
        }
    }

    protected function setUp(): void {
        $this->container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(new Container())
            );
        $this->eventLinkPhaseRepo = $this->container->get(EventLinkPhaseRepo::class);
    }


    protected function tearDown(): void {
        //$this->institutionTypeRepo->flush();
        $this->eventLinkPhaseRepo->__destruct(); // vlastne zbytecne
    }

    public static function tearDownAfterClass(): void {
        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(new Container())
            );

        self::deleteRecords($container);
    }

    public function testSetUp() {
        $this->assertInstanceOf(EventLinkPhaseRepo::class, $this->eventLinkPhaseRepo);
    }

    public function testGetNonExisted() {
        $eventLinkPhase = $this->eventLinkPhaseRepo->get(-1);
        $this->assertNull($eventLinkPhase);
    }

    public function testGetAfterSetup() {
        $eventLinkPhase = $this->eventLinkPhaseRepo->get(self::$eventLinkPhaseId);
        $this->assertInstanceOf(EventLinkPhase::class, $eventLinkPhase);
    }


    public function testAdd() {
        $eventLinkPhase = new EventLinkPhase();
        $eventLinkPhase->setText(self::$eventLinkPhaseText);

        $this->eventLinkPhaseRepo->add($eventLinkPhase);
        $this->assertTrue($eventLinkPhase->isPersisted());  // !!!!!! EventLinkPhaseDao ma DaoEditAutoincrementKeyInterface, k zápisu dojde ihned !!!!!!!
        // pro automaticky|generovany klic (tento pripad zde ) a  pro  overovany klic  - !!! zapise se hned !!!
        $this->assertFalse($eventLinkPhase->isLocked());
    }


    public function testAddAndReread() {
        $eventLinkPhase = new EventLinkPhase();
        $eventLinkPhase->setText(self::$eventLinkPhaseText);

        $this->eventLinkPhaseRepo->add($eventLinkPhase);
        $this->eventLinkPhaseRepo->flush();

        $eventLinkPhaseRereaded = $this->eventLinkPhaseRepo->get($eventLinkPhase->getId());

        $this->assertInstanceOf(EventLinkPhaseInterface::class, $eventLinkPhaseRereaded);
        $this->assertTrue($eventLinkPhaseRereaded->isPersisted());
        $this->assertFalse($eventLinkPhaseRereaded->isLocked());
    }


    public function testFindAll() {
        $eventLinkPhaseArray = $this->eventLinkPhaseRepo->findAll();
        $this->assertTrue(is_array($eventLinkPhaseArray));
        $this->assertGreaterThan(0,count($eventLinkPhaseArray)); //jsou tam minimalne 2
    }


    public function testFind() {
        $eventLinkPhaseArray = $this->eventLinkPhaseRepo->find( " text LIKE '" . self::$eventLinkPhaseText . "%'", []);
        $this->assertTrue(is_array($eventLinkPhaseArray));
        $this->assertGreaterThan(0,count($eventLinkPhaseArray)); //jsou tam minimalne 2
    }



    public function testRemove_OperationWithLockedEntity() {
        $evntLinkPhase = $this->eventLinkPhaseRepo->get(self::$eventLinkPhaseId);
        $this->assertInstanceOf(EventLinkPhaseInterface::class, $evntLinkPhase);
        $this->assertTrue($evntLinkPhase->isPersisted());
        $this->assertFalse($evntLinkPhase->isLocked());

        $evntLinkPhase->lock();
        $this->expectException( OperationWithLockedEntityException::class);
        $this->eventLinkPhaseRepo->remove($evntLinkPhase);
    }


    public function testRemove() {
        $evntLinkPhase = $this->eventLinkPhaseRepo->get(self::$eventLinkPhaseId);
        $this->assertInstanceOf(EventLinkPhaseInterface::class, $evntLinkPhase);
        $this->assertTrue($evntLinkPhase->isPersisted());
        $this->assertFalse($evntLinkPhase->isLocked());

        $this->eventLinkPhaseRepo->remove($evntLinkPhase);

        $this->assertTrue($evntLinkPhase->isPersisted());
        $this->assertTrue($evntLinkPhase->isLocked());   // maže až při flush
        $this->eventLinkPhaseRepo->flush();
        // document uz neni locked
        $this->assertFalse($evntLinkPhase->isLocked());

        // pokus o čtení, institutionType uz  neni
        $evntLinkPhase = $this->eventLinkPhaseRepo->get(self::$eventLinkPhaseId);
        $this->assertNull($evntLinkPhase);
    }

}
