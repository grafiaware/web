<?php
declare(strict_types=1);
namespace Test\Integration\Event\Model\Repository;

use Test\AppRunner\AppRunner;
use Pes\Container\Container;

use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Entity\Institution;
use Events\Model\Entity\InstitutionInterface;
use Events\Model\Dao\InstitutionDao;
use Events\Model\Repository\InstitutionRepo;
use Events\Model\Dao\InstitutionTypeDao;

use Model\Repository\Exception\OperationWithLockedEntityException;
use Model\RowData\RowData;



 /**
 * Description of InstitutionRepositoryTest
 *
 * @author vlse2610
 */ 
class InstitutionRepositoryTest extends AppRunner {
    private $container;

    /**
     *
     * @var InstitutionRepo
     */
    private $institutionRepo;
    
    private static $institutionId;
    private static $institutionName = "testInstitutionRepo";
    
    private static $institutionTypeId;
    private static $institutionType = "proTestInstitutionRepo";
    
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

    private static function insertRecords(Container $container) {
        /** @var InstitutionDao $institutionDao */
        $institutionDao = $container->get(InstitutionDao::class);  
        $rowData = new RowData();
        $rowData->import([
            'name' => self::$institutionName
        ]);
        $institutionDao->insert($rowData);                
        self::$institutionId = $institutionDao->lastInsertIdValue();
        
        //------------------------------
        /** @var InstitutionTypeDao $institutionTypeDao */
        $institutionTypeDao = $container->get(InstitutionTypeDao::class);  
        $rowData = new RowData();
        $rowData->import([
            'institution_type' => self::$institutionType
        ]);
        $institutionTypeDao->insert($rowData);                
        self::$institutionTypeId = $institutionTypeDao->lastInsertIdValue();
    }

    private static function deleteRecords(Container $container) {                      
        /** @var InstitutionDao $institutionDao */
        $institutionDao = $container->get(InstitutionDao::class);        
        $rows = $institutionDao->find( "name LIKE '". self::$institutionName . "%'", [] );               
        foreach($rows as $row) {
            $ok = $institutionDao->delete($row);
        }
                
         /** @var InstitutionTypeDao $institutionTypeDao */
        $institutionTypeDao = $container->get(InstitutionTypeDao::class);        
        $rows = $institutionTypeDao->find( "institution_type LIKE '". self::$institutionType . "%'", [] );               
        foreach($rows as $row) {
            $ok = $institutionTypeDao->delete($row);
        }
        
    }

    protected function setUp(): void {
        $this->container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        $this->institutionRepo = $this->container->get(InstitutionRepo::class);
    }

    protected function tearDown(): void {
        $this->institutionRepo->flush();
    }

    public static function tearDownAfterClass(): void {
        $container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );

        self::deleteRecords($container);
    }

    public function testSetUp() {
        $this->assertInstanceOf(InstitutionRepo::class, $this->institutionRepo);
    }

    public function testGetNonExisted() {
        $institution = $this->institutionRepo->get(-1);
        $this->assertNull($institution);
    }

    public function testGetAfterSetup() {
        $institution = $this->institutionRepo->get(self::$institutionId);    
        $this->assertInstanceOf(InstitutionInterface::class, $institution);
    }

 
    
    public function testAdd() {
        $institution = new Institution();
        $institution->setName(self::$institutionName);                       
       
        $this->institutionRepo->add($institution);
        $this->assertTrue($institution->isPersisted());  // !!!!!! InstitutionDao ma DaoEditAutoincrementKeyInterface, k zápisu dojde ihned !!!!!!!
        // pro automaticky|generovany klic (tento pripad zde ) a  pro  overovany klic  - !!! zapise se hned !!!       
        $this->assertFalse($institution->isLocked());
    }

    
    public function testAddAndReread_I() {
        $institution = new Institution();       
        $institution->setName(self::$institutionName);        

        $this->institutionRepo->add($institution); //zapise hned
        /** @var Institution $institutionRereaded */
        $institutionRereaded = $this->institutionRepo->get($institution->getId());
        $this->assertInstanceOf(InstitutionInterface::class, $institutionRereaded);
        $this->assertTrue($institutionRereaded->isPersisted());
        $this->assertFalse($institutionRereaded->isLocked());
       
        //$institutionRereaded, $institution ... jsou odkazy na stejny objekt              
        self::$idI = $institution->getId();
        self::$idR = $institutionRereaded->getId();
        $this->assertEquals(self::$idI, self::$idR );
        
        $institution->setInstitutionTypeId(self::$institutionTypeId); //zapise do entity, ktera je v repository
    }    
    
    public function testAddAndReread_II() {        
        $institution = $this->institutionRepo->get(self::$idI);
    
        /** @var Institution $institutionRereaded2 */
        $institutionRereaded2 = $this->institutionRepo->get($institution->getId());
        $this->assertInstanceOf(InstitutionInterface::class, $institutionRereaded2);
        $this->assertTrue($institutionRereaded2->isPersisted());
        $this->assertFalse($institutionRereaded2->isLocked());

        $this->assertEquals(self::$institutionTypeId, $institutionRereaded2->getInstitutionTypeId());
    }

    
    public function testFindAll() {
        $institutionsArray = $this->institutionRepo->findAll();
        $this->assertTrue(is_array($institutionsArray));
        $this->assertGreaterThan(0,count($institutionsArray)); //jsou tam minimalne 2
    }

    
    public function testFind() {      
        $institutionsArray = $this->institutionRepo->find( "name LIKE '" . self::$institutionName . "%'", []); 
        $this->assertTrue(is_array($institutionsArray));
        $this->assertGreaterThan(0,count($institutionsArray)); //jsou tam minimalne 2
                       
    }

    public function testRemove_OperationWithLockedEntity() {
        $institution = $this->institutionRepo->get(self::$institutionId);    
        $this->assertInstanceOf(InstitutionInterface::class, $institution);
        $this->assertTrue($institution->isPersisted());
        $this->assertFalse($institution->isLocked());
        
        $institution->lock();
        $this->expectException( OperationWithLockedEntityException::class);
        $this->institutionRepo->remove($institution);
    }

    
    public function testRemove() {
        $institution = $this->institutionRepo->get(self::$institutionId);    
        $this->assertInstanceOf(InstitutionInterface::class, $institution);
        $this->assertTrue($institution->isPersisted());
        $this->assertFalse($institution->isLocked());

        $this->institutionRepo->remove($institution);
        
        $this->assertTrue($institution->isPersisted());
        $this->assertTrue($institution->isLocked());   // maže až při flush
        $this->institutionRepo->flush();
        //  uz neni locked
        $this->assertFalse($institution->isLocked());
       
        // pokus o čtení, institution uz  neni
        $institution = $this->institutionRepo->get(self::$institutionId);
        $this->assertNull($institution);
    }

}

