<?php
declare(strict_types=1);
namespace Test\Integration\Repository;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use PHPUnit\Framework\TestCase;

use Pes\Container\Container;

use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Dao\EventLinkDao;
use Events\Model\Repository\EventLinkRepo;

use Events\Model\Entity\EventLink;

use Model\RowData\RowData;

/**
 *
 * @author pes2704
 */
class EventLinkRepositoryTest extends TestCase {

    private $container;

    /**
     *
     * @var EventPresentationRepo
     */
    private $eventLinkRepo;

    private static $idTouple;

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();

        $container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );

        // mazání - zde jen pro případ, že minulý test nebyl dokončen
        self::deleteRecords($container);

        // toto je příprava testu
        /** @var EventLinkDao $eventLinkDao */
        $eventLinkDao = $container->get(EventLinkDao::class);
//            'id',
//            'show',
//            'href',
//            'link_phase_id_fk'
        $rowData = new RowData();
        $rowData->offsetSet('show', true);
        $rowData->offsetSet('href', "https://tqwrqwztrrwqz.zu?44654s6d5f46sd54f6s54f654sdf654sd65f4");
        $eventLinkDao->insert($rowData);
        self::$idTouple = $eventLinkDao->getLastInsertIdTouple();
    }

    private static function deleteRecords(Container $container) {
        /** @var EventLinkDao $eventPresentationDao */
        $eventPresentationDao = $container->get(EventLinkDao::class);
        $rowData = new RowData();
        $rowData->offsetSet('id', self::$idTouple);
        $eventPresentationDao->delete($rowData);
    }

    protected function setUp(): void {
        $this->container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        $this->eventLinkRepo = $this->container->get(EventPresentationRepo::class);
    }

    protected function tearDown(): void {
        $this->eventLinkRepo->flush();
    }

    public static function tearDownAfterClass(): void {
        $container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );

        self::deleteRecords($container);
    }

    public function testSetUp() {
        $this->assertInstanceOf(EventPresentationRepo::class, $this->eventLinkRepo);
    }

    public function testGetNonExisted() {
        $event = $this->eventLinkRepo->get(0);
        $this->assertNull($event);
    }

    public function testGetAndRemoveAfterSetup() {
        $event = $this->eventLinkRepo->get(self::$idTouple);    // !!!! jenom po insertu v setUp - hodnotu vrací dao
        $this->assertInstanceOf(EventPresentation::class, $event);

        $event = $this->eventLinkRepo->remove($event);
        $this->assertNull($event);
    }

    public function testGetAfterRemove() {
        $event = $this->eventLinkRepo->get(self::$idTouple);
        $this->assertNull($event);
    }

    public function testAdd() {

        $eventContent = new EventPresentation();
        $eventContent->setEventIdFk(null);
        $eventContent->setShow(false);
        $eventContent->setPlatform("testEventPresentation Platform test add");
        $eventContent->setUrl("https://tqwrqwztrrwqz.zu?aaaa=555@ddd=6546546");
        $this->eventLinkRepo->add($eventContent);
        $this->assertTrue($eventContent->isLocked());
    }

    public function testfindAll() {
        $eventContents = $this->eventLinkRepo->findAll();
        $this->assertTrue(is_array($eventContents));
    }

//    public function testGetAfterAdd() {
//        $event = $this->eventTypeRepo->get("XXXXXX");
//        $this->assertInstanceOf(EventPresentation::class, $event);
//        $this->assertTrue($event->getPublished());
//    }
//
//    public function testGetAndRemoveAfterAdd() {
//        $event = $this->eventTypeRepo->get("XXXXXX");
//        $this->eventTypeRepo->remove($event);
//        $this->assertTrue($event->isLocked(), 'EventPresentation není zamčena po remove.');
//    }
//
//    public function testAddAndReread() {
//        $event = new EventPresentation();
//        $event->setLoginName("XXXXXX");
//        $this->eventTypeRepo->add($event);
//        $this->eventTypeRepo->flush();
//        $event = $this->eventTypeRepo->get($event->getLoginName());
//        $this->assertTrue($event->isPersisted(), 'EventPresentation není persistován.');
//        $this->assertTrue(is_string($event->getLoginName()));
//    }

}
