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

use Events\Model\Dao\EventContentTypeDao;
use Events\Model\Repository\EventContentTypeRepo;

use Events\Model\Entity\EventContentType;


/**
 *
 * @author pes2704
 */
class EventContentTypeRepositoryTest extends TestCase {

    private $container;

    /**
     *
     * @var EventContentTypeRepo
     */
    private $eventContentTypeRepo;

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
        /** @var EventContentTypeDao $eventContentTypeDao */
        $eventContentTypeDao = $container->get(EventContentTypeDao::class);

        $eventContentTypeDao->insertWithKeyVerification([
            'type' => 'testEvCtTypeType',
            'name' => 'testEventContentTypeName',
        ]);
    }

    private static function deleteRecords(Container $container) {
        /** @var EventContentTypeDao $eventContentTypeDao */
        $eventContentTypeDao = $container->get(EventContentTypeDao::class);
        $eventContentTypeDao->delete(['type' => 'testEvCtTypeType']);
    }

    protected function setUp(): void {
        $this->container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        $this->eventContentTypeRepo = $this->container->get(EventContentTypeRepo::class);
    }

    protected function tearDown(): void {
        $this->eventContentTypeRepo->flush();
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
        $this->assertInstanceOf(EventContentTypeRepo::class, $this->eventContentTypeRepo);
    }

    public function testGetNonExisted() {
        $eventContentTypeRepo = $this->eventContentTypeRepo->get('QQfffg  ghghgh');
        $this->assertNull($eventContentTypeRepo);
    }

    public function testGetAndRemoveAfterSetup() {
        $eventContentType = $this->eventContentTypeRepo->get('testEvCtTypeType');    // !!!! jenom po insertu v setUp - hodnotu vrací dao
        $this->assertInstanceOf(EventContentType::class, $eventContentType);

        $eventContentType = $this->eventContentTypeRepo->remove($eventContentType);
        $this->assertNull($eventContentType);
    }

    public function testGetAfterRemove() {
        $eventContentType = $this->eventContentTypeRepo->get('testEvCtTypeType');
        $this->assertNull($eventContentType);
    }

    public function testAdd() {
        $eventContentType = new EventContentType();
        $eventContentType->setType('testEvCtTypeType');
        $eventContentType->setName('testEventContentTypeName');
        $this->eventContentTypeRepo->add($eventContentType);
        $this->assertTrue($eventContentType->isPersisted());  // DaoKeyDbVerifiedInterface
    }

    public function testFindAll() {
        $eventContentTypes = $this->eventContentTypeRepo->findAll();
        $this->assertTrue(is_array($eventContentTypes));
    }

//    public function testGetAfterAdd() {
//        $event = $this->eventTypeRepo->get("XXXXXX");
//        $this->assertInstanceOf(EventContentType::class, $event);
//        $this->assertTrue($event->getPublished());
//    }
//
//    public function testGetAndRemoveAfterAdd() {
//        $event = $this->eventTypeRepo->get("XXXXXX");
//        $this->eventTypeRepo->remove($event);
//        $this->assertTrue($event->isLocked(), 'EventContentType není zamčena po remove.');
//    }
//
//    public function testAddAndReread() {
//        $event = new EventContentType();
//        $event->setLoginName("XXXXXX");
//        $this->eventTypeRepo->add($event);
//        $this->eventTypeRepo->flush();
//        $event = $this->eventTypeRepo->get($event->getLoginName());
//        $this->assertTrue($event->isPersisted(), 'EventContentType není persistován.');
//        $this->assertTrue(is_string($event->getLoginName()));
//    }

}
