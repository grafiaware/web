<?php
declare(strict_types=1);

use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Test\Integration\Red\Container\TestDbUpgradeContainerConfigurator;
use Test\Integration\Red\Container\TestHierarchyContainerConfigurator;

use Pes\Database\Manipulator\Manipulator;

use Red\Model\Dao\Hierarchy\HierarchyAggregateReadonlyDao;

use Red\Model\Dao\MenuItemDao;
use Model\Context\ContextProviderInterface;
use Test\Integration\Red\Model\Context\ContextProviderMock;

use Model\RowData\RowDataInterface;

use Model\Dao\Exception\DaoForbiddenOperationException;

/**
 * Description of MenuItemDaoTest
 *
 * @author pes2704
 */
class MenuItemDaoTest extends AppRunner {

    private static $container;

    private static $initialState = [];

    /**
     *
     * @var MenuItemDao
     */
    private static $dao;

    private static $primaryKey;

    private static $contextProvider;

    private static $upd;

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        $container =
                (new TestHierarchyContainerConfigurator())->configure(
                           (new TestDbUpgradeContainerConfigurator())->configure(new Container())
                        );
        self::$dao = $container->get(MenuItemDao::class);  // vždy nový objekt

        /** @var HierarchyAggregateReadonlyDao $hierarchy */
        $hierarchy = $container->get(HierarchyAggregateReadonlyDao::class);
        /** @var ContextProviderMock $contextProvider */
        self::$contextProvider = $container->get(ContextProviderInterface::class);  // získá ContextProviderMock - viz TestHierarchyContainerConfigurator aliasy

        // kontext pro čtení pomocí hierarchy - čte v editovatelném modu - načte i neaktivní node
        self::$contextProvider->changeContext(true);
        $title='Tests Integration';
        $node = $hierarchy->getByTitleHelper(['lang_code_fk'=>'cs', 'title'=>$title]);
        if (!isset($node)) {
            throw new \LogicException("Nelze spouštět integrační testy - v databázi projektu není položka menu v jazyce cs' s názvem 'Tests Integration'");
        }
        // vrací kontext na default hodnotu - false
        self::$contextProvider->changeContext(false);

        //  node.uid, (COUNT(parent.uid) - 1) AS depth, node.left_node, node.right_node, node.parent_uid
        self::$primaryKey = ['lang_code_fk'=>'cs', 'uid_fk'=>$node['uid']];

        /** @var Manipulator $manipulator */
        $manipulator = $container->get(Manipulator::class);
        $sql = "
            SELECT
            lang_code_fk, uid_fk, type_fk, id, list, title, prettyuri, active
            FROM menu_item
            WHERE title='$title'";

        $statement = $manipulator->query($sql);
        $menuItemRow = $statement->fetch();
        self::$initialState['active ']= $menuItemRow['active'];
        // nastav neaktivní položku menu na aktivní
        if (!self::$initialState['active ']) {
            $lk = self::$primaryKey['lang_code_fk'];
            $ui = self::$primaryKey['uid_fk'];
            $sql = "
                UPDATE `menu_item`
                SET
                `active` = 1
                WHERE `lang_code_fk` = '$lk' AND `uid_fk` = '$ui'";

            $manipulator->exec($sql);
        }
    }
    public static function tearDownAfterClass(): void {
        // pokud položka před testem byla neaktivní, vrať položku do stavu neaktivní
        if (!self::$initialState['active ']) {        
            $container =
                (new TestHierarchyContainerConfigurator())->configure(
                           (new TestDbUpgradeContainerConfigurator())->configure(new Container())
                        );        
            /** @var Manipulator $manipulator */
            $manipulator = $container->get(Manipulator::class);        
            $lk = self::$primaryKey['lang_code_fk'];
            $ui = self::$primaryKey['uid_fk'];
            $sql = "
                UPDATE `menu_item`
                SET
                `active` = 0
                WHERE `lang_code_fk` = '$lk' AND `uid_fk` = '$ui'";

            $manipulator->exec($sql);
        }
    }
    protected function setUp(): void {
        self::$container =
                (new TestHierarchyContainerConfigurator())->configure(
                           (new TestDbUpgradeContainerConfigurator())->configure(new Container())
                        );

    }

    /**
     * Čte s kontextem (jen aktivní). Podmínkou celého testu je existence menu item s příslušných názvem (title) . viz setUp()
     * a to, že je tento item aktivní.
     */
    public function testGetExistingRow() {
        $menuItemRow = self::$dao->get(self::$primaryKey);
        $this->assertInstanceOf(RowDataInterface::class, $menuItemRow, "Selhává dao->get() nebo položka pro test není aktivní.");
    }

    /**
     *
     */
    public function test8Columns() {
        $menuItemRow = self::$dao->get(self::$primaryKey);
        $this->assertCount(8, $menuItemRow);
    }

    /**
     *
     */
    public function testUpdatePrettyuri() {
        $menuItemRow = self::$dao->get(self::$primaryKey);
        self::$upd = explode('/', $menuItemRow['prettyuri'])[0].'/'.date('Y-m-d H:i:s');
        $menuItemRow['prettyuri'] = self::$upd;
        $succ = self::$dao->update($menuItemRow);
        $this->assertTrue($succ);
    }

    /**
     *
     */
    public function testRereadPrettyUrl() {
        $menuItemRow = self::$dao->get(self::$primaryKey);
        $menuItemRow['prettyuri'] = self::$upd;
        self::$dao->update($menuItemRow);
    }
    /**
     *
     */
    public function testUpdate1() {
        $menuItemRow = self::$dao->get(self::$primaryKey);
        $menuItemRow['active'] = $menuItemRow['active'] ? 0 : 1;
        self::$dao->update($menuItemRow);
    }

    /**
     *
     */
    public function testUpdate2ReadUpdated() {
        $menuItemRowRereaded = self::$dao->get(self::$primaryKey);
        $this->assertNotEquals($this->activeBeforeTest, $menuItemRowRereaded['active']);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testContext1deactivate() {
        $menuItemRow['active'] = 0;
        self::$dao->update($menuItemRow);
    }

    /**
     *
     */
    public function testContext2readInContext() {
        $menuItemRow = self::$dao->get(self::$primaryKey, $this->uid);
        $this->assertNull($menuItemRow);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testRestoreActive() {
        // vrácení původní hodnoty
                $menuItemRow = self::$dao->getOu(self::$primaryKey, $this->uid);

        $menuItemRow['active'] = 12345;
        self::$dao->update($menuItemRow);
        $this->setUp();
        self::$dao = self::$container->get(MenuItemDao::class);
        $menuItemRowRereaded = self::$dao->get(self::$primaryKey, $this->uid);
        $this->assertEquals($menuItemRow, $menuItemRowRereaded);
        $this->assertEquals($oldActive, $menuItemRowRereaded['active']);

    }

    /**
     *
     */
    public function testInsertException() {
        $menuItemRow = self::$dao->get(self::$primaryKey);
        $this->expectException(DaoForbiddenOperationException::class);
        self::$dao->insert($menuItemRow);
    }

    /**
     *
     */
    public function testDeleteException() {
        $menuItemRow = self::$dao->get(self::$primaryKey);
        $this->expectException(DaoForbiddenOperationException::class);
        self::$dao->delete($menuItemRow);
    }
}
