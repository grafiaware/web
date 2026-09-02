<?php
declare(strict_types=1);

namespace Test\Integration\Red\Model\Dao;

use Pes\Container\Container;
use Pes\Model\RowData\RowData;
use Pes\Model\RowData\RowDataInterface;
use Red\Model\Dao\Hierarchy\HierarchyAggregateReadonlyDao;
use Red\Model\Dao\PaperDao;
use Red\Model\Dao\PaperSectionDao;
use Red\Model\Entity\PaperAggregatePaperSectionInterface;
use Red\Model\Repository\MenuItemAggregatePaperRepo;
use Test\AppRunner\AppRunner;
use Test\Integration\Red\Container\TestDbUpgradeContainerConfigurator;
use Test\Integration\Red\Container\TestHierarchyContainerConfigurator;

final class PaperSectionDaoTest extends AppRunner
{
    private PaperSectionDao $dao;
    private static int $paperId;
    private static int $sectionId;

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
        $paper = $aggregate->getPaper();
        if (!$paper instanceof PaperAggregatePaperSectionInterface) {
            self::markTestSkipped('Tests Integration nemá publikovaný paper.');
        }
        self::$paperId = (int) $paper->getId();
    }

    protected function setUp(): void
    {
        $container = (new TestHierarchyContainerConfigurator())->configure(
            (new TestDbUpgradeContainerConfigurator())->configure(new Container())
        );
        $this->dao = $container->get(PaperSectionDao::class);
    }

    public static function tearDownAfterClass(): void
    {
        if (!isset(self::$sectionId)) {
            return;
        }
        $container = (new TestHierarchyContainerConfigurator())->configure(
            (new TestDbUpgradeContainerConfigurator())->configure(new Container())
        );
        /** @var PaperSectionDao $dao */
        $dao = $container->get(PaperSectionDao::class);
        $row = $dao->get(['id' => self::$sectionId]);
        if ($row !== null) {
            $dao->delete($row);
        }
    }

    public function testInsertGetUpdateDelete(): void
    {
        $now = (new \DateTime())->format('Y-m-d H:i:s');
        $row = new RowData([
            'paper_id_fk' => self::$paperId,
            'content' => '<p>PaperSectionDaoTest</p>',
            'template_name' => 'section.php',
            'template' => '<p>template</p>',
            'active' => 1,
            'priority' => 999,
            'show_time' => $now,
            'hide_time' => $now,
            'event_start_time' => $now,
            'event_end_time' => $now,
            'editor' => 'PaperSectionDaoTest',
        ]);
        $this->dao->insert($row);
        self::$sectionId = (int) $this->dao->getLastInsertedPrimaryKey()['id'];

        $loaded = $this->dao->get(['id' => self::$sectionId]);
        $this->assertInstanceOf(RowDataInterface::class, $loaded);

        $loaded['content'] = '<p>updated</p>';
        $this->assertTrue($this->dao->update($loaded));

        $this->setUp();
        $updated = $this->dao->get(['id' => self::$sectionId]);
        $this->assertSame('<p>updated</p>', $updated['content']);

        $this->assertTrue($this->dao->delete($updated));
        self::$sectionId = 0;
    }
}
