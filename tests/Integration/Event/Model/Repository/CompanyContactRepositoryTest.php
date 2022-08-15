<?php
declare(strict_types=1);
namespace Test\Integration\Event\Model\Repository;

use Test\AppRunner\AppRunner;
use Pes\Container\Container;
use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Dao\CompanyDao;
use Events\Model\Dao\CompanyContactDao;

use Events\Model\Repository\CompanyContactRepo;
use Events\Model\Entity\CompanyContact;
use Events\Model\Entity\CompanyContactInterface;

use Model\Repository\Exception\OperationWithLockedEntityException;
use Model\RowData\RowData;


 /**
 * Description of CompanyContactRepositoryTest
 *
 * @author vlse2610
 */
class CompanyContactRepositoryTest extends AppRunner {
    private $container;
    /**
     *
     * @var CompanyContactRepo
     */
    private $companyContactRepo;

    private static $companyContactName = "proCompanyContactRepoTest";    
    private static $companyContactId;
    private static $companyId;

    
    /**
     * 
     * @var CompanyContact
     */
    private static $companyContact2;            


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
            'name' => self::$companyContactName    ]);
        $companyDao->insert($rowData);                
        self::$companyId = $companyDao->lastInsertIdValue(); 
        
        /** @var CompanyContactDao $companyContactDao */
        $companyContactDao = $container->get(CompanyContactDao::class);  
        $rowData = new RowData();
        $rowData->import([
            'name' => self::$companyContactName,  
            'company_id'  =>   self::$companyId   ]);
        $companyContactDao->insert($rowData);                
        self::$companyContactId = $companyContactDao->lastInsertIdValue(); 
    }

    
    private static function deleteRecords(Container $container) {
        /** @var CompanyDao $companyDao */
        $companyDao = $container->get( CompanyDao::class);
        $rows = $companyDao->find( " name LIKE '". "%" . self::$companyContactName . "%'", [] );
        foreach($rows as $row) {
            $ok = $companyDao->delete($row);
        }         
        
        /** @var CompanyContactDao $companyContactDao */
        $companyContactDao = $container->get( CompanyContactDao::class);
        $rows = $companyContactDao->find( " name LIKE '". "%" . self::$companyContactName . "%'", [] );
        foreach($rows as $row) {
            $ok = $companyContactDao->delete($row);
        }         
    } 

    

    protected function setUp(): void {
        $this->container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        $this->companyContactRepo = $this->container->get(CompanyContactRepo::class);
    }
    
    
    
    protected function tearDown(): void {
        //$this->e>flush();
        $this->companyContactRepo->__destruct();        
    }
    

    public static function tearDownAfterClass(): void {
        $container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        self::deleteRecords($container);
    }
    
    
    public function testSetUp() {
        $this->assertInstanceOf( CompanyContactRepo::class, $this->companyContactRepo );
    }
    

    public function testGetNonExisted() {
        $companyContact = $this->companyContactRepo->get(0);
        $this->assertNull($companyContact);
    }
    
    
    public function testGetAfterSetup() {
        $companyContact = $this->companyContactRepo->get(self::$companyContactId);    
        $this->assertInstanceOf(CompanyContactInterface::class, $companyContact);
    }


    public function testGetAndRemoveAfterSetup() {
        $companyContact = $this->companyContactRepo->get(self::$companyContactId);    
        $this->assertInstanceOf(CompanyContactInterface::class, $companyContact);
        
        $this->companyContactRepo->remove($companyContact);        
        $this->assertTrue($companyContact->isPersisted());
        $this->assertTrue($companyContact->isLocked()); 
    }

    
    public function testGetAfterRemove() {
        $companyContact = $this->companyContactRepo->get(self::$companyContactId);
        $this->assertNull($companyContact);
    }

   
    
    public function testAdd() {
        $companyContact = new CompanyContact();      
        $companyContact->setName( self::$companyContactName  . "trrwqz.zu?aa" );
        $companyContact->setCompanyId(  self::$companyId );
        $this->companyContactRepo->add($companyContact);  //zapise hned        
       
        $this->assertTrue($companyContact->isPersisted());
        $this->assertFalse($companyContact->isLocked());
        
        self::$companyContact2 = $this->companyContactRepo->get($companyContact->getId());        
    }
    
    
    public function testReread() {        
        $companyContactRereaded = $this->companyContactRepo->get( self::$companyContact2->getId() );
     
        $this->assertInstanceOf(CompanyContactInterface::class, $companyContactRereaded);
        $this->assertTrue($companyContactRereaded->isPersisted());
        $this->assertFalse($companyContactRereaded->isLocked());        
    }
    

    public function testfindAll() {
        $companyContactArray = $this->companyContactRepo->findAll();
        $this->assertTrue(is_array($companyContactArray));
    }
    
    
    public function testFind() {      
       $companyContactArray = $this->companyContactRepo->find( " name LIKE '%" . self::$companyContactName . "%'", []); 
       $this->assertTrue(is_array($companyContactArray));
       $this->assertGreaterThan(0,count($companyContactArray)); //jsou tam minimalne 2
                       
    }


    public function testRemove_OperationWithLockedEntity() {
        $companyContact = $this->companyContactRepo->get(self::$companyContact2->getId() );    
        $this->assertInstanceOf(CompanyContactInterface::class, $companyContact);
        $this->assertTrue($companyContact->isPersisted());
        $this->assertFalse($companyContact->isLocked());
        
        $companyContact->lock();
        $this->expectException( OperationWithLockedEntityException::class);
        $this->companyContactRepo->remove($companyContact);
    }

    
    public function testRemove() {
        $companyContact = $this->companyContactRepo->get(self::$companyContact2->getId() );    
        $this->assertInstanceOf( CompanyContactInterface::class, $companyContact);
        $this->assertTrue($companyContact->isPersisted());
        $this->assertFalse($companyContact->isLocked());

        $this->companyContactRepo->remove($companyContact);
        
        $this->assertTrue($companyContact->isPersisted());
        $this->assertTrue($companyContact->isLocked());   // maže až při flush
       
        $this->companyContactRepo->flush();
        //  uz neni locked
        $this->assertFalse($companyContact->isLocked());
       
        // pokus o čtení, institution uz  neni
        $companyContact2 = $this->companyContactRepo->get( self::$companyContact2->getId() );
        $this->assertNull($companyContact2);
    }

}


