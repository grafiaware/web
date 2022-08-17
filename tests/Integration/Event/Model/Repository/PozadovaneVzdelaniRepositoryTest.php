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

    
    
    public function testAddTheSame() {
        /** @var PozadovaneVzdelani $pozadovaneVzdelani */
        $pozadovaneVzdelani = new PozadovaneVzdelani();
        $pozadovaneVzdelani->setStupen(self::$stupenKlic .'1');
        $pozadovaneVzdelani->setVzdelani(self::$stupenKlic .'Name1');
                
        $this->expectException( UnableAddEntityException::class );
        $this->pozadovaneVzdelaniRepo->add($pozadovaneVzdelani);               
    }

    
    public function testAddAndReread() {
        /** @var PozadovaneVzdelani $pozadovaneVzdelani */
        $pozadovaneVzdelani = new PozadovaneVzdelani();
        $pozadovaneVzdelani->setStupen(self::$stupenKlic .'2');
        $pozadovaneVzdelani->setVzdelani(self::$stupenKlic .'Name2');
        $this->pozadovaneVzdelaniRepo->add($pozadovaneVzdelani); // overovany klic zapise hned

       // $this->pozadovaneVzdelaniRepo->flush();
        $this->assertTrue($pozadovaneVzdelani->isPersisted());  
        $this->assertFalse($pozadovaneVzdelani->isLocked());

                /** @var PozadovaneVzdelani $pozadovaneVzdelaniRereaded */
        $pozadovaneVzdelaniRereaded = $this->pozadovaneVzdelaniRepo->get($pozadovaneVzdelani->getStupen());
        $this->assertInstanceOf(PozadovaneVzdelaniInterface::class, $pozadovaneVzdelaniRereaded);        
        $this->assertTrue($pozadovaneVzdelaniRereaded->isPersisted());
        $this->assertFalse($pozadovaneVzdelaniRereaded->isLocked());
    }    
        

    public function testFindAll() {
        $pozadovaneVzdelaniArray = $this->pozadovaneVzdelaniRepo->findAll();
        $this->assertTrue(is_array($pozadovaneVzdelaniArray));
        $this->assertGreaterThan(0,count($pozadovaneVzdelaniArray)); //jsou tam minimalne 2
    }
  

////ZATIM NEMA FIND metodu    
//    public function testFind() {                                         
//        $rows =  $this->pozadovaneVzdelaniRepo->find( " vzdelani LIKE '" . self::$stupenKlic . "%'", []);   
//
//        $this->assertTrue(is_array($rows));
//        $this->assertGreaterThan(0,count($rows)); //jsou tam minimalne 2                                       
//    }
    
    
    public function testRemove_OperationWithLockedEntity() {
        /** @var PozadovaneVzdelani $pozadovaneVzdelani */
        $pozadovaneVzdelani = $this->pozadovaneVzdelaniRepo->get(self::$stupenKlic . "1");    
        $this->assertInstanceOf(PozadovaneVzdelani::class, $pozadovaneVzdelani);
        $this->assertTrue($pozadovaneVzdelani->isPersisted());
        $this->assertFalse($pozadovaneVzdelani->isLocked());
        
        $pozadovaneVzdelani->lock();
        $this->expectException( OperationWithLockedEntityException::class);
        $this->pozadovaneVzdelaniRepo->remove($pozadovaneVzdelani);
    }
    
    
    public function testRemove() {
        /** @var PozadovaneVzdelani $pozadovaneVzdelani */
        $pozadovaneVzdelani = $this->pozadovaneVzdelaniRepo->get(self::$stupenKlic . "1" );
                
        $this->assertInstanceOf(PozadovaneVzdelaniInterface::class, $pozadovaneVzdelani);
        $this->assertTrue($pozadovaneVzdelani->isPersisted());
        $this->assertFalse($pozadovaneVzdelani->isLocked());
        
        $this->pozadovaneVzdelaniRepo->remove($pozadovaneVzdelani);
        
        $this->assertTrue($pozadovaneVzdelani->isPersisted());
        $this->assertTrue($pozadovaneVzdelani->isLocked());   // maže až při flush
        $this->pozadovaneVzdelaniRepo->flush();
        //  uz neni locked
        $this->assertFalse($pozadovaneVzdelani->isLocked());
        
        // pokus o čtení, entita PozadovaneVzdelani.self::$stupenKlic  uz  neni
        $pozadovaneVzdelani = $this->pozadovaneVzdelaniRepo->get(self::$stupenKlic . "1" );
        $this->assertNull($pozadovaneVzdelani);
        
    }
            


}
