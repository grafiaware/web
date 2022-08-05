<?php
declare(strict_types=1);
namespace Test\Integration\Event\Model\Repository;

use Test\AppRunner\AppRunner;
use Pes\Container\Container;

use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Dao\EventDao;
use Events\Model\Repository\EventRepo;
use Events\Model\Entity\Event;
use Events\Model\Entity\EventInterface;

use Model\RowData\RowData;
use Model\Repository\Exception\OperationWithLockedEntityException;

/**
 *
 */
class EventRepositoryTest extends AppRunner {
    private $container;

    /**
     *
     * @var EventRepo
     */
    private $eventRepo;

    private static $idEvent;
    private static $startTimestamp = '2000-01-01 01:01:01' ;    
    private static $idEvent_poAdd ;
    private static $idEvent_poAddRereaded ;

    public static function setUpBeforeClass(): void {     
        self::bootstrapBeforeClass();
        $container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(
                    (new Container( )  )  )
            );

        // mazání - zde jen pro případ, že minulý test nebyl dokončen
        self::deleteRecords($container);
        
        // toto je příprava testu
        /** @var EventDao $eventDao */
        $eventDao = $container->get(EventDao::class);
        $rowData = new RowData();
        $rowData->offsetSet('published', true);
        $rowData->offsetSet('start', self::$startTimestamp  );
        //     $d=(new \DateTime())->format('Y-m-d H:i:s');                
        $rowData->offsetSet('end',   (new \DateTime())->modify("+24 hours")->format('Y-m-d H:i:s'));
        $eventDao->insert($rowData);
        self::$idEvent = $eventDao->lastInsertIdValue();
    }

    private static function deleteRecords(Container $container) {
        /** @var EventDao $eventDao */
        $eventDao = $container->get(EventDao::class);
        
        $rows = $eventDao->find( "  `start` = '" . self::$startTimestamp  . "'"   /*, [] */ );               
        foreach($rows as $row) {
            $ok = $eventDao->delete($row);
        }       
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
        $this->eventRepo->__destruct(); 
    }

    public static function tearDownAfterClass(): void {
        $container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(
                    (new Container(   ) ) )
            );
        self::deleteRecords($container);
    }

    public function testSetUp() {
        $this->assertInstanceOf(EventRepo::class, $this->eventRepo);
    }

    public function testGetNonExisted() {
        $event = $this->eventRepo->get(-1);
        $this->assertNull($event);
    }

    
    public function testGetAfterSetup() {
        $event = $this->eventRepo->get(self::$idEvent);
        $this->assertInstanceOf(EventInterface::class, $event);
        
    }

    
//    asi zbytecne
//    public function testRemoveAfterSetup() {
//        $event = $this->eventRepo->get(self::$idEvent);
//        $o = $this->eventRepo->remove($event);        
//        $this->assertNull($o);
//    }        
//    public function testGetAfterRemove() {
//        $event = $this->eventRepo->get(self::$idEvent);
//        $this->assertNull($event);
//    }
    
        
        
        

    public function testAddAndReread_I() {
        $event = new Event();
        $event->setPublished(true);
        $event->setStart( \DateTime::createFromFormat( 'Y-m-d H:i:s', self::$startTimestamp) );        //clone $event->getStart())->modify("+1 hour")
        $event->setEnd(   \DateTime::createFromFormat( 'Y-m-d H:i:s', self::$startTimestamp)->modify("+1 hour") );
        $this->eventRepo->add($event);  // zapise hned
        
        $this->assertTrue($event->isPersisted());
        
//        $event->setInstitutionIdFk(null);
//        $event->setParty("testEventContentKluk, Ťuk, Muk, Kuk, Buk, Guk");
//        $event->setPerex("testEventContent Perexůrjheů");
//        $event->setTitle("testEventContent TitlePřednáška");
               
        /** @var Event $eventRereaded */
        $eventRereaded = $this->eventRepo->get($event->getId());
        $this->assertInstanceOf( EventInterface::class, $eventRereaded);
        $this->assertTrue($eventRereaded->isPersisted());
        $this->assertFalse($eventRereaded->isLocked());
       
        //$eventRereaded, $event ... jsou odkazy na stejny objekt              
        self::$idEvent_poAdd = $event->getId();
        self::$idEvent_poAddRereaded = $eventRereaded->getId();
        $this->assertEquals(self::$idEvent_poAdd, self::$idEvent_poAddRereaded );                      
    }
    
    

    public function testAddAndReread_II() {        
        $event = $this->eventRepo->get(self::$idEvent_poAdd);
    
        /** @var  $eventContentRereaded2 */
        $eventRereaded2 = $this->eventRepo->get($event->getId());
        $this->assertInstanceOf(EventInterface::class, $eventRereaded2);
        $this->assertTrue($eventRereaded2->isPersisted());
        $this->assertFalse($eventRereaded2->isLocked());
    }    
    
    
    public function testFindAll() {
        $events = $this->eventRepo->findAll();
        $this->assertTrue(is_array($events));
    }       
    

    public function testGetAndRemoveAfterAdd() {
        $event = $this->eventRepo->get(self::$idEvent_poAdd);
        $this->eventRepo->remove($event);
        $this->assertTrue($event->isLocked());  
    }

     public function testRemove_OperationWithLockedEntity() {
        $event = $this->eventRepo->get(self::$idEvent);
        $this->assertInstanceOf( Event::class, $event);
        $this->assertTrue($event->isPersisted());
        $this->assertFalse($event->isLocked());
        
        $event->lock();
        $this->expectException( OperationWithLockedEntityException::class);
        $this->eventRepo->remove($event);
    }

    
    public function testRemove() {
        $event = $this->eventRepo->get(self::$idEvent);
        $this->assertInstanceOf(Event::class, $event);
        $this->assertTrue($event->isPersisted());
        $this->assertFalse($event->isLocked());

        $this->eventRepo->remove($event);
        
        $this->assertTrue($event->isPersisted());
        $this->assertTrue($event->isLocked());   // maže až při flush
        $this->eventRepo->flush();
        // uz neni locked
        $this->assertFalse($event->isLocked());
       
        // pokus o čtení, entita uz  neni
        $event = $this->eventRepo->get(self::$idEvent);
        $this->assertNull($event);
    }
    
    

    
    

}
