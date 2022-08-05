<?php
declare(strict_types=1);
namespace Test\Integration\Event\Model\Repository;

use Test\AppRunner\AppRunner;
use Pes\Container\Container;

//use Container\DbUpgradeContainerConfigurator;
//use Container\HierarchyContainerConfigurator;
use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Repository\InstitutionTypeAggregateInstitutionRepo;
use Events\Model\Entity\InstitutionTypeAggregateInstitution;
use Events\Model\Entity\InstitutionTypeAggregateInstitutionInterface;

use Events\Model\Entity\Institution;
use Events\Model\Entity\InstitutionInterface;
use Events\Model\Dao\InstitutionDao;
use Events\Model\Repository\InstitutionRepo;
use Events\Model\Repository\InstitutionTypeRepo;
use Events\Model\Dao\InstitutionTypeDao;

use Model\RowData\RowData;


/**
 * Description of InstitutionTypeAggregateInstitutionRepositoryTest
 *
 * @author vlse2610
 */
class InstitutionTypeAggregateInstitutionRepositoryTest extends AppRunner {   
    
    private $container;

    /**
     *
     * @var InstitutionTypeAggregateInstitutionRepo
     */
    private $institutionTypeAggregateInstitutionRepo;

    private static $institutionId1;
    private static $institutionId2;
    private static $institutionName = "proTestInstitutionTypeAggInstitutionRepo";
    
    private static $institutionTypeId;
    private static $institutionType = "proTestInstitutionTypeAggInstitutionRepo";
    


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
        /** @var InstitutionTypeDao $institutionTypeDao */
        $institutionTypeDao = $container->get(InstitutionTypeDao::class);  
        $rowData = new RowData();
        $rowData->import([
            'institution_type' => self::$institutionType /*. "1" */
        ]);
        $institutionTypeDao->insert($rowData);                
        self::$institutionTypeId = $institutionTypeDao->lastInsertIdValue();
         
          /** @var InstitutionDao $institutionDao */ //1
        $institutionDao = $container->get(InstitutionDao::class);  
        $rowData = new RowData();
        $rowData->import([
            'name' => self::$institutionName . "1",
            'institution_type_id' => self::$institutionTypeId
        ]);
        $institutionDao->insert($rowData);                
        self::$institutionId1 = $institutionDao->lastInsertIdValue();
        
        /** @var InstitutionDao $institutionDao */ //2
        $institutionDao = $container->get(InstitutionDao::class);  
        $rowData = new RowData();
        $rowData->import([
            'name' => self::$institutionName  . "2",
            'institution_type_id' => self::$institutionTypeId
        ]);
        $institutionDao->insert($rowData);                
        self::$institutionId2 = $institutionDao->lastInsertIdValue();
       
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
        $this->institutionTypeAggregateInstitutionRepo = $this->container->get(InstitutionTypeAggregateInstitutionRepo::class);

    }
    
    
    
    protected function tearDown(): void {
        //$this->institutionTypeAggregateInstitutionRepo->flush();
        $this->institutionTypeAggregateInstitutionRepo->__destruct();
    }
    
    
    
    public static function tearDownAfterClass(): void {
        $container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        self::deleteRecords($container);
    }

    
    public function testSetUp() {        
        $this->assertInstanceOf( InstitutionTypeAggregateInstitutionRepo::class, $this->institutionTypeAggregateInstitutionRepo );
    }

    
    /**
     * $this->institutionTypeAggregateInstitutionRepo->get() vrací aggregovanou entitu  s agregovanými všemi Instituties (pole),
     * které mají  jako cizi klic v Institution.event_id_fk klic InstitutionType.id.
     * Instituties (pole)  je  privatni vlastnost $institutions = []  entity InstitutionTypeAggregateInstitution.
     * InstitutionType-parent, Institution-child
     */
    public function testGet() {
        /**  @var InstitutionTypeAggregateInstitution $institutionTypeAgg */
        $institutionTypeAgg = $this->institutionTypeAggregateInstitutionRepo->get( self::$institutionTypeId );                       
        $this->assertInstanceOf(InstitutionTypeAggregateInstitutionInterface::class, $institutionTypeAgg);
        $institutions = $institutionTypeAgg->getInstitutionsArray();
        $this->assertEquals ( self::$institutionType, $institutionTypeAgg->getInstitutionType());
        
        $this->assertTrue(is_array($institutions));
        $this->assertIsArray($institutions);
        $this->assertGreaterThan(0,count($institutions)); //jsou tam minimalne 2
        $this->assertInstanceOf(Institution::class, $institutions[0]);  

    }
    
    

    public function testFindAll() {        
        /**  @var InstitutionTypeAggregateInstitution $institutionTypeAgg */
        $institutionTypeAgg = $this->institutionTypeAggregateInstitutionRepo->findAll();     
        $this->assertTrue(is_array($institutionTypeAgg));        
    }

    
    
    public function testUpdateInstitution_I_nastaveni() {
         /**  @var InstitutionTypeAggregateInstitution $institutionTypeAgg */
        $institutionTypeAgg = $this->institutionTypeAggregateInstitutionRepo->get( self::$institutionTypeId ); 
        $institutionsArray = $institutionTypeAgg->getInstitutionsArray();
        $this->assertIsArray($institutionsArray);
        foreach ($institutionsArray as $key => $value) {
            $institutionsArray[$key]->setName(self::$institutionName . "upgrade");
        }            
    }    
    public function testUpdateInstitution_II_kontrola() {
         /**  @var InstitutionTypeAggregateInstitution $institutionTypeAgg */
        $institutionTypeAgg = $this->institutionTypeAggregateInstitutionRepo->get( self::$institutionTypeId ); 
        $institutionsArray = $institutionTypeAgg->getInstitutionsArray();
        foreach ($institutionsArray as $key => $value) {
            $institutionsArray[$key]->setName(self::$institutionName . "upgrade");
            $this->assertEquals( self::$institutionName . "upgrade",  $institutionsArray[$key]->getName() );
        }
            
    }

}
