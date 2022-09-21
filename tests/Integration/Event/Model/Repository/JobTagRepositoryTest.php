<?php
declare(strict_types=1);
namespace Test\Integration\Event\Model\Repository;

use Test\AppRunner\AppRunner;
use Pes\Container\Container;

use Test\Integration\Event\Container\EventsModelContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Dao\JobTagDao;
use Events\Model\Repository\JobTagRepo;
use Model\Repository\Exception\UnableAddEntityException;
use Model\Repository\Exception\OperationWithLockedEntityException;

use Events\Model\Entity\JobTag;
use Events\Model\Entity\JobTagInterface;
use Model\RowData\RowData;

/**
 * Description of JobTagRepositoryTest
 *
 * @author vlse2610
 */
class JobTagRepositoryTest extends AppRunner {
    private $container;

    /**
     *
     * @var JobTagRepo
     */
    private $jobTagRepo;
    
    private static $tagKlic = "proJobTagRepoTest";

    
    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(
                    (new Container( ) )  )
            );

        // mazání - zde jen pro případ, že minulý test nebyl dokončen
        self::deleteRecords($container);
        
        self::insertRecords($container);
    }
    
    
    private static function insertRecords(Container $container) {
        // toto je příprava testu, vlozi 1 tag
        /** @var JobTagDao $jobTagDao */
        $jobTagDao = $container->get( JobTagDao::class );
        $rowData = new RowData();
        $rowData->offsetSet('tag', self::$tagKlic . "1" );
        $jobTagDao->insert($rowData);
    }
    

    private static function deleteRecords(Container $container) {
        /** @var JobTagDao $jobTagDao */
        $jobTagDao = $container->get(JobTagDao::class);
        
        $rows = $jobTagDao->find( " tag LIKE '". self::$tagKlic . "%'", []);                
        foreach($rows as $row) {
            $ok =  $jobTagDao->delete($row);
        }
    }

    
    protected function setUp(): void {
        $this->container =
            (new EventsModelContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        $this->jobTagRepo = $this->container->get( JobTagRepo::class);
    }

  
    protected function tearDown(): void {
        //$this->jobTagRepo->flush();
        $this->jobTagRepo->__destruct();
    }

    public static function tearDownAfterClass(): void {
        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );

        self::deleteRecords($container);
    }

    
    public function testSetUp() {
        $this->assertInstanceOf( JobTagRepo::class, $this->jobTagRepo );
    }

    
    
    public function testGetNonExisted() {
       $jobTag = $this->jobTagRepo->get('QQfffg  ghghgh');
        $this->assertNull($jobTag);
    }
    
    

    public function testGetAndRemoveAfterSetup() {
        /** @var JobTag $jobTag */
        $jobTag = $this->jobTagRepo->get( self::$tagKlic  . "1" );   
        $this->assertInstanceOf( JobTagInterface::class, $jobTag );

        $this->jobTagRepo->remove($jobTag);
    }
 
    public function testGetAfterRemove() {
        /** @var JobTag $jobTag */
        $jobTag = $this->jobTagRepo->get( self::$tagKlic  . "1" );
        $this->assertNull($jobTag);
    }
    
    

    public function testAdd() {
        /** @var JobTag $jobTag */
        $jobTag = new JobTag();
        $jobTag->setTag(self::$tagKlic .'2');
        $this->jobTagRepo->add($jobTag);        
        // pro automaticky|generovany klic a pro  overovany klic (tento pripad zde ) - !!! zapise se hned !!!    DaoEditKeyDbVerifiedInterface        
        $this->assertTrue($jobTag->isPersisted());  
        $this->assertFalse($jobTag->isLocked());
        
    }

    
    
    public function testAddTheSame() {
        /** @var JobTag $jobTag */
        $jobTag = new JobTag();
        $jobTag->setTag(self::$tagKlic .'2');
                
        $this->expectException( UnableAddEntityException::class );
        $this->jobTagRepo->add($jobTag);               
    }

    
    public function testAddAndReread() {
        /** @var JobTag $jobTag */
        $jobTag = new JobTag();
        $jobTag->setTag(self::$tagKlic .'3');
        $this->jobTagRepo->add($jobTag); // overovany klic zapise hned

        // $this->->flush();
        $this->assertTrue($jobTag->isPersisted());  
        $this->assertFalse($jobTag->isLocked());

        /** @var JobTag $jobTagRereaded */
        $jobTagRereaded = $this->jobTagRepo->get($jobTag->getTag());
        $this->assertInstanceOf(JobTagInterface::class, $jobTagRereaded);        
        $this->assertTrue($jobTagRereaded->isPersisted());
        $this->assertFalse($jobTagRereaded->isLocked());
    }    
        
    

    public function testFindAll() {
        $jobTagArray = $this->jobTagRepo->findAll();
        $this->assertTrue(is_array($jobTagArray));
        $this->assertGreaterThan(0,count($jobTagArray)); //jsou tam minimalne 2
    }
  

//////ZATIM NEMA FIND metodu    
////    public function testFind() {                                         
////        $rows =  $this->pozadovaneVzdelaniRepo->find( " vzdelani LIKE '" . self::$stupenKlic . "%'", []);   
////
////        $this->assertTrue(is_array($rows));
////        $this->assertGreaterThan(0,count($rows)); //jsou tam minimalne 2                                       
////    }
    

    public function testRemove_OperationWithLockedEntity() {
        /** @var JobTag $jobTag */
        $jobTag = $this->jobTagRepo->get(self::$tagKlic . "3");    
        $this->assertInstanceOf(JobTag::class, $jobTag);
        $this->assertTrue($jobTag->isPersisted());
        $this->assertFalse($jobTag->isLocked());
        
        $jobTag->lock();
        $this->expectException( OperationWithLockedEntityException::class);
        $this->jobTagRepo->remove($jobTag);
    }
    
    
    
    public function testRemove() {
        /** @var JobTag $jobTag */
        $jobTag = $this->jobTagRepo->get(self::$tagKlic . "3" );
                
        $this->assertInstanceOf(JobTagInterface::class, $jobTag);
        $this->assertTrue($jobTag->isPersisted());
        $this->assertFalse($jobTag->isLocked());
        
        $this->jobTagRepo->remove($jobTag);
        
        $this->assertTrue($jobTag->isPersisted());
        $this->assertTrue($jobTag->isLocked());   // maže až při flush
        $this->jobTagRepo->flush();
        //  uz neni locked
        $this->assertFalse($jobTag->isLocked());
        
        // pokus o čtení, entita JobTag.self::$tagKlic.3  uz  neni
        $jobTag = $this->jobTagRepo->get(self::$tagKlic . "3" );
        $this->assertNull($jobTag);
        
    }
            


}
