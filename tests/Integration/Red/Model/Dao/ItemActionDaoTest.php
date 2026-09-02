<?php
declare(strict_types=1);

namespace Test\Integration\Red\Model\Dao;

use Pes\Container\Container;
use Pes\Model\RowData\RowData;
use Pes\Model\RowData\RowDataInterface;
use Red\Model\Dao\Hierarchy\HierarchyAggregateReadonlyDao;
use Red\Model\Dao\ItemActionDao;
use Red\Model\Repository\MenuItemAggregatePaperRepo;
use Test\AppRunner\AppRunner;
use Test\Integration\Red\Container\TestDbUpgradeContainerConfigurator;
use Test\Integration\Red\Container\TestHierarchyContainerConfigurator;

final class ItemActionDaoTest extends AppRunner
{
    private ItemActionDao $dao;
    private static int $itemId;
    private const EDITOR = 'ItemActionDaoTest_editor';

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
        self::$itemId = (int) $aggregate->getMenuItem()->getId();
    }

    protected function setUp(): void
    {
        $container = (new TestHierarchyContainerConfigurator())->configure(
            (new TestDbUpgradeContainerConfigurator())->configure(new Container())
        );
        $this->dao = $container->get(ItemActionDao::class);
    }

    public static function tearDownAfterClass(): void
    {
        $container = (new TestHierarchyContainerConfigurator())->configure(
            (new TestDbUpgradeContainerConfigurator())->configure(new Container())
        );
        /** @var ItemActionDao $dao */
        $dao = $container->get(ItemActionDao::class);
        $row = $dao->get(['item_id' => self::$itemId, 'editor_login_name' => self::EDITOR]);
        if ($row !== null) {
            $dao->delete($row);
        }
    }

    public function testInsertGetDelete(): void
    {
        $row = new RowData([
            'item_id' => self::$itemId,
            'editor_login_name' => self::EDITOR,
        ]);
        $this->dao->insert($row);

        $loaded = $this->dao->get([
            'item_id' => self::$itemId,
            'editor_login_name' => self::EDITOR,
        ]);
        $this->assertInstanceOf(RowDataInterface::class, $loaded);

        $this->assertTrue($this->dao->delete($loaded));
    }
}
