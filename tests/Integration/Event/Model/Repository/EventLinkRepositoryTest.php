<?php
declare(strict_types=1);
namespace Test\Integration\Event\Model\Repository;

use Test\AppRunner\AppRunner;
use Pes\Container\Container;
use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Dao\EventLinkPhaseDao;
use Events\Model\Dao\EventLinkDao;
use Events\Model\Repository\EventLinkRepo;
use Events\Model\Entity\EventLink;
use Events\Model\Entity\EventLinkInterface;

use Model\Repository\Exception\OperationWithLockedEntityException;
use Model\RowData\RowData;


/**
 *
 * @author pes2704
 */
class EventLinkRepositoryTest extends AppRunner {
    private $container;
    /**
     *
     * @var EventLinkRepo
     */
    private $eventLinkRepo;

    private static $eventLinkPhaseText = "proEventLinkRepoTest";    
    private static $eventLinkPhaseId;
    
    private static $eventLinkId;
    /**
     * 
     * @var EventLink
     */
    private static $eventLink2;            


    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        $container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
         // mazání - zde jen pro případ, že minulý test nebyl dokončen
        self::deleteRecords($container);        
        self::insertRecords($container);                       
    }
    
    
    private static function insertRecords( Container $container) {
          /** @var EventLinkPhaseDao $eventLinkPhaseDao */
        $eventLinkPhaseDao = $container->get(EventLinkPhaseDao::class);  
        $rowData = new RowData();
        $rowData->import([
            'text' => self::$eventLinkPhaseText ]);
        $eventLinkPhaseDao->insert($rowData);                
        self::$eventLinkPhaseId = $eventLinkPhaseDao->lastInsertIdValue();        
        
        /** @var EventLinkDao $eventLinkDao */
        $eventLinkDao = $container->get(EventLinkDao::class);  
        $rowData = new RowData();
        $rowData->import([
            'link_phase_id_fk' => self::$eventLinkPhaseId,
            'show' => true,            
            'href' =>  "https://pro" . self::$eventLinkPhaseText . "estEventLinkeeeeeeeeeeee"
        ]);
        $eventLinkDao->insert($rowData);                
        self::$eventLinkId = $eventLinkDao->lastInsertIdValue();        
       
    }

    
    private static function deleteRecords(Container $container) {
        /** @var EventLinkDao $eventLinkDao */
        $eventLinkDao = $container->get(EventLinkDao::class);
        $rows = $eventLinkDao->find( " href LIKE '". "%" . self::$eventLinkPhaseText . "%'", [] );
        foreach($rows as $row) {
            $ok = $eventLinkDao->delete($row);
        }
        
         /** @var EventLinkPhaseDao $eventLinkPhaseDao */
        $eventLinkPhaseDao = $container->get(EventLinkPhaseDao::class);        
        $rows = $eventLinkPhaseDao->find( "text LIKE '". "%" . self::$eventLinkPhaseText . "%'", [] );               
        foreach($rows as $row) {
            $ok = $eventLinkPhaseDao->delete($row);
        }                
    } 

    

    protected function setUp(): void {
        $this->container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        $this->eventLinkRepo = $this->container->get(EventLinkRepo::class);
    }
    
    
    protected function tearDown(): void {
        //$this->eventLinkRepo->flush();
        $this->eventLinkRepo->__destruct();
        
    }

    public static function tearDownAfterClass(): void {
        $container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        self::deleteRecords($container);
    }
    
    
    public function testSetUp() {
        $this->assertInstanceOf(EventLinkRepo::class, $this->eventLinkRepo);
    }
    

    public function testGetNonExisted() {
        $eventLink = $this->eventLinkRepo->get(0);
        $this->assertNull($eventLink);
    }
    
    
    public function testGetAfterSetup() {
        $eventLink = $this->eventLinkRepo->get(self::$eventLinkId);    
        $this->assertInstanceOf( EventLinkInterface::class, $eventLink);
    }


    public function testGetAndRemoveAfterSetup() {
        $eventLink = $this->eventLinkRepo->get(self::$eventLinkId);    
        $this->assertInstanceOf(EventLinkInterface::class, $eventLink);
        $this->eventLinkRepo->remove($eventLink);        
        $this->assertTrue($eventLink->isPersisted());
        $this->assertTrue($eventLink->isLocked()); 
    }

    
    public function testGetAfterRemove() {
        $eventLink = $this->eventLinkRepo->get(self::$eventLinkId);
        $this->assertNull($eventLink);
    }

   
    
    public function testAdd() {
        $eventLink = new EventLink();
        // svoboda
//        $eventLink->setShow( false );        
        $eventLink->setShow( 0 );
   
        $eventLink->setHref("https://tqwrqwz" . self::$eventLinkPhaseText  . "trrwqz.zu?aaaa=555@ddd=6546546");
        $eventLink->setLinkPhaseIdFk(self::$eventLinkPhaseId);
        $this->eventLinkRepo->add($eventLink);  //zapise hned
        
        $this->assertFalse($eventLink->isLocked());
        $this->assertTrue($eventLink->isPersisted());
        
        self::$eventLink2 = $this->eventLinkRepo->get($eventLink->getId());        

    }
    
    
    public function testReread() {        
        $eventLinkRereaded = $this->eventLinkRepo->get( self::$eventLink2->getId() );
        $this->assertInstanceOf(EventLinkInterface::class, $eventLinkRereaded);
        $this->assertTrue($eventLinkRereaded->isPersisted());
        $this->assertFalse($eventLinkRereaded->isLocked());        
    }
    

    public function testfindAll() {
        $eventLink = $this->eventLinkRepo->findAll();
        $this->assertTrue(is_array($eventLink));
    }
    
    
    public function testFind() {      
       $eventLinkArray = $this->eventLinkRepo->find( "href LIKE '%" . self::$eventLinkPhaseText . "%'", []); 
       $this->assertTrue(is_array($eventLinkArray));
       $this->assertGreaterThan(0,count($eventLinkArray)); //jsou tam minimalne 2
                       
    }


    public function testRemove_OperationWithLockedEntity() {
        $eventLink = $this->eventLinkRepo->get(self::$eventLink2->getId() );    
        $this->assertInstanceOf(EventLinkInterface::class, $eventLink);
        $this->assertTrue($eventLink->isPersisted());
        $this->assertFalse($eventLink->isLocked());
        
        $eventLink->lock();
        $this->expectException( OperationWithLockedEntityException::class);
        $this->eventLinkRepo->remove($eventLink);
    }

    
    public function testRemove() {
        $eventLink = $this->eventLinkRepo->get(self::$eventLink2->getId() );    
        $this->assertInstanceOf(EventLinkInterface::class, $eventLink);
        $this->assertTrue($eventLink->isPersisted());
        $this->assertFalse($eventLink->isLocked());

        $this->eventLinkRepo->remove($eventLink);
        
        $this->assertTrue($eventLink->isPersisted());
        $this->assertTrue($eventLink->isLocked());   // maže až při flush
       
        $this->eventLinkRepo->flush();
        //  uz neni locked
        $this->assertFalse($eventLink->isLocked());
       
        // pokus o čtení, institution uz  neni
        $eventLink = $this->eventLinkRepo->get( self::$eventLink2->getId() );
        $this->assertNull($eventLink);
    }

}
