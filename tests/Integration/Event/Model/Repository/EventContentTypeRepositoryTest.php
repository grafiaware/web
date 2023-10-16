<?php
declare(strict_types=1);
namespace Test\Integration\Event\Model\Repository;

use Test\AppRunner\AppRunner;
use Pes\Container\Container;

use Container\EventsModelContainerConfigurator;
use Test\Integration\Event\Container\TestDbEventsContainerConfigurator;

use Events\Model\Dao\EventContentTypeDao;
use Events\Model\Repository\EventContentTypeRepo;
use Model\Repository\Exception\UnableAddEntityException;
use Model\Repository\Exception\OperationWithLockedEntityException;

use Events\Model\Entity\EventContentType;
use Events\Model\Entity\EventContentTypeInterface;
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

    private static $typeDalsi = "testEventContentType";
    
    private static $idContentTypeTouple;
    private static $idContentType2;


    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(
                    (new Container( ) )  )
            );

        // mazání - zde jen pro případ, že minulý test nebyl dokončen
        self::deleteRecords($container);

        self::insertRecords($container);
    }


    private static function insertRecords(Container $container) {
        // toto je příprava testu, vlozi 1 typ
        /** @var EventContentTypeDao $eventContentTypeDao */
        $eventContentTypeDao = $container->get(EventContentTypeDao::class);
        $rowData = new RowData();
        //$type =  "testEventContentType";
        $rowData->offsetSet('type', self::$typeDalsi );
        $rowData->offsetSet('name', self::$typeDalsi . "Name");
        $eventContentTypeDao->insert($rowData);
        self::$idContentTypeTouple = $eventContentTypeDao->getLastInsertedPrimaryKey();
        
    }


    private static function deleteRecords(Container $container) {
        /** @var EventContentTypeDao $eventContentTypeDao */
        $eventContentTypeDao = $container->get(EventContentTypeDao::class);
        //$row = $eventContentTypeDao->get(['type'=>'testEventContentType']);

        $rows = $eventContentTypeDao->find( "type LIKE '". self::$typeDalsi . "%'", []);
        foreach($rows as $row) {
            $ok =  $eventContentTypeDao->delete($row);
        }
    }

    protected function setUp(): void {
        $this->container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(new Container())
            );
        $this->eventContentTypeRepo = $this->container->get(EventContentTypeRepo::class);
    }


    protected function tearDown(): void {
        //$this->eventContentTypeRepo->flush();
      $this->eventContentTypeRepo->__destruct();
      
    
    }

    public static function tearDownAfterClass(): void {
        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(new Container())
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
        $eventContentType = $this->eventContentTypeRepo->get( self::$idContentTypeTouple['id'] );
        $this->assertInstanceOf(EventContentTypeInterface::class, $eventContentType);

        $v = $this->eventContentTypeRepo->remove($eventContentType);
        $this->assertNull($v);
    }

//zde ne    
//    public function testGetAfterRemove() {
//        $eventContentType = $this->eventContentTypeRepo->get( self::$idContentTypeTouple['id']  );
//        $this->assertNull($eventContentType);
//    }



    public function testAdd() {
        /** @var EventContentType $eventContentType */
        $eventContentType = new EventContentType();
        $eventContentType->setType(self::$typeDalsi );
        $eventContentType->setName(self::$typeDalsi .'Name1');
        $this->eventContentTypeRepo->add($eventContentType);
        
        // pro automaticky|generovany klic a pro  overovany klic (tento pripad zde ) - !!! zapise se hned !!!    DaoEditKeyDbVerifiedInterface
        $this->assertTrue($eventContentType->isPersisted());
        $this->assertFalse($eventContentType->isLocked());

    }

    public function testAddTheSame() {
        /** @var EventContentType $eventContentType */
        $eventContentType = new EventContentType();
        $eventContentType->setType(self::$typeDalsi);
        $eventContentType->setName(self::$typeDalsi .'Name1');

        $this->expectException( UnableAddEntityException::class );
        $this->eventContentTypeRepo->add($eventContentType);
    }


    public function testAddAndReread() {
        /** @var EventContentType $eventContentType */
        $eventContentType = new EventContentType();
        $eventContentType->setType(self::$typeDalsi . '2');
        $eventContentType->setName(self::$typeDalsi . 'Name2');
        $this->eventContentTypeRepo->add($eventContentType);

        //$this->eventContentTypeRepo->flush();   //overovany klic (tento pripad zde ) - !!! zapise se hned
        $this->assertTrue($eventContentType->isPersisted());
        $this->assertFalse($eventContentType->isLocked());
                      
        //podle id znovu precist        
        /** @var EventContentType $eventContentTypeRereaded */
        $eventContentTypeRereaded = $this->eventContentTypeRepo->get($eventContentType->getId());
        $this->assertInstanceOf(EventContentTypeInterface::class, $eventContentTypeRereaded);
        $this->assertTrue($eventContentTypeRereaded->isPersisted());
        $this->assertFalse($eventContentType->isLocked());
        
        self::$idContentType2 = $eventContentType->getId();
    }


    public function testFindAll() {
        $eventContentTypes = $this->eventContentTypeRepo->findAll();
        $this->assertTrue(is_array($eventContentTypes));
        $this->assertGreaterThan(0,count($eventContentTypes)); //jsou tam minimalne 2
    }


//ZATIM NEMA FIND metodu
//    public function testFind() {
//        $rows =  $this->eventContentTypeRepo->find( "type LIKE '" . self::$typeKlic . "%'", []);
//
//        $this->assertTrue(is_array($rows));
//        $this->assertGreaterThan(0,count($rows)); //jsou tam minimalne 2
//    }


    public function testRemove_OperationWithLockedEntity() {
        /** @var EventContentType $eventContentType */
        $eventContentType = $this->eventContentTypeRepo->get( self::$idContentType2 );                    
        
        $this->assertInstanceOf(EventContentTypeInterface::class, $eventContentType);
        $this->assertTrue($eventContentType->isPersisted());
        $this->assertFalse($eventContentType->isLocked());

        $eventContentType->lock();
        $this->expectException( OperationWithLockedEntityException::class);
        $this->eventContentTypeRepo->remove($eventContentType);
    }


    public function testRemove() {
        /** @var EventContentType $eventContentType */
        $eventContentType = $this->eventContentTypeRepo->get( self::$idContentType2 );

        $this->assertInstanceOf(EventContentTypeInterface::class, $eventContentType);
        $this->assertTrue($eventContentType->isPersisted());
        $this->assertFalse($eventContentType->isLocked());

        $this->eventContentTypeRepo->remove($eventContentType);

        $this->assertTrue($eventContentType->isPersisted());
        $this->assertTrue($eventContentType->isLocked());   // maže až při flush
        $this->eventContentTypeRepo->flush();
        //  uz neni locked
        $this->assertFalse($eventContentType->isLocked());

        // pokus o čtení, entita EventContentType.self::$typeKlic  uz  neni
        $eventContentType = $this->eventContentTypeRepo->get( self::$idContentType2 );
        $this->assertNull($eventContentType);

    }



}
