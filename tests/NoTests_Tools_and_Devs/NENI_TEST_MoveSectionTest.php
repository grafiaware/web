<?php
declare(strict_types=1);
namespace Test\Integration\Repository;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Pes\Container\Container;

use Test\AppRunner\AppRunner;

use Red\Model\Repository\PaperSectionRepo;

use Test\Integration\Red\Container\TestDbUpgradeContainerConfigurator;
use Test\Integration\Red\Container\TestHierarchyContainerConfigurator;

use Red\Model\Repository\PaperSectionRepoInterface;

use Model\Context\ContextProviderInterface;

//use Red\Model\Dao\Hierarchy\HierarchyAggregateReadonlyDao;
//use Red\Model\Repository\MenuItemAggregatePaperRepo;
//
//use Red\Model\Entity\MenuItemAggregatePaperInterface;
//
//use Red\Model\Entity\PaperSection;
//use Red\Model\Entity\PaperAggregatePaperSectionInterface;


/**
 * Description of PaperContentDaoTest
 *
 * @author pes2704
 */
class NENI_TEST_MoveSectionTest  extends AppRunner {

    private $container;


    private $langCode;
    private $sectionId;

    /**
     *  @var  PaperSectionRepoInterface
     */
    private $paperSectionRepo;
    
    
    

//    /**
//     * @var MenuItemAggregatePaperInterface
//     */
//    private $menuItemAgg;
//    private $paper;
    
    // pomocná proměnná pro add
   // private static $oldContentCount;

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
    }

    protected function setUp(): void {
        $this->container =
                           
                (new TestHierarchyContainerConfigurator())->configure(
                           (new TestDbUpgradeContainerConfigurator())->configure(new Container())
                        );        
        $n=1;
        /** @var ContextProviderInterface $contextProvider */
        $contextProvider = $this->container->get(ContextProviderInterface::class);
        $contextProvider->forceShowOnlyPublished(false);
        $this->paperSectionRepo = $this->container->get(PaperSectionRepo::class);
        
                /** @var PaperSectionInterface $section */
        $section = $this->paperSectionRepo->get(560);
        $sections = $this->paperSectionRepo->findByPaperIdFk($section->getPaperIdFk());
        //$selectedSectionPriority = $section->getPriority();
        
        
        
        
        
        
        

//        /** @var HierarchyAggregateReadonlyDao $hierarchyDao */
//        $hierarchyDao = $this->container->get(HierarchyAggregateReadonlyDao::class);
//        $this->langCode = 'cs';
//        $this->title = 'DIVIDE';
//        $node = $hierarchyDao->getByTitleHelper(['lang_code_fk'=>$this->langCode, 'title'=>$this->title]);
//        if (!isset($node)) {
//            
//            throw new \LogicException("Error in setUp: Nelze spouštět integrační testy - v databázi '{$hierarchyDao->getSchemaName()}' není publikovaná položka menu v jazyce '$this->langCode' s názvem '$this->title'");
//        }
//
//        //  node.uid, (COUNT(parent.uid) - 1) AS depth, node.left_node, node.right_node, node.parent_uid
//        $this->uid = $node['uid'];
//
//        $this->menuItemAgg = $this->menuItemAggRepo->get($this->langCode, $this->uid);
//        $this->paper = $this->menuItemAgg->getPaper();
//        if (!$this->paper instanceof PaperAggregatePaperSectionInterface) {
//            throw new \LogicException("Error in setUp: Nelze spustit integrační test - v setup() metodě nelze číst publikovaný paper.");
//        }
    }
    
    protected function tearDown(): void {
        $this->paperSectionRepo->flush();
    }

    
    
    public function test() {
        
        
        
            
    }
    
    
    
//    public function testDivide() {
//        $this->assertIsReadable(__DIR__.'/Divide.html');
//        $text = file_get_contents(__DIR__.'/Divide.html');
//        $pattern ='<div class="blok_text_obrazek">';
//        $divided = preg_split('/'.$pattern.'/', $text, -1, PREG_SPLIT_NO_EMPTY);
//        $sectionContents = array_map(function($str) use ($pattern) {return $pattern.$str;}, $divided);
//        $paperIdFk = $this->paper->getId();
//        $sections = [];
//        $cnt = count($sectionContents);  // priorita musí být od nejvyšší
//        foreach ($sectionContents as $key=>$part) {
//            $sections[]=$this->createSection($paperIdFk, $part, $cnt-$key);
//        }
//        $this->paper->setPaperSectionsArray($sections);
//        $this->assertTrue(1);
//    }
//
//    public function testCheckAdded() {
//        $this->menuItemAgg = $this->menuItemAggRepo->get($this->langCode, $this->uid);
//        $this->paper = $this->menuItemAgg->getPaper();
//        $newContentsArray = $this->paper->getPaperSectionsArray();
//
//        $this->assertTrue(count($newContentsArray) == static::$oldContentCount+1, "Není o jeden obsah více po testAdd.");
//
//        // tohle nefunguje!
////        $this->assertTrue(count($newContentsArray) == count($oldContentsArray)+1, "Není o jeden obsah více po paper->exchangePaperContentsArray ");
//
//    }
//
//    private function createSection($paperIdFk, $part, $key) {
//        $section = new PaperSection();
//        // paper_id_fk, active, priority, editor - jsou NOT NULL -> musí mít nastaveny hodnoty
//        $section->setContent($part);
//        $section->setPaperIdFk($paperIdFk);
//        $section->setActive(1);
//        $section->setPriority($key+1);
//        
//        return $section;
//    }

}
