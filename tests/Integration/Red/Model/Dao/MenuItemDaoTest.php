<?php
declare(strict_types=1);

use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Test\Integration\Red\Container\TestHierarchyContainerConfigurator;
use Test\Integration\Red\Container\TestDbUpgradeContainerConfigurator;

use Red\Model\Dao\Hierarchy\HierarchyAggregateReadonlyDao;

use Red\Model\Dao\MenuItemDao;

use Model\RowData\RowDataInterface;

use Model\Dao\Exception\DaoForbiddenOperationException;

/**
 * Description of MenuItemDaoTest
 *
 * @author pes2704
 */
class MenuItemDaoTest extends AppRunner {

    private $container;

    /**
     *
     * @var MenuItemDao
     */
    private $dao;

    private $id;



    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();


    }

    protected function setUp(): void {
        $this->container =
                (new TestHierarchyContainerConfigurator())->configure(
                           (new TestDbUpgradeContainerConfigurator())->configure(new Container())
                        );
        $this->dao = $this->container->get(MenuItemDao::class);  // vždy nový objekt

        /** @var HierarchyAggregateReadonlyDao $hierarchy */
        $hierarchy = $this->container->get(HierarchyAggregateReadonlyDao::class);
        $node = $hierarchy->getByTitleHelper(['lang_code_fk'=>'cs', 'title'=>'Tests Integration']);
        if (!isset($node)) {
            throw new \LogicException("Nelze spouštět integrační testy - v databázi projektu není položka menu v jazyce cs' s názvem 'Tests Integration'");
        }
        //  node.uid, (COUNT(parent.uid) - 1) AS depth, node.left_node, node.right_node, node.parent_uid
        $this->id = ['lang_code_fk'=>'cs', 'uid_fk'=>$node['uid']];
    }

    public function testGetExistingRow() {
        $menuItemRow = $this->dao->get($this->id);
        $this->assertInstanceOf(RowDataInterface::class, $menuItemRow);
    }

    public function test7Columns() {
        $menuItemRow = $this->dao->get($this->id);
        $this->assertCount(7, $menuItemRow);
    }

    public function testUpdate() {
        $menuItemRow = $this->dao->get($this->id);
        $oldActive = $menuItemRow['active'];
        $this->assertIsInt($oldActive);
        //
        $this->setUp();
        $menuItemRow['active'] = 1;
        $this->dao->update($menuItemRow);
        $this->setUp();
        $menuItemRowRereaded = $this->dao->get($this->id, $this->uid);
        $this->assertEquals($menuItemRow, $menuItemRowRereaded);
        $this->assertEquals(1, $menuItemRowRereaded['active']);

        $this->setUp();
        $menuItemRow['active'] = 0;
        $this->dao->update($menuItemRow);
        $this->setUp();
        $menuItemRowRereaded = $this->dao->get($this->id, $this->uid);
        $this->assertEquals($menuItemRow, $menuItemRowRereaded);
        $this->assertEquals(0, $menuItemRowRereaded['active']);

        // vrácení původní hodnoty
        $this->setUp();
        $menuItemRow['active'] = $oldActive;
        $this->dao->update($menuItemRow);
        $this->setUp();
        $this->dao = $this->container->get(MenuItemDao::class);
        $menuItemRowRereaded = $this->dao->get($this->id, $this->uid);
        $this->assertEquals($menuItemRow, $menuItemRowRereaded);
        $this->assertEquals($oldActive, $menuItemRowRereaded['active']);

    }

    public function testInsertException() {
        $menuItemRow = $this->dao->get($this->id);
        $this->expectException(DaoForbiddenOperationException::class);
        $this->dao->insert($menuItemRow);
    }

    public function testDeleteException() {
        $menuItemRow = $this->dao->get($this->id);
        $this->expectException(DaoForbiddenOperationException::class);
        $this->dao->delete($menuItemRow);
    }
}
