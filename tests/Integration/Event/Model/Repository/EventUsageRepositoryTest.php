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

use Test\Integration\Event\Container\EventsModelContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Dao\EventContentDao;

use Model\RowData\RowData;


// eventrepo
use Events\Model\Repository\EventRepo;
use Events\Model\Entity\EventInterface;
// event content repo
use Events\Model\Repository\EventLinkRepo;
use Events\Model\Entity\EventContentInterface;
// event content type repo
use Events\Model\Repository\EventContentTypeRepo;
use Events\Model\Entity\EventContentTypeInterface;




/**
 *
 * @author pes2704
 */
class EventUsageRepositoryTest extends TestCase {

    private $container;

    /**
     *
     * @var EventRepo
     */
    private $eventRepo;

    /**
     *
     * @var EventTypeRepo
     */
    private $eventTypeRepo;

    /**
     *
     * @var EventLinkRepo
     */
    private $eventContentRepo;

    /**
     *
     * @var EventContentTypeRepo
     */
    private $eventContentTypeRepo;


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

//        $container =
//            (new EventsContainerConfigurator())->configure(
//                (new DbEventsContainerConfigurator())->configure(
//                    (new Container(
//                        )
//                    )
//                )
//            );
//
//        // mazání - zde jen pro případ, že minulý test nebyl dokončen
//        self::deleteRecords($container);
//
//        // toto je příprava testu
//        /** @var EventContentDao $eventTContentDao */
//        $eventTContentDao = $container->get(EventContentDao::class);
//
//        $eventTContentDao->insert([
//            'party' => "testEventContentKluk, Ťuk, Muk, Kuk, Buk, Guk",
//            'perex' => "testEventContent Přednáška kjrhrkjh rkh rktjh erůjkhlkjhlkjhg welkfh ůh ů§h §h ů§fh lůfjkhů fkjh fůsdjefhů fhsůjh ksjh ůjh ůsdhdůfh sůheůrjheů",
//            'title' => "testEventContentPřednáška",
//        ]);
//        self::$id = $eventTContentDao->getLastInsertId();
    }

    private static function deleteRecords(Container $container) {
        /** @var EventContentDao $eventContentDao */
        $eventContentDao = $container->get(EventContentDao::class);
        $eventContentDao->delete(['id'=>0]);
    }

    protected function setUp(): void {
        $this->container =
            (new EventsModelContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        $this->eventContentRepo = $this->container->get(EventLinkRepo::class);
    }

    protected function tearDown(): void {
        $this->eventContentRepo->flush();
    }

    public static function tearDownAfterClass(): void {
//        $container =
//            (new EventsContainerConfigurator())->configure(
//                (new DbEventsContainerConfigurator())->configure(
//                    (new Container(
//                        )
//                    )
//                )
//            );
//
//        self::deleteRecords($container);
    }

    public function testSetUp() {
        $this->assertInstanceOf(EventLinkRepo::class, $this->eventContentRepo);
    }

    public function testUpdateEventsWithEventContents() {

        $this->eventRepo = $this->container->get(EventRepo::class);
        $events = $this->eventRepo->findAll();
        $this->assertTrue(is_array($events));

        $this->eventContentRepo = $this->container->get(EventLinkRepo::class);
        $eventContents = $this->eventContentRepo->findAll();
        $this->assertTrue(is_array($eventContents));

        $this->eventContentTypeRepo = $this->container->get(EventContentTypeRepo::class);
        $eventContentTypes = $this->eventContentTypeRepo->findAll();
        $this->assertTrue(is_array($eventContentTypes));

        $evContentCounter = 0;
        $evContentsCount = count($eventContents);

        $evtypeCounter = 0;
        $evTypesCount = count($eventContentTypes);

        foreach ($events as $event) {
            /** @var EventContentInterface $evContent */
            $evContent = $eventContents[($evContentCounter % $evContentsCount)];
            $evContentCounter++;
            /** @var EventContentTypeInterface $evType */
            $evType = $eventContentTypes[($evtypeCounter % $evTypesCount)];
            $evtypeCounter++;
            $event->setEventContentIdFk($evContent->getId());
            $evContent->setEventContentTypeFk($evType->getType());
        }
        $this->assertTrue($evContentCounter>2);
    }

//    public function testGetAfterAdd() {
//        $event = $this->eventTypeRepo->get("XXXXXX");
//        $this->assertInstanceOf(EventContent::class, $event);
//        $this->assertTrue($event->getPublished());
//    }
//
//    public function testGetAndRemoveAfterAdd() {
//        $event = $this->eventTypeRepo->get("XXXXXX");
//        $this->eventTypeRepo->remove($event);
//        $this->assertTrue($event->isLocked(), 'EventContent není zamčena po remove.');
//    }
//
//    public function testAddAndReread() {
//        $event = new EventContent();
//        $event->setLoginName("XXXXXX");
//        $this->eventTypeRepo->add($event);
//        $this->eventTypeRepo->flush();
//        $event = $this->eventTypeRepo->get($event->getLoginName());
//        $this->assertTrue($event->isPersisted(), 'EventContent není persistován.');
//        $this->assertTrue(is_string($event->getLoginName()));
//    }

}
