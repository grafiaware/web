<?php
declare(strict_types=1);
namespace Test\Integration\Repository;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use PHPUnit\Framework\TestCase;

use Pes\Http\Factory\EnvironmentFactory;

use Application\WebAppFactory;

use Pes\Container\Container;

use Container\DbUpgradeContainerConfigurator;
use Container\RedModelContainerConfigurator;

use Test\AppRunner\AppRunner;

use Test\Integration\Red\Container\TestDbUpgradeContainerConfigurator;
use Test\Integration\Red\Container\TestHierarchyContainerConfigurator;

use Red\Model\Dao\Hierarchy\HierarchyAggregateReadonlyDao;
use Red\Model\Repository\MenuItemAggregatePaperRepo;
use Red\Model\Repository\PaperAggregateSectionsRepo;

use Red\Model\Entity\MenuItemAggregatePaperInterface;

// pro contents repo
use Pes\Database\Handler\HandlerInterface;
use Red\Model\Dao\PaperSectionDao;
use Red\Model\Hydrator\PaperSectionHydrator;
use Red\Model\Repository\PaperSectionRepo;

use Red\Model\Entity\PaperSectionInterface;
use Red\Model\Entity\PaperSection;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;


/**
 * Description of PaperContentDaoTest
 *
 * @author pes2704
 */
class MenuitemAggPaperContentManipulationTest  extends AppRunner {

    private $container;


    private $langCode;
    private $uid;

    /**
     *  @var MenuItemAggregatePaperRepo
     */
    private $menuItemAggRepo;

    /**
     * @var MenuItemAggregatePaperInterface
     */
    private $menuItemAgg;
    private $paper;
    
    // pomocná proměnná pro add
    private static $oldContentCount;

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
    }

    protected function setUp(): void {
        $this->container =
                (new TestHierarchyContainerConfigurator())->configure(
                           (new TestDbUpgradeContainerConfigurator())->configure(new Container())
                        );
        $this->menuItemAggRepo = $this->container->get(MenuItemAggregatePaperRepo::class);

        /** @var HierarchyAggregateReadonlyDao $hierarchyDao */
        $hierarchyDao = $this->container->get(HierarchyAggregateReadonlyDao::class);
        $this->langCode = 'cs';
        $this->title = 'Tests Integration';
        $node = $hierarchyDao->getByTitleHelper(['lang_code_fk'=>$this->langCode, 'title'=>$this->title]);
        if (!isset($node)) {
            
            throw new \LogicException("Error in setUp: Nelze spouštět integrační testy - v databázi '{$hierarchyDao->getSchemaName()}' není publikovaná položka menu v jazyce '$this->langCode' s názvem '$this->title'");
        }

        //  node.uid, (COUNT(parent.uid) - 1) AS depth, node.left_node, node.right_node, node.parent_uid
        $this->uid = $node['uid'];

        $this->menuItemAgg = $this->menuItemAggRepo->get($this->langCode, $this->uid);
        $this->paper = $this->menuItemAgg->getPaper();
        if (!$this->paper instanceof PaperAggregatePaperSectionInterface) {
            throw new \LogicException("Error in setUp: Nelze spustit integrační test - v setup() metodě nelze číst publikovaný paper.");
        }
    }
    
    protected function tearDown(): void {
        $this->menuItemAggRepo->flush();
    }
    
    public function testPaperHasSections() {
        $sections = $this->paper->getPaperSectionsArray();
        $this->assertIsArray($sections);
    }


    public function testAdd() {
        $oldContentsArray = $this->paper->getPaperSectionsArray();
        static::$oldContentCount = count($oldContentsArray);
        $paperIdFk = $this->paper->getId();
        $this->paper->setPaperSectionsArray($this->addSection($this->createSection($paperIdFk), $oldContentsArray));
        $this->assertTrue(count($this->paper->getPaperSectionsArray()) == static::$oldContentCount+1, "Není o jeden obsah více po testAdd.");
    }

    public function testCheckAdded() {
        $this->menuItemAgg = $this->menuItemAggRepo->get($this->langCode, $this->uid);
        $this->paper = $this->menuItemAgg->getPaper();
        $newContentsArray = $this->paper->getPaperSectionsArray();

        $this->assertTrue(count($newContentsArray) == static::$oldContentCount+1, "Není o jeden obsah více po testAdd.");

        // tohle nefunguje!
//        $this->assertTrue(count($newContentsArray) == count($oldContentsArray)+1, "Není o jeden obsah více po paper->exchangePaperContentsArray ");

    }

    public function testPaperContentType() {
        $this->assertInstanceOf(PaperSectionInterface::class, $this->paper->getPaperSectionsArray()[0]);
    }
    
    private function createSection($paperIdFk) {
        $newContent = new PaperSection();
        // paper_id_fk, active, priority, editor - jsou NOT NULL -> musí mít nastaveny hodnoty
        $newContent->setContent(file_get_contents('http://loripsum.net/api/3/short/headers'));
        $newContent->setPaperIdFk($paperIdFk);
        $newContent->setActive(1);
        $newContent->setShowTime((new \DateTime("now"))->modify("-1 week"));
        $newContent->setHideTime((new \DateTime("now"))->modify("+1 week"));
        $newContent->setEventStartTime(new \DateTime("now"));
        $newContent->setEventEndTime((new \DateTime("now"))->modify("1 day"));
        $newContent->setTemplateName('neexistujícíTemplate.php');
        $newContent->setTemplate("<h1>Content z testu</h1>");
        return $newContent;
    }

    private function addSection(PaperSectionInterface $newSectionContent, $oldContentsArray) {
        $newSectionContent->setPriority(count($oldContentsArray)+1);
        $oldContentsArray[] = $newSectionContent;
        return $oldContentsArray;
    }

//    public function testUpdate() {
//        $menuItemRow = $this->dao->get($this->langCode, $this->uid);
//        $oldActive = $menuItemRow['active'];
//        $this->assertIsInt($oldActive);
//        //
//        $this->setUp();
//        $menuItemRow['active'] = 1;
//        $this->dao->update($menuItemRow);
//        $this->setUp();
//        $menuItemRowRereaded = $this->dao->get($this->langCode, $this->uid);
//        $this->assertEquals($menuItemRow, $menuItemRowRereaded);
//        $this->assertEquals(1, $menuItemRowRereaded['active']);
//
//        $this->setUp();
//        $menuItemRow['active'] = 0;
//        $this->dao->update($menuItemRow);
//        $this->setUp();
//        $menuItemRowRereaded = $this->dao->get($this->langCode, $this->uid);
//        $this->assertEquals($menuItemRow, $menuItemRowRereaded);
//        $this->assertEquals(0, $menuItemRowRereaded['active']);
//
//        // vrácení původní hodnoty
//        $this->setUp();
//        $menuItemRow['active'] = $oldActive;
//        $this->dao->update($menuItemRow);
//        $this->setUp();
//        $this->dao = $this->container->get(MenuItemDao::class);
//        $menuItemRowRereaded = $this->dao->get($this->langCode, $this->uid);
//        $this->assertEquals($menuItemRow, $menuItemRowRereaded);
//        $this->assertEquals($oldActive, $menuItemRowRereaded['active']);
//
//    }
//
//    public function testDelete() {
//        $menuItemRow = $this->dao->get($this->langCode, $this->uid);
//        $this->expectException(\LogicException::class);
//        $this->dao->delete($menuItemRow);
//    }
}
