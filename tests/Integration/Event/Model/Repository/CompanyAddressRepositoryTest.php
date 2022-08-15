<?php
declare(strict_types=1);
namespace Test\Integration\Event\Model\Repository;

use Test\AppRunner\AppRunner;
use Pes\Container\Container;
use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Dao\CompanyDao;
use Events\Model\Dao\CompanyAddressDao;

use Events\Model\Repository\CompanyAddressRepo;
use Events\Model\Entity\CompanyAddress;
use Events\Model\Entity\CompanyAddressInterface;

use Model\Repository\Exception\OperationWithLockedEntityException;
use Model\RowData\RowData;


 /**
 * Description of CompanyAddressRepositoryTest
 *
 * @author vlse2610
 */
class CompanyAddressRepositoryTest extends AppRunner {
    private $container;
    /**
     *
     * @var CompanyAddressRepo
     */
    private $companyAddressRepo;

    private static $companyAddressName = "proCompanyAddressRepoTest";    
    //private static $companyAddressId;
    
    private static $companyId;
    //private static $companyId2;

             


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
        /** @var CompanyDao $companyDao */
        $companyDao = $container->get(CompanyDao::class);  
        $rowData = new RowData();
        $rowData->import([
            'name' => self::$companyAddressName 
        ]);
        $companyDao->insert($rowData);                
        self::$companyId = $companyDao->lastInsertIdValue(); 
        
        /** @var CompanyAddressDao $companyAddressDao */
        $companyAddressDao = $container->get(CompanyAddressDao::class);  
        $rowData = new RowData();
        $rowData->import([
            'name' => self::$companyAddressName,  
            'company_id'  =>   self::$companyId,
            'lokace'  =>  self::$companyAddressName
        ]);
        $companyAddressDao->insert($rowData);                
       // self::$companyAddressId = $companyAddressDao->lastInsertIdValue(); 
    }

    
    private static function deleteRecords(Container $container) {
        /** @var CompanyDao $companyDao */
        $companyDao = $container->get( CompanyDao::class);
        $rows = $companyDao->find( " name LIKE '". "%" . self::$companyAddressName . "%'", [] );
        foreach($rows as $row) {
            $ok = $companyDao->delete($row);
        }         
        
        /** @var CompanyAddressDao $companyAddresstDao */
        $companyAddresstDao = $container->get( CompanyAddressDao::class);
        $rows = $companyAddresstDao->find( " name LIKE '". "%" . self::$companyAddressName . "%'", [] );
        foreach($rows as $row) {
            $ok = $companyAddresstDao->delete($row);
        }         
    } 

    

    protected function setUp(): void {
        $this->container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        $this->companyAddressRepo = $this->container->get(CompanyAddressRepo::class);
    }
    
    
    
    protected function tearDown(): void {
        //$this->e>flush();
        $this->companyAddressRepo->__destruct();        
    }
    

    public static function tearDownAfterClass(): void {
        $container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        self::deleteRecords($container);
    }
    
    
    public function testSetUp() {
        $this->assertInstanceOf( CompanyAddressRepo::class, $this->companyAddressRepo );
    }
    

    public function testGetNonExisted() {
        $companyAddress = $this->companyAddressRepo->get(0);
        $this->assertNull($companyAddress);
    }
    
    
    public function testGetAfterSetup() {
        $companyAddress = $this->companyAddressRepo->get(self::$companyId);    
        $this->assertInstanceOf(CompanyAddressInterface::class, $companyAddress);
    }


    public function testGetAndRemoveAfterSetup() {
        $companyAddress = $this->companyAddressRepo->get(self::$companyId);    
        $this->assertInstanceOf(CompanyAddressInterface::class, $companyAddress);
        
        $this->companyAddressRepo->remove($companyAddress);        
        $this->assertTrue($companyAddress->isPersisted());
        $this->assertTrue($companyAddress->isLocked()); 
    }

    
    public function testGetAfterRemove() {
        $companyAddress = $this->companyAddressRepo->get(self::$companyId);
        $this->assertNull($companyAddress);
    }

   
    
    public function testAdd() {
        $companyAddress = new CompanyAddress();      
        $companyAddress->setName( self::$companyAddressName  . "trrwqz.zu?aa" );
        $companyAddress->setCompanyId(  self::$companyId );
        $companyAddress->setLokace( self::$companyAddressName );
        $this->companyAddressRepo->add($companyAddress);  //nezapise hned!!! --nenigenerovany ani overovany
        
        $this->assertFalse($companyAddress->isPersisted());
        $this->assertTrue($companyAddress->isLocked());        
    }
    
    
    public function testReread() {        
        $companyAddressRereaded = $this->companyAddressRepo->get( self::$companyId );
     
        $this->assertInstanceOf(CompanyAddressInterface::class, $companyAddressRereaded);
        $this->assertTrue($companyAddressRereaded->isPersisted());
        $this->assertFalse($companyAddressRereaded->isLocked());        
    }
    
//    /**
//     * Pokus o zapsani věty do company_address se stejným primárním klíčem. 
//     * Na úrovni DB nastane chyba (tj. OK) a nic se neprovede. Nijak se ale nepozná,že nastala.
//     */
//    public function testAdd2() {
//        $companyAddress = new CompanyAddress();      
//        $companyAddress->setName( self::$companyAddressName  . "trr222" );
//        $companyAddress->setCompanyId(  self::$companyId );
//        $companyAddress->setLokace( self::$companyAddressName );
//        $this->companyAddressRepo->add($companyAddress);  //nezapise hned!!! --nenigenerovany ani overovany
//        
//        $this->assertFalse($companyAddress->isPersisted());
//        $this->assertTrue($companyAddress->isLocked());        
//    }
    

    public function testfindAll() {
        $companyAddressArray = $this->companyAddressRepo->findAll();
        $this->assertTrue(is_array($companyAddressArray));
    }
    
    
    public function testFind() {      
       $companyAddressArray = $this->companyAddressRepo->find( " name LIKE '%" . self::$companyAddressName . "%'", []); 
       $this->assertTrue(is_array($companyAddressArray));
       $this->assertGreaterThan(0,count($companyAddressArray)); //jsou tam minimalne 1
       $this->assertInstanceOf( CompanyAddressInterface::class, $companyAddressArray [0] );
    }


    public function testRemove_OperationWithLockedEntity() {
        $companyAddress = $this->companyAddressRepo->get( self::$companyId );    
        $this->assertInstanceOf(CompanyAddressInterface::class,  $companyAddress);
        $this->assertTrue( $companyAddress->isPersisted());
        $this->assertFalse( $companyAddress->isLocked());
        
        $companyAddress->lock();
        $this->expectException( OperationWithLockedEntityException::class);
        $this->companyAddressRepo->remove( $companyAddress);
    }

    
    public function testRemove() {
        $companyAddress = $this->companyAddressRepo->get(self::$companyId );    
        $this->assertInstanceOf( CompanyAddressInterface::class,  $companyAddress);
        $this->assertTrue( $companyAddress->isPersisted());
        $this->assertFalse( $companyAddress->isLocked());

        $this->companyAddressRepo->remove($companyAddress);
        
        $this->assertTrue($companyAddress->isPersisted());
        $this->assertTrue($companyAddress->isLocked());   // maže až při flush
       
        $this->companyAddressRepo->flush();
        //  uz neni locked
        $this->assertFalse($companyAddress->isLocked());
       
        // pokus o čtení, institution uz  neni
        $companyAddress2 = $this->companyAddressRepo->get(self::$companyId );
        $this->assertNull($companyAddress2);
    }

}



