<?php
declare(strict_types=1);

use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Container\DbUpgradeContainerConfigurator;
use Container\RedModelContainerConfigurator;
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

    private $primaryKey;

    private $activeBeforeTest;

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
        $this->primaryKey = ['lang_code_fk'=>'cs', 'uid_fk'=>$node['uid']];
    }

    protected function tearDown(): void {
        $this->container->flush();
    }

    /**
     * Čte s kontextem (jen aktivní). Podmínkou celého testu je existence menu item s příslušných názvem (title) . viz setUp()
     * a to, že je tento item aktivní. 
     */
    public function testGetExistingRow() {
        $menuItemRow = $this->dao->get($this->primaryKey);
        $this->assertInstanceOf(RowDataInterface::class, $menuItemRow, "Selhává dao->get() nebo položka pro test není aktivní.");
    }

    /**
     *
     */
    public function test8Columns() {
        $menuItemRow = $this->dao->get($this->primaryKey);
        $this->assertCount(8, $menuItemRow);
    }

    /**
     *
     */
    public function testUpdate1() {
        $menuItemRow = $this->dao->get($this->primaryKey);
        $menuItemRow['active'] = explode('/', $menuItemRow['prettyUrl'])[0].'/'.date('Y-m-d H:i:s');
        $this->dao->update($menuItemRow);
    }

    /**
     *
     */
    public function testUpdate1() {
        $menuItemRow = $this->dao->get($this->primaryKey);
        $menuItemRow['active'] = $menuItemRow['active'] ? 0 : 1;
        $this->dao->update($menuItemRow);
    }

    /**
     *
     */
    public function testUpdate2ReadUpdated() {
        $menuItemRowRereaded = $this->dao->get($this->primaryKey);
        $this->assertNotEquals($this->activeBeforeTest, $menuItemRowRereaded['active']);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testContext1deactivate() {
        $menuItemRow['active'] = 0;
        $this->dao->update($menuItemRow);
    }

    /**
     *
     */
    public function testContext2readInContext() {
        $menuItemRow = $this->dao->get($this->primaryKey, $this->uid);
        $this->assertNull($menuItemRow);
    }

    /**
     *
     */
    public function testContext2readInContext() {
        $menuItemRow = $this->dao->get($this->primaryKey, $this->uid);
        $this->assertNull($menuItemRow);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testContext1deactivate() {
        $menuItemRow['active'] = 0;
        $this->dao->update($menuItemRow);
    }


    /**
     * @doesNotPerformAssertions
     */
    public function testRestoreActive() {
        // vrácení původní hodnoty
                $menuItemRow = $this->dao->getOu($this->primaryKey, $this->uid);

        $menuItemRow['active'] = $this->;
        $this->dao->update($menuItemRow);
        $this->setUp();
        $this->dao = $this->container->get(MenuItemDao::class);
        $menuItemRowRereaded = $this->dao->get($this->primaryKey, $this->uid);
        $this->assertEquals($menuItemRow, $menuItemRowRereaded);
        $this->assertEquals($oldActive, $menuItemRowRereaded['active']);

    }

    /**
     *
     */
    public function testInsertException() {
        $menuItemRow = $this->dao->get($this->primaryKey);
        $this->expectException(DaoForbiddenOperationException::class);
        $this->dao->insert($menuItemRow);
    }

    /**
     *
     */
    public function testDeleteException() {
        $menuItemRow = $this->dao->get($this->primaryKey);
        $this->expectException(DaoForbiddenOperationException::class);
        $this->dao->delete($menuItemRow);
    }
}
