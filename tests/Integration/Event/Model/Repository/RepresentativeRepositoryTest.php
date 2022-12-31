<?php
declare(strict_types=1);
namespace Test\Integration\Event\Model\Repository;

use Test\AppRunner\AppRunner;
use Pes\Container\Container;
use Test\Integration\Event\Container\EventsModelContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Dao\CompanyDao;
use Events\Model\Dao\LoginDao;

use Events\Model\Dao\RepresentativeDao;
use Events\Model\Repository\RepresentativeRepo;
use Events\Model\Entity\Representative;
use Events\Model\Entity\RepresentativeInterface;

use Model\Repository\Exception\OperationWithLockedEntityException;
use Model\RowData\RowData;


 /**
 * Description of RepresentativeRepositoryTest
 *
 * @author vlse2610
 */
class RepresentativeRepositoryTest extends AppRunner {
    private $container;
    /**
     *
     * @var RepresentativeRepo
     */
    private $representativeRepo;

    private static $representativeName = "proRepresentativeRepoTest";    
    
    private static $companyId;
    private static $loginNameTouples;
    private static $representativeLoginNameTouples;

    
         


    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        $container =
            (new EventsModelContainerConfigurator())->configure(
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
            'name' => self::$representativeName 
        ]);
        $companyDao->insert($rowData);                
        self::$companyId = $companyDao->lastInsertIdValue(); 
        
        
        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);  
        $rowData = new RowData();
        $rowData->import([
            'login_name' => self::$representativeName 
        ]);
        $loginDao->insert($rowData);                
        self::$loginNameTouples =  $loginDao->getPrimaryKeyTouples($rowData->getArrayCopy());            
        
        
        /** @var RepresentativeDao $representativeDao */
        $representativeDao = $container->get(RepresentativeDao::class);  
        $rowData = new RowData();
        $rowData->import([
            'login_login_name' => self::$loginNameTouples['login_name'],  //primarni
            'company_id'  =>   self::$companyId,
        ]);
        $representativeDao->insert($rowData);          
        self::$representativeLoginNameTouples =  $representativeDao->getPrimaryKeyTouples($rowData->getArrayCopy());
        
    }

    
    private static function deleteRecords(Container $container) {                    
        /** @var RepresentativeDao $representativetDao */
        $representativetDao = $container->get( RepresentativeDao::class);
        $rows = $representativetDao->find( " login_login_name LIKE '". "%" . self::$representativeName . "%'", [] );
        foreach($rows as $row) {
            $ok = $representativetDao->delete($row);
        }     
        
        /** @var CompanyDao $companyDao */
        $companyDao = $container->get( CompanyDao::class);
        $rows = $companyDao->find( " name LIKE '". "%" . self::$representativeName . "%'", [] );
        foreach($rows as $row) {
            $ok = $companyDao->delete($row);
        }             
        
       
        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        // get a smazat podle login name je jen 1
        $rows = $loginDao->find( " login_name LIKE '". "%" . self::$representativeName . "%'", [] );
        foreach($rows as $row) {
            $ok = $loginDao->delete($row);
        }     
        
        
    } 

    

    protected function setUp(): void {
        $this->container =
            (new EventsModelContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        $this->representativeRepo = $this->container->get(RepresentativeRepo::class);
    }
    
    
    
    protected function tearDown(): void {
        //$this->e>flush();
        $this->representativeRepo->__destruct();        
    }
    

    public static function tearDownAfterClass(): void {
        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        self::deleteRecords($container);
    }
    
    
    public function testSetUp() {
        $this->assertInstanceOf( RepresentativeRepo::class, $this->representativeRepo );
    }
    

    public function testGetNonExisted() {
        $representative = $this->representativeRepo->get(0,0);
        $this->assertNull($representative);
    }
    
    
    public function testGetAfterSetup() {
        $representative = $this->representativeRepo->get(self::$representativeLoginNameTouples['login_login_name'] );    
        $this->assertInstanceOf(RepresentativeInterface::class, $representative);
    }


    public function testGetAndRemoveAfterSetup() {
        $representative = $this->representativeRepo->get(self::$representativeLoginNameTouples['login_login_name']);    
        $this->assertInstanceOf(RepresentativeInterface::class, $representative);
        
        $this->representativeRepo->remove($representative);        
        $this->assertTrue($representative->isPersisted());
        $this->assertTrue($representative->isLocked()); 
    }

    
    public function testGetAfterRemove() {
        $representative = $this->representativeRepo->get(self::$representativeLoginNameTouples['login_login_name']);
        $this->assertNull($representative);
    }

   
    
    public function testAdd() {
        $representative = new Representative();      
        $representative->setCompanyId(  self::$companyId );
        $representative->setLoginLoginName( self::$representativeName );
        $this->representativeRepo->add($representative);  //nezapise hned!!! --nenigenerovany ani overovany
        
        $this->assertFalse($representative->isPersisted());
        $this->assertTrue($representative->isLocked());        
    }
    
    
    public function testReread() {        
        $representativeRereaded = $this->representativeRepo->get( self::$representativeLoginNameTouples['login_login_name'] );
     
        $this->assertInstanceOf(RepresentativeInterface::class, $representativeRereaded);
        $this->assertTrue($representativeRereaded->isPersisted());
        $this->assertFalse($representativeRereaded->isLocked());        
    }
   
//    /**
//     * Pokus o zapsani věty do company_address se stejným primárním klíčem. 
//     * Na úrovni DB nastane chyba (tj. OK) a nic se neprovede. Nijak se ale neda poznat ,že nastala?
//     */
//    public function testAdd2() {
//        $representative = new Representative();      
//        $representative->setLoginLoginName( self::$representativeName  . "trr222" );
//        $representative->setCompanyId(  self::$companyId );
//        $this->representativeRepo->add($representative);  //nezapise hned!!! --nenigenerovany ani overovany
//        
//        $this->assertFalse($representative->isPersisted());
//        $this->assertTrue($representative->isLocked());        
//    }
    

    public function testfindAll() {
        $representativeArray = $this->representativeRepo->findAll();
        $this->assertTrue(is_array($representativeArray));
    }
    
    
    public function testFind() {      
       $representativeArray = $this->representativeRepo->find( " login_login_name LIKE '%" . self::$representativeName . "%'", []); 
       $this->assertTrue(is_array($representativeArray));
       $this->assertGreaterThan(0,count($representativeArray)); //jsou tam minimalne 1
       $this->assertInstanceOf( RepresentativeInterface::class, $representativeArray [0] );
    }
    
    
    public function testFindByCompany(){
        $representatives = $this->representativeRepo->findByCompany(self::$companyId);
        $this->assertTrue(is_array($representatives));    
        $this->assertCount(1, $representatives);
        $this->assertEquals(1,count($representatives));   
        $this->assertInstanceOf( RepresentativeInterface::class, $representatives [0] );

    }
    


    public function testRemove_OperationWithLockedEntity() {
        $representative = $this->representativeRepo->get( self::$representativeLoginNameTouples['login_login_name'] );    
        $this->assertInstanceOf(RepresentativeInterface::class,  $representative);
        $this->assertTrue( $representative->isPersisted());
        $this->assertFalse( $representative->isLocked());
        
        $representative->lock();
        $this->expectException( OperationWithLockedEntityException::class);
        $this->representativeRepo->remove( $representative);
    }

    
    public function testRemove() {
        $representative = $this->representativeRepo->get( self::$representativeLoginNameTouples['login_login_name'] );    
        $this->assertInstanceOf( RepresentativeInterface::class,  $representative);
        $this->assertTrue( $representative->isPersisted());
        $this->assertFalse( $representative->isLocked());

        $this->representativeRepo->remove($representative);
        
        $this->assertTrue($representative->isPersisted());
        $this->assertTrue($representative->isLocked());   // maže až při flush
       
        $this->representativeRepo->flush();
        //  uz neni locked
        $this->assertFalse($representative->isLocked());
       
        // pokus o čtení, institution uz  neni
        $representative2 = $this->representativeRepo->get( self::$representativeLoginNameTouples['login_login_name'] );
        $this->assertNull($representative2);
    }

}



