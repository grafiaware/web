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

use Events\Model\Dao\EventTypeDao;
use Events\Model\Repository\EventTypeRepo;

use Events\Model\Entity\EventType;


/**
 *
 * @author pes2704
 */
class EventTypeRepositoryTest extends TestCase {

    private $container;

    /**
     *
     * @var EventTypeRepo
     */
    private $eventTypeRepo;

    private static $id;

    public static function setUpBeforeClass(): void {
        if ( !defined('PES_DEVELOPMENT') AND !defined('PES_PRODUCTION') ) {
            define('PES_FORCE_DEVELOPMENT', 'force_development');
            //// nebo
            //define('PES_FORCE_PRODUCTION', 'force_production');

            define('PROJECT_PATH', 'c:/ApacheRoot/web/');

            include '../vendor/pes/pes/src/Bootstrap/Bootstrap.php';
        }

        $container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(
                    (new Container(
                        )
                    )
                )
            );

        // mazání - zde jen pro případ, že minulý test nebyl dokončen
        self::deleteRecords($container);

        // toto je příprava testu
        /** @var EventTypeDao $eventTypeDao */
        $eventTypeDao = $container->get(EventTypeDao::class);

        $eventTypeDao->insert([
            'value' => 'testEventType',
        ]);
        self::$id = $eventTypeDao->getLastInsertedId();
    }

    private static function deleteRecords(Container $container) {
        /** @var EventTypeDao $eventDao */
        $eventDao = $container->get(EventTypeDao::class);
        $eventDao->delete(['id'=>0]);
    }

    protected function setUp(): void {
        $this->container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        $this->eventTypeRepo = $this->container->get(EventTypeRepo::class);
    }

    protected function tearDown(): void {
        $this->eventTypeRepo->flush();
    }

    public static function tearDownAfterClass(): void {
        $container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(
                    (new Container(
                        )
                    )
                )
            );

        self::deleteRecords($container);
    }

    public function testSetUp() {
        $this->assertInstanceOf(EventTypeRepo::class, $this->eventTypeRepo);
    }

    public function testGetNonExisted() {
        $event = $this->eventTypeRepo->get(0);
        $this->assertNull($event);
    }

    public function testGetAndRemoveAfterSetup() {
        $event = $this->eventTypeRepo->get(self::$id);    // !!!! jenom po insertu v setUp - hodnotu vrací dao
        $this->assertInstanceOf(EventType::class, $event);

        $event = $this->eventTypeRepo->remove($event);
        $this->assertNull($event);
    }

    public function testGetAfterRemove() {
        $event = $this->eventTypeRepo->get(self::$id);
        $this->assertNull($event);
    }

    public function testAdd() {
        $event = new EventType();
        $event->setValue("testEventTypePřednáška");
        $this->eventTypeRepo->add($event);
        $this->assertTrue($event->isLocked());
    }

    public function testFindAll() {
        $events = $this->eventTypeRepo->findAll();
        $this->assertTrue(is_array($events));
    }

//    public function testGetAfterAdd() {
//        $event = $this->eventTypeRepo->get("XXXXXX");
//        $this->assertInstanceOf(EventType::class, $event);
//        $this->assertTrue($event->getPublished());
//    }
//
//    public function testGetAndRemoveAfterAdd() {
//        $event = $this->eventTypeRepo->get("XXXXXX");
//        $this->eventTypeRepo->remove($event);
//        $this->assertTrue($event->isLocked(), 'EventType není zamčena po remove.');
//    }
//
//    public function testAddAndReread() {
//        $event = new EventType();
//        $event->setLoginName("XXXXXX");
//        $this->eventTypeRepo->add($event);
//        $this->eventTypeRepo->flush();
//        $event = $this->eventTypeRepo->get($event->getLoginName());
//        $this->assertTrue($event->isPersisted(), 'EventType není persistován.');
//        $this->assertTrue(is_string($event->getLoginName()));
//    }

}
