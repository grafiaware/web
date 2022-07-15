<?php
declare(strict_types=1);
namespace Test\Integration\Repository;


use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Dao\EventContentTypeDao;
use Events\Model\Repository\EventContentTypeRepo;

use Events\Model\Entity\EventContentType;
use Model\RowData\RowData;

/**
 *
 * @author pes2704
 */
class EventContentTypeRepositoryTest extends AppRunner {
    private $container;

    /**
     *
     * @var EventContentTypeRepo
     */
    private $eventContentTypeRepo;

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        $container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(
                    (new Container( ) )  )
            );

        // mazání - zde jen pro případ, že minulý test nebyl dokončen
        self::deleteRecords($container);

        // toto je příprava testu, vlozi 1 typ
        /** @var EventContentTypeDao $eventContentTypeDao */
        $eventContentTypeDao = $container->get(EventContentTypeDao::class);
        $rowData = new RowData();
        $type =  "testEventContentType";
        $rowData->offsetSet('type', $type);
        $rowData->offsetSet('name', "testEventContentTypeName");
        $eventContentTypeDao->insert($rowData);
    }

    private static function deleteRecords(Container $container) {
        /** @var EventContentTypeDao $eventContentTypeDao */
        $eventContentTypeDao = $container->get(EventContentTypeDao::class);
        $row = $eventContentTypeDao->get(['type'=>'testEventContentType']);
        if (isset($row)) {
            $eventContentTypeDao->delete($row);
        }
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
                (new DbEventsContainerConfigurator())->configure(new Container())
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
        /** @var EventContentType $eventContentType */
        $eventContentType = $this->eventContentTypeRepo->get('testEventContentType');   
        $this->assertInstanceOf(EventContentType::class, $eventContentType);

        $eventContentType = $this->eventContentTypeRepo->remove($eventContentType);
        $this->assertNull($eventContentType);
    }
 
    
    
    public function testGetAfterRemove() {
        $eventContentType = $this->eventContentTypeRepo->get('testEventContentType');
        $this->assertNull($eventContentType);
    }
    
    

    public function testAdd() {
        /** @var EventContentType $eventContentType */
        $eventContentType = new EventContentType();
        $eventContentType->setType('testEventContentType1');
        $eventContentType->setName('testEventContentTypeName1');
        $this->eventContentTypeRepo->add($eventContentType);
        
        $this->assertFalse($eventContentType->isPersisted());  
        // neni persisted, protoze neni automaticky generovany klic, zapise se hned
        
    }
    
    public function testAddAndReread() {
        /** @var EventContentType $eventContentType */
        $eventContentType = new EventContentType();
        $eventContentType->setType('testEventContentType1');
        $eventContentType->setName('testEventContentTypeName1');
        $this->eventContentTypeRepo->add($eventContentType);

        $this->eventContentTypeRepo->flush();
        $eventContentTypeRereaded = $this->eventContentTypeRepo->get($eventContentType->getType());
        $this->assertInstanceOf(EventContentType::class, $eventContentTypeRereaded);
        
        $this->assertTrue($eventContentTypeRereaded->isPersisted());
    }
    
        
    

    public function testFindAll() {
        $eventContentTypes = $this->eventContentTypeRepo->findAll();
        $this->assertTrue(is_array($eventContentTypes));
        $this->assertGreaterThan(0,count($eventContentTypes)); //jsou tam minimalne 2

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
