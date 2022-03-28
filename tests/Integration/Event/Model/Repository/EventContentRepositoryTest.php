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

use Events\Model\Dao\EventContentDao;
use Events\Model\Repository\EventContentRepo;

use Events\Model\Entity\EventContent;

use Model\RowData\RowData;

// eventrepo
use Events\Model\Repository\EventRepo;
use Events\Model\Entity\EventInterface;
// event type repo
use Events\Model\Repository\EventTypeRepo;
use Events\Model\Entity\EventType;

/**
 *
 * @author pes2704
 */
class EventContentRepositoryTest extends TestCase {

    private $container;

    /**
     *
     * @var EventContentRepo
     */
    private $eventContentRepo;

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
        /** @var EventContentDao $eventTContentDao */
        $eventTContentDao = $container->get(EventContentDao::class);
        $rowData = new RowData();
        $rowData->offsetSet('party', "testEventContentKluk, Ťuk, Muk, Kuk, Buk, Guk");
        $rowData->offsetSet('perex', "testEventContent Přednáška kjrhrkjh rkh rktjh erůjkhlkjhlkjhg welkfh ůh ů§h §h ů§fh lůfjkhů fkjh fůsdjefhů fhsůjh ksjh ůjh ůsdhdůfh sůheůrjheů");
        $rowData->offsetSet('title', "testEventContentPřednáška");
        $eventTContentDao->insert($rowData);
        self::$id = $eventTContentDao->getLastInsertId();
    }

    private static function deleteRecords(Container $container) {
        /** @var EventContentDao $eventContentDao */
        $eventContentDao = $container->get(EventContentDao::class);
        $rowData = new RowData();
        $rowData->offsetSet('id', 0);
        $eventContentDao->delete($rowData);
    }

    protected function setUp(): void {
        $this->container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        $this->eventContentRepo = $this->container->get(EventContentRepo::class);
    }

    protected function tearDown(): void {
        $this->eventContentRepo->flush();
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
        $this->assertInstanceOf(EventContentRepo::class, $this->eventContentRepo);
    }

    public function testGetNonExisted() {
        $event = $this->eventContentRepo->get(0);
        $this->assertNull($event);
    }

    public function testGetAndRemoveAfterSetup() {
        $event = $this->eventContentRepo->get(self::$id);    // !!!! jenom po insertu v setUp - hodnotu vrací dao
        $this->assertInstanceOf(EventContent::class, $event);

        $event = $this->eventContentRepo->remove($event);
        $this->assertNull($event);
    }

    public function testGetAfterRemove() {
        $event = $this->eventContentRepo->get(self::$id);
        $this->assertNull($event);
    }

    public function testAdd() {

        $eventContent = new EventContent();
        $eventContent->setInstitutionIdFk(null);
        $eventContent->setParty("testEventContentKluk, Ťuk, Muk, Kuk, Buk, Guk");
        $eventContent->setPerex("testEventContent Přednáška kjrhrkjh rkh rktjh erůjkhlkjhlkjhg welkfh ůh ů§h §h ů§fh lůfjkhů fkjh fůsdjefhů fhsůjh ksjh ůjh ůsdhdůfh sůheůrjheů");
        $eventContent->setTitle("testEventContentPřednáška");
        $this->eventContentRepo->add($eventContent);
        $this->assertTrue($eventContent->isLocked());
    }

//    public function testFindAll() {
//        $eventContents = $this->eventContentRepo->findAll();
//        $this->assertTrue(is_array($eventContents));
//    }

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
