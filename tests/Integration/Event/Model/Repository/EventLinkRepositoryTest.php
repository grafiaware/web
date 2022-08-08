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

    private static $eventLinkPhaseText = "proEventLinkTest";    
    private static $eventLinkPhaseId;
    
    private static $eventLinkId;
        
    
    
    private static $idI;
    private static $idR;
    


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
            'href' =>  "https://pro" . self::$eventLinkPhaseText . "estEventLinkccaas54f654sdf654sd65f4"
        ]);
        $eventLinkDao->insert($rowData);                
        self::$eventLinkId = $eventLinkDao->lastInsertIdValue();        
        //------------------------------                                
    }

    
    private static function deleteRecords(Container $container) {
//        /** @var EventLinkDao $eventLinkDao */
//        $eventLinkDao = $container->get(EventLinkDao::class);
//        $rows = $eventLinkDao->find( " href LIKE '". "%" . self::$eventLinkPhaseText . "%'", [] );
//        foreach($rows as $row) {
//            $ok = $eventLinkDao->delete($row);
//        }
//        
//         /** @var EventLinkPhaseDao $eventLinkPhaseDao */
//        $eventLinkPhaseDao = $container->get(EventLinkPhaseDao::class);        
//        $rows = $eventLinkPhaseDao->find( "text LIKE '". "%" . self::$eventLinkPhaseText . "%'", [] );               
//        foreach($rows as $row) {
//            $ok = $eventLinkPhaseDao->delete($row);
//        }
                
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
        $this->assertNull($event);
        
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
        $this->eventLinkRepo->add($eventLink);
        
        $this->assertFalse($eventLink->isLocked());
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

 

   

//---------------------------------------------------    
 
//    
//    public function testAdd() {
//        $institution = new Institution();
//        $institution->setName(self::$institutionName);                       
//       
//        $this->institutionRepo->add($institution);
//        $this->assertTrue($institution->isPersisted());  // !!!!!! InstitutionDao ma DaoEditAutoincrementKeyInterface, k zápisu dojde ihned !!!!!!!
//        // pro automaticky|generovany klic (tento pripad zde ) a  pro  overovany klic  - !!! zapise se hned !!!       
//        $this->assertFalse($institution->isLocked());
//    }
//
//    
//    public function testAddAndReread_I() {
//        $institution = new Institution();       
//        $institution->setName(self::$institutionName);        
//
//        $this->institutionRepo->add($institution); //zapise hned
//        /** @var Institution $institutionRereaded */
//        $institutionRereaded = $this->institutionRepo->get($institution->getId());
//        $this->assertInstanceOf(InstitutionInterface::class, $institutionRereaded);
//        $this->assertTrue($institutionRereaded->isPersisted());
//        $this->assertFalse($institutionRereaded->isLocked());
//       
//        //$institutionRereaded, $institution ... jsou odkazy na stejny objekt              
//        self::$idI = $institution->getId();
//        self::$idR = $institutionRereaded->getId();
//        $this->assertEquals(self::$idI, self::$idR );
//        
//        $institution->setInstitutionTypeId(self::$institutionTypeId); //zapise do entity, ktera je v repository
//    }    
//    
//    public function testAddAndReread_II() {        
//        $institution = $this->institutionRepo->get(self::$idI);
//    
//        /** @var Institution $institutionRereaded2 */
//        $institutionRereaded2 = $this->institutionRepo->get($institution->getId());
//        $this->assertInstanceOf(InstitutionInterface::class, $institutionRereaded2);
//        $this->assertTrue($institutionRereaded2->isPersisted());
//        $this->assertFalse($institutionRereaded2->isLocked());
//
//        $this->assertEquals(self::$institutionTypeId, $institutionRereaded2->getInstitutionTypeId());
//    }
//
//    
//    public function testFindAll() {
//        $institutionsArray = $this->institutionRepo->findAll();
//        $this->assertTrue(is_array($institutionsArray));
//        $this->assertGreaterThan(0,count($institutionsArray)); //jsou tam minimalne 2
//    }
//
//    
//    public function testFind() {      
//        $institutionsArray = $this->institutionRepo->find( "name LIKE '" . self::$institutionName . "%'", []); 
//        $this->assertTrue(is_array($institutionsArray));
//        $this->assertGreaterThan(0,count($institutionsArray)); //jsou tam minimalne 2
//                       
//    }
//
//    public function testRemove_OperationWithLockedEntity() {
//        $institution = $this->institutionRepo->get(self::$institutionId);    
//        $this->assertInstanceOf(InstitutionInterface::class, $institution);
//        $this->assertTrue($institution->isPersisted());
//        $this->assertFalse($institution->isLocked());
//        
//        $institution->lock();
//        $this->expectException( OperationWithLockedEntityException::class);
//        $this->institutionRepo->remove($institution);
//    }
//
//    
//    public function testRemove() {
//        $institution = $this->institutionRepo->get(self::$institutionId);    
//        $this->assertInstanceOf(InstitutionInterface::class, $institution);
//        $this->assertTrue($institution->isPersisted());
//        $this->assertFalse($institution->isLocked());
//
//        $this->institutionRepo->remove($institution);
//        
//        $this->assertTrue($institution->isPersisted());
//        $this->assertTrue($institution->isLocked());   // maže až při flush
//        $this->institutionRepo->flush();
//        //  uz neni locked
//        $this->assertFalse($institution->isLocked());
//       
//        // pokus o čtení, institution uz  neni
//        $institution = $this->institutionRepo->get(self::$institutionId);
//        $this->assertNull($institution);
//    }



