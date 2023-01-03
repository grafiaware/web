<?php
declare(strict_types=1);
namespace Test\Integration\Repository;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Container\DbUpgradeContainerConfigurator;
use Container\RedModelContainerConfigurator;
use Test\Integration\Red\Container\TestDbUpgradeContainerConfigurator;
use Test\Integration\Red\Container\TestHierarchyContainerConfigurator;

use Red\Model\Dao\Hierarchy\HierarchyAggregateReadonlyDao;
use Red\Model\Repository\MenuItemAggregatePaperRepo;

use Red\Model\Entity\MenuItemAggregatePaper;
use Red\Model\Entity\PaperAggregatePaperSection;
/**
 * Description of MenuItemPaperRepositoryTest
 *
 * @author pes2704
 */
class MenuItemAggregateRepositoryTest extends AppRunner {

    private $container;

    /**
     *
     * @var MenuItemAggregatePaperRepo
     */
    private $menuItemAggRepo;

    private $title;
    private $langCode;
    private $uid;
    private $id;
    private $prettyUri;


    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
    }

    protected function setUp(): void {
        $this->container =
                (new TestHierarchyContainerConfigurator())->configure(  // přepisuje ContextFactory
                    (new RedModelContainerConfigurator())->configure(
                       (new DbUpgradeContainerConfigurator())->configure(new Container())
                    )
                );


        $this->menuItemAggRepo = $this->container->get(MenuItemAggregatePaperRepo::class);

        /** @var HierarchyAggregateReadonlyDao $hierarchyDao */
        $hierarchyDao = $this->container->get(HierarchyAggregateReadonlyDao::class);
        $this->langCode = 'cs';
        $this->title = 'Tests Integration';
        $node = $hierarchyDao->getByTitleHelper($this->langCode, $this->title);
        if (!isset($node)) {
            throw new \LogicException("Error in setUp: Nelze spouštět integrační testy - v databázi projektu není položka menu v jazyce '$this->langCode' s názvem '$this->title'");
        }
        //  node.uid, (COUNT(parent.uid) - 1) AS depth, node.left_node, node.right_node, node.parent_uid
        $this->uid = $node['uid'];
        $this->id = $node['id'];
        $this->prettyUri = $node['prettyUri'] ?? null;
    }

    public function testSetUp() {
        $this->assertIsString($this->langCode);
        $this->assertIsString($this->uid);
        $this->assertInstanceOf(MenuItemAggregatePaperRepo::class, $this->menuItemAggRepo);
    }

    public function testGet() {
        $entity = $this->menuItemAggRepo->get($this->langCode, $this->uid);
        $this->assertInstanceOf(MenuItemAggregatePaper::class, $entity);
        $this->assertEquals($this->title, $entity->getTitle());
        /** @var PaperAggregatePaperSection $paper */      // není interface
        $paper = $entity->getPaper();
        $this->assertInstanceOf(PaperAggregatePaperSection::class, $paper);
        $contents = $paper->getPaperContentsArray();
        $this->assertIsArray($contents);
        $this->assertTrue(count($contents)>0, "Nenalezen žádný obsah");

    }

    public function testGetById() {
        $entity = $this->menuItemAggRepo->getById($this->id);
        $this->assertInstanceOf(MenuItemAggregatePaper::class, $entity);
        $this->assertEquals($this->title, $entity->getTitle());
        /** @var PaperAggregatePaperSection $paper */      // není interface
        $paper = $entity->getPaper();
        $this->assertInstanceOf(PaperAggregatePaperSection::class, $paper);
        $contents = $paper->getPaperContentsArray();
        $this->assertIsArray($contents);
        $this->assertTrue(count($contents)>0, "Nenalezen žádný obsah");

    }

    public function testFindByPaperFulltextSearch() {
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );

        $items = $this->menuItemAggRepo->findByPaperFulltextSearch($this->langCode, "Grafia", false, false);
        $this->assertIsArray($items);
        $this->assertTrue(count($items) > 0, 'Nebyl nalezen alespoň jeden výskyt řetězce.');
    }

}
