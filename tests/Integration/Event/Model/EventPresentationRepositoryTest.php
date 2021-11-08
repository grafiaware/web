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

use Events\Model\Dao\EventPresentationDao;
use Events\Model\Repository\EventPresentationRepo;

use Events\Model\Entity\EventPresentation;

/**
 *
 * @author pes2704
 */
class EventPresentationRepositoryTest extends TestCase {

    private $container;

    /**
     *
     * @var EventPresentationRepo
     */
    private $eventPresentationRepo;

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
        /** @var EventPresentationDao $eventTPresentationDao */
        $eventTPresentationDao = $container->get(EventPresentationDao::class);

        $eventTPresentationDao->insert([
            'show' => true,
            'platform' => "testEventPresentation Platform",
            'url' => "https://tqwrqwztrrwqz.zu?44654s6d5f46sd54f6s54f654sdf654sd65f4",
        ]);
        self::$id = $eventTPresentationDao->getLastInsertedId();
    }

    private static function deleteRecords(Container $container) {
        /** @var EventPresentationDao $eventPresentationDao */
        $eventPresentationDao = $container->get(EventPresentationDao::class);
        $eventPresentationDao->delete(['id'=>0]);
    }

    protected function setUp(): void {
        $this->container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        $this->eventPresentationRepo = $this->container->get(EventPresentationRepo::class);
    }

    protected function tearDown(): void {
        $this->eventPresentationRepo->flush();
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
        $this->assertInstanceOf(EventPresentationRepo::class, $this->eventPresentationRepo);
    }

    public function testGetNonExisted() {
        $event = $this->eventPresentationRepo->get(0);
        $this->assertNull($event);
    }

    public function testGetAndRemoveAfterSetup() {
        $event = $this->eventPresentationRepo->get(self::$id);    // !!!! jenom po insertu v setUp - hodnotu vrací dao
        $this->assertInstanceOf(EventPresentation::class, $event);

        $event = $this->eventPresentationRepo->remove($event);
        $this->assertNull($event);
    }

    public function testGetAfterRemove() {
        $event = $this->eventPresentationRepo->get(self::$id);
        $this->assertNull($event);
    }

    public function testAdd() {

        $eventContent = new EventPresentation();
        $eventContent->setEventIdFk(null);
        $eventContent->setShow(false);
        $eventContent->setPlatform("testEventPresentation Platform test add");
        $eventContent->setUrl("https://tqwrqwztrrwqz.zu?aaaa=555@ddd=6546546");
        $this->eventPresentationRepo->add($eventContent);
        $this->assertTrue($eventContent->isLocked());
    }

    public function testfindAll() {
        $eventContents = $this->eventPresentationRepo->findAll();
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
