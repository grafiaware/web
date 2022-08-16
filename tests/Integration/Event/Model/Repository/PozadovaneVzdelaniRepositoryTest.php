<?php
declare(strict_types=1);
namespace Test\Integration\Event\Model\Repository;

use Test\AppRunner\AppRunner;
use Pes\Container\Container;

use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Dao\PozadovaneVzdelaniDao;
use Events\Model\Repository\PozadovaneVzdelaniRepo;
use Model\Repository\Exception\UnableAddEntityException;
use Model\Repository\Exception\OperationWithLockedEntityException;

use Events\Model\Entity\PozadovaneVzdelani;
use Events\Model\Entity\PozadovaneVzdelaniInterface;
use Model\RowData\RowData;

/**
 *
 * Description of PozadovaneVzdelaniRepositoryTest
 * @author vlse2610
 */
class PozadovaneVzdelaniRepositoryTest extends AppRunner {
    private $container;

    /**
     *
     * @var PozadovaneVzdelaniRepo
     */
    private $pozadovaneVzdelaniRepo;
    
    private static $stupenKlic = "110";

    
    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        $container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(
                    (new Container( ) )  )
            );

        // mazání - zde jen pro případ, že minulý test nebyl dokončen
        self::deleteRecords($container);
        
        self::insertRecords($container);
    }
    
    
    private static function insertRecords(Container $container) {
        // toto je příprava testu, vlozi 1 stupen
        /** @var PozadovaneVzdelaniDao $pozadovaneVzdelaniDao */
        $pozadovaneVzdelaniDao = $container->get( PozadovaneVzdelaniDao::class );
        $rowData = new RowData();
        $rowData->offsetSet('stupen', self::$stupenKlic );
        $rowData->offsetSet('vzdelani', self::$stupenKlic . "Vydelani");
        $pozadovaneVzdelaniDao->insert($rowData);
    }
    

    private static function deleteRecords(Container $container) {
        /** @var PozadovaneVzdelaniDao $pozadovaneVzdelaniDao */
        $pozadovaneVzdelaniDao = $container->get(PozadovaneVzdelaniDao::class);
        
        $rows = $pozadovaneVzdelaniDao->find( " stupen LIKE '". self::$stupenKlic . "%'", []);                
        foreach($rows as $row) {
            $ok =  $pozadovaneVzdelaniDao->delete($row);
        }
    }

    
    protected function setUp(): void {
        $this->container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        $this->pozadovaneVzdelaniRepo = $this->container->get( PozadovaneVzdelaniRepo::class);
    }

  
    protected function tearDown(): void {
        //$this->eventContentTypeRepo->flush();
        $this->pozadovaneVzdelaniRepo->__destruct();
    }

    public static function tearDownAfterClass(): void {
        $container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );

        self::deleteRecords($container);
    }

    
    public function testSetUp() {
        $this->assertInstanceOf( PozadovaneVzdelaniRepo::class, $this->pozadovaneVzdelaniRepo );
    }

    
    
    public function testGetNonExisted() {
        $pozadovaneVzdelaniRepo = $this->pozadovaneVzdelaniRepo->get('QQfffg  ghghgh');
        $this->assertNull($pozadovaneVzdelaniRepo);
    }
    
    

    public function testGetAndRemoveAfterSetup() {
        /** @var PozadovaneVzdelani $pozadovaneVzdelani */
        $pozadovaneVzdelani = $this->pozadovaneVzdelaniRepo->get( self::$stupenKlic );   
        $this->assertInstanceOf( PozadovaneVzdelaniInterface::class, $pozadovaneVzdelani );

        $this->pozadovaneVzdelaniRepo->remove($pozadovaneVzdelani);
        //$this->assertNull($v);
    }
 
    public function testGetAfterRemove() {
        /** @var PozadovaneVzdelani $pozadovaneVzdelani */
        $pozadovaneVzdelani = $this->pozadovaneVzdelaniRepo->get( self::$stupenKlic );
        $this->assertNull($pozadovaneVzdelani);
    }
    
    

    public function testAdd() {
        /** @var PozadovaneVzdelani $pozadovaneVzdelani */
        $pozadovaneVzdelani = new PozadovaneVzdelani();
        $pozadovaneVzdelani->setStupen(self::$stupenKlic .'1');
        $pozadovaneVzdelani->setVzdelani(self::$stupenKlic .'Name1');
        $this->pozadovaneVzdelaniRepo->add($pozadovaneVzdelani);        
        // pro automaticky|generovany klic a pro  overovany klic (tento pripad zde ) - !!! zapise se hned !!!    DaoEditKeyDbVerifiedInterface        
        $this->assertTrue($pozadovaneVzdelani->isPersisted());  
        $this->assertFalse($pozadovaneVzdelani->isLocked());
        
    }
//    
//    public function testAddTheSame() {
//        /** @var EventContentType $eventContentType */
//        $eventContentType = new EventContentType();
//        $eventContentType->setType(self::$typeKlic .'1');
//        $eventContentType->setName(self::$typeKlic .'Name1');
//                
//        $this->expectException( UnableAddEntityException::class );
//        $this->eventContentTypeRepo->add($eventContentType);               
//    }
//    
//    
//    public function testAddAndReread() {
//        /** @var EventContentType $eventContentType */
//        $eventContentType = new EventContentType();
//        $eventContentType->setType(self::$typeKlic . '2');
//        $eventContentType->setName(self::$typeKlic . 'Name2');
//        $this->eventContentTypeRepo->add($eventContentType);
//
//        $this->eventContentTypeRepo->flush();
//        $this->assertTrue($eventContentType->isPersisted());  
//        $this->assertFalse($eventContentType->isLocked());
//
//        /** @var EventContentType $eventContentTypeRereaded */
//        $eventContentTypeRereaded = $this->eventContentTypeRepo->get($eventContentType->getType());
//        $this->assertInstanceOf(EventContentTypeInterface::class, $eventContentTypeRereaded);        
//        $this->assertTrue($eventContentTypeRereaded->isPersisted());
//        $this->assertFalse($eventContentType->isLocked());
//    }    
//        
//
//    public function testFindAll() {
//        $eventContentTypes = $this->eventContentTypeRepo->findAll();
//        $this->assertTrue(is_array($eventContentTypes));
//        $this->assertGreaterThan(0,count($eventContentTypes)); //jsou tam minimalne 2
//    }
//  
//    
////ZATIM NEMA FIND metodu    
////    public function testFind() {                                         
////        $rows =  $this->eventContentTypeRepo->find( "type LIKE '" . self::$typeKlic . "%'", []);   
////
////        $this->assertTrue(is_array($rows));
////        $this->assertGreaterThan(0,count($rows)); //jsou tam minimalne 2                                       
////    }
//    
//    
//    public function testRemove_OperationWithLockedEntity() {
//        /** @var EventContentType $eventContentType */
//        $eventContentType = $this->eventContentTypeRepo->get(self::$typeKlic . "1");    
//        $this->assertInstanceOf(EventContentTypeInterface::class, $eventContentType);
//        $this->assertTrue($eventContentType->isPersisted());
//        $this->assertFalse($eventContentType->isLocked());
//        
//        $eventContentType->lock();
//        $this->expectException( OperationWithLockedEntityException::class);
//        $this->eventContentTypeRepo->remove($eventContentType);
//    }
//    
//    
//    public function testRemove() {
//        /** @var EventContentType $eventContentType */
//        $eventContentType = $this->eventContentTypeRepo->get(self::$typeKlic . "1" );
//                
//        $this->assertInstanceOf(EventContentTypeInterface::class, $eventContentType);
//        $this->assertTrue($eventContentType->isPersisted());
//        $this->assertFalse($eventContentType->isLocked());
//        
//        $this->eventContentTypeRepo->remove($eventContentType);
//        
//        $this->assertTrue($eventContentType->isPersisted());
//        $this->assertTrue($eventContentType->isLocked());   // maže až při flush
//        $this->eventContentTypeRepo->flush();
//        //  uz neni locked
//        $this->assertFalse($eventContentType->isLocked());
//        
//        // pokus o čtení, entita EventContentType.self::$typeKlic  uz  neni
//        $eventContentType = $this->eventContentTypeRepo->get(self::$typeKlic . "1" );
//        $this->assertNull($eventContentType);
//        
//    }
//            


}
