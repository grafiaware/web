<?php
declare(strict_types=1);

namespace Test\Integration\Red\Model\Dao;

use Pes\Container\Container;
use Pes\Model\RowData\RowData;
use Pes\Model\RowData\RowDataInterface;
use Red\Model\Dao\Hierarchy\HierarchyAggregateReadonlyDao;
use Red\Model\Dao\MenuItemDao;
use Red\Model\Dao\PaperDao;
use Red\Model\Repository\MenuItemAggregatePaperRepo;
use Test\AppRunner\AppRunner;
use Test\Integration\Red\Container\TestDbUpgradeContainerConfigurator;
use Test\Integration\Red\Container\TestHierarchyContainerConfigurator;

final class PaperDaoTest extends AppRunner
{
    private PaperDao $dao;
    private static int $menuItemId;
    private static int $paperId;

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
        $this->dao = $container->get(PaperDao::class);
    }

    public function testInsertGetUpdateDelete(): void
    {
        $row = new RowData([
            'menu_item_id_fk' => self::$menuItemId,
            'headline' => 'PaperDaoTest ' . uniqid(),
            'perex' => 'Perex',
            'template' => 'default.php',
        ]);
        $this->dao->insert($row);
        self::$paperId = (int) $this->dao->getLastInsertedPrimaryKey()['id'];

        $loaded = $this->dao->get(['id' => self::$paperId]);
        $this->assertInstanceOf(RowDataInterface::class, $loaded);
        $this->assertSame(self::$menuItemId, (int) $loaded['menu_item_id_fk']);

        $loaded['headline'] = 'PaperDaoTest updated';
        $this->assertTrue($this->dao->update($loaded));

        $this->setUp();
        $updated = $this->dao->get(['id' => self::$paperId]);
        $this->assertSame('PaperDaoTest updated', $updated['headline']);

        $this->assertTrue($this->dao->delete($updated));
    }
}
