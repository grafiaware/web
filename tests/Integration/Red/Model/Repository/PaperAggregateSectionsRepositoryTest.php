<?php
declare(strict_types=1);

namespace Test\Integration\Red\Model\Repository;

use Pes\Container\Container;
use Red\Model\Dao\Hierarchy\HierarchyAggregateReadonlyDao;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;
use Red\Model\Repository\MenuItemAggregatePaperRepo;
use Red\Model\Repository\PaperAggregateSectionsRepo;
use Test\AppRunner\AppRunner;
use Test\Integration\Red\Container\TestDbUpgradeContainerConfigurator;
use Test\Integration\Red\Container\TestHierarchyContainerConfigurator;

final class PaperAggregateSectionsRepositoryTest extends AppRunner
{
    private PaperAggregateSectionsRepo $repo;
    private static int $menuItemId;

    public static function setUpBeforeClass(): void
    {
        self::bootstrapBeforeClass();
        $container = (new TestHierarchyContainerConfigurator())->configure(
            (new TestDbUpgradeContainerConfigurator())->configure(new Container())
        );

        /** @var HierarchyAggregateReadonlyDao $hierarchyDao */
        $hierarchyDao = $container->get(HierarchyAggregateReadonlyDao::class);
        $node = $hierarchyDao->getByTitleHelper(['lang_code_fk' => 'cs', 'title' => 'Tests Integration']);
        if ($node === null) {
            self::markTestSkipped('V DB chybí položka menu Tests Integration (cs).');
        }

        /** @var MenuItemAggregatePaperRepo $menuItemAggRepo */
        $menuItemAggRepo = $container->get(MenuItemAggregatePaperRepo::class);
        $aggregate = $menuItemAggRepo->get('cs', $node['uid']);
        self::$menuItemId = (int) $aggregate->getMenuItem()->getId();
    }

    protected function setUp(): void
    {
        $container = (new TestHierarchyContainerConfigurator())->configure(
            (new TestDbUpgradeContainerConfigurator())->configure(new Container())
        );
        $this->repo = $container->get(PaperAggregateSectionsRepo::class);
    }

    protected function tearDown(): void
    {
        $this->repo->flush();
    }

    public function testGetByMenuItemIdReturnsPaperWithSections(): void
    {
        $paper = $this->repo->getByMenuItemId(self::$menuItemId);
        $this->assertInstanceOf(PaperAggregatePaperSectionInterface::class, $paper);
        $this->assertSame(self::$menuItemId, (int) $paper->getMenuItemIdFk());
        $this->assertIsArray($paper->getPaperSectionsArray());
    }
}
