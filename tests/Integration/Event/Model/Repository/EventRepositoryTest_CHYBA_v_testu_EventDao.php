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

use Events\Model\Dao\EventDao;
use Events\Model\Repository\EventRepo;

use Events\Model\Entity\Event;

use Model\RowData\RowData;


/**
 *
 * @author pes2704
 */
class EventRepositoryTest extends TestCase {

    private $container;

    /**
     *
     * @var EventRepo
     */
    private $eventRepo;

    private static $id;

    private static $startTimestamp;

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
        /** @var EventDao $eventDao */
        $eventDao = $container->get(EventDao::class);
//            id,
//            published,
//            start,
//            end,
//            event_type_id_fk,
//            event_content_id_fk

        self::$startTimestamp = (new \DateTime())->format('Y-m-d H:i:s');
        $eventDao->insert([
            'published' => true,
            'start' => self::$startTimestamp,
            'end' => (new \DateTime())->modify("+24 hours")->format('Y-m-d H:i:s'),
        ]);
        self::$id = $eventDao->getLastInsertedId();
    }

    private static function deleteRecords(Container $container) {
        /** @var EventDao $eventDao */
        $eventDao = $container->get(EventDao::class);
        $eventDao->delete(['login_name'=>"testEvent"]);  // nesmysl Event namá login_name - vyhodí chybu - !! je třeba vymyslet mazání testovacích datz databáze!
    }

    protected function setUp(): void {
        $this->container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        $this->eventRepo = $this->container->get(EventRepo::class);
    }

    protected function tearDown(): void {
        $this->eventRepo->flush();
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
        $this->assertInstanceOf(EventRepo::class, $this->eventRepo);
    }

    public function testGetNonExisted() {
        $event = $this->eventRepo->get("QWER45T6U7I89OPOLKJHGFD");
        $this->assertNull($event);
    }

    public function testGetAndRemoveAfterSetup() {
        $event = $this->eventRepo->get(self::$id);    // !!!! jenom po insertu v setUp - hodnotu vrací dao
        $this->assertInstanceOf(Event::class, $event);

        $event = $this->eventRepo->remove($event);
        $this->assertNull($event);
    }

    public function testGetAfterRemove() {
        $event = $this->eventRepo->get(self::$id);
        $this->assertNull($event);
    }

    public function testAdd() {
        $event = new Event();
        $event->setPublished(true);
        $event->setStart((new\DateTime())->setDate(1999, 7, 11)->setTime(14, 55));
        $event->setEnd( ( clone $event->getStart())->modify("+1 hour"));
        $this->eventRepo->add($event);
        $this->assertTrue($event->isLocked());
    }

    public function testFindAll() {
        $events = $this->eventRepo->findAll();
        $this->assertTrue(is_array($events));
    }



//    public function testGetAfterAdd() {
//        $event = $this->eventRepo->get("XXXXXX");
//        $this->assertInstanceOf(Event::class, $event);
//        $this->assertTrue($event->getPublished());
//    }
//
//    public function testGetAndRemoveAfterAdd() {
//        $event = $this->eventRepo->get("XXXXXX");
//        $this->eventRepo->remove($event);
//        $this->assertTrue($event->isLocked(), 'Event není zamčena po remove.');
//    }
//
//    public function testAddAndReread() {
//        $event = new Event();
//        $event->setLoginName("XXXXXX");
//        $this->eventRepo->add($event);
//        $this->eventRepo->flush();
//        $event = $this->eventRepo->get($event->getLoginName());
//        $this->assertTrue($event->isPersisted(), 'Event není persistován.');
//        $this->assertTrue(is_string($event->getLoginName()));
//    }

}
