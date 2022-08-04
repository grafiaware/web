<?php
declare(strict_types=1);
namespace Test\Integration\Event\Model\Repository;

use Test\AppRunner\AppRunner;
use Pes\Container\Container;

//use Container\DbUpgradeContainerConfigurator;
//use Container\HierarchyContainerConfigurator;
use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

//use Test\Integration\Red\Container\TestHierarchyContainerConfigurator;
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

//use Red\Model\Dao\Hierarchy\HierarchyAggregateReadonlyDao;
//use Red\Model\Repository\MenuItemAggregatePaperRepo;

//use Red\Model\Entity\MenuItemAggregatePaper;
//use Red\Model\Entity\PaperAggregatePaperSection;



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

    private static $institutionId;
    private static $institutionName = "proTestInstitutionTypeAggInstitutionRepo";
    
    private static $institutionTypeId;
    private static $institutionType = "proTestInstitutionTypeAggInstitutionRepo";
    
//    private $title;
//    private $langCode;
//    private $uid;
//    private $id;
//    private $prettyUri;


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
         
          /** @var InstitutionDao $institutionDao */
        $institutionDao = $container->get(InstitutionDao::class);  
        $rowData = new RowData();
        $rowData->import([
            'name' => self::$institutionName,
            'institution_type_id' => self::$institutionTypeId
        ]);
        $institutionDao->insert($rowData);                
        self::$institutionId = $institutionDao->lastInsertIdValue();
        
        $institutionDao = $container->get(InstitutionDao::class);  
        $rowData = new RowData();
        $rowData->import([
            'name' => self::$institutionName,
            'institution_type_id' => self::$institutionTypeId
        ]);
        $institutionDao->insert($rowData);                
        self::$institutionId = $institutionDao->lastInsertIdValue();
        //------------------------------        
         
    }

    private static function deleteRecords(Container $container) {
        
//         /** @var InstitutionDao $institutionDao */
//        $institutionDao = $container->get(InstitutionDao::class);        
//        $rows = $institutionDao->find( "name LIKE '". self::$institutionName . "%'", [] );               
//        foreach($rows as $row) {
//            $ok = $institutionDao->delete($row);
//        }
//                
//         /** @var InstitutionTypeDao $institutionTypeDao */
//        $institutionTypeDao = $container->get(InstitutionTypeDao::class);        
//        $rows = $institutionTypeDao->find( "institution_type LIKE '". self::$institutionType . "%'", [] );               
//        foreach($rows as $row) {
//            $ok = $institutionTypeDao->delete($row);
//        }
        
        
    }
    
    
    
    protected function setUp(): void {
        $this->container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        $this->institutionTypeAggregateInstitutionRepo = $this->container->get(InstitutionTypeAggregateInstitutionRepo::class);


//        /** @var HierarchyAggregateReadonlyDao $hierarchyDao */
//        $hierarchyDao = $this->container->get(HierarchyAggregateReadonlyDao::class);
//        $this->langCode = 'cs';
//        $this->title = 'Tests Integration';
//        $node = $hierarchyDao->getByTitleHelper($this->langCode, $this->title);
//        if (!isset($node)) {
//            throw new \LogicException("Error in setUp: Nelze spouštět integrační testy - v databázi projektu není položka menu v jazyce '$this->langCode' s názvem '$this->title'");
//        }
//        //  node.uid, (COUNT(parent.uid) - 1) AS depth, node.left_node, node.right_node, node.parent_uid
//        $this->uid = $node['uid'];
//        $this->id = $node['id'];
//        $this->prettyUri = $node['prettyUri'] ?? null;
    }
    
    
    protected function tearDown(): void {
        $this->institutionTypeAggregateInstitutionRepo->flush();
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

    
    
    public function testGet() {
        /**  @var InstitutionTypeAggregateInstitution $institutionTypeAgg */
        $institutionTypeAgg = $this->institutionTypeAggregateInstitutionRepo->get( self::$institutionTypeId );                       
        $this->assertInstanceOf(InstitutionTypeAggregateInstitutionInterface::class, $institutionTypeAgg);
        $institutions = $institutionTypeAgg->getInstitutionsArray();
        $this->assertEquals ( self::$institutionType, $institutionTypeAgg->getInstitutionType());
        
        
        
        
        
        
//        $this->assertEquals($this->title, $entity->getTitle());
//        /** @var PaperAggregatePaperSection $paper */      // není interface
//        $paper = $entity->getPaper();
//        
//        $this->assertInstanceOf(PaperAggregatePaperSection::class, $paper);
//        $contents = $paper->getPaperContentsArray();
//        $this->assertIsArray($contents);
//        $this->assertTrue(count($contents)>0, "Nenalezen žádný obsah");

    }
    
    

    public function testGetById() {
//        $entity = $this->menuItemAggRepo->getById($this->id);
//        $this->assertInstanceOf(MenuItemAggregatePaper::class, $entity);
//        $this->assertEquals($this->title, $entity->getTitle());
//        /** @var PaperAggregatePaperSection $paper */      // není interface
//        $paper = $entity->getPaper();
//        $this->assertInstanceOf(PaperAggregatePaperSection::class, $paper);
//        $contents = $paper->getPaperContentsArray();
//        $this->assertIsArray($contents);
//        $this->assertTrue(count($contents)>0, "Nenalezen žádný obsah");

    }

    public function testFindByPaperFulltextSearch() {
//        // Stop here and mark this test as incomplete.
//        $this->markTestIncomplete(
//          'This test has not been implemented yet.'
//        );
//
//        $items = $this->menuItemAggRepo->findByPaperFulltextSearch($this->langCode, "Grafia", false, false);
//        $this->assertIsArray($items);
//        $this->assertTrue(count($items) > 0, 'Nebyl nalezen alespoň jeden výskyt řetězce.');
    }

}
