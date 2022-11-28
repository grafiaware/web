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

use Test\Integration\Red\Container\TestDbUpgradeContainerConfigurator;
use Test\Integration\Red\Container\TestHierarchyContainerConfigurator;

use Red\Model\Dao\MenuItemAssetDao;
use Red\Model\Repository\MenuItemAssetRepo;

use Red\Model\Entity\MenuItemAsset;
use Model\RowData\RowData;

/**
 *
 * @author pes2704
 */
class MenuItemAssetDaoRepositoryTest extends AppRunner {

    const TEST_USER = 'Test';

    private $container;

    /**
     *
     * @var MenuItemAssetRepo
     */
    private $menuItemAssetRepo;

    private static $id;

    public static function setUpBeforeClass(): void {
//        if ( !defined('PES_DEVELOPMENT') AND !defined('PES_PRODUCTION') ) {
//            define('PES_FORCE_DEVELOPMENT', 'force_development');
//            //// nebo
//            //define('PES_FORCE_PRODUCTION', 'force_production');
//
//            define('PROJECT_PATH', 'c:/ApacheRoot/web/');
//
//            include '../vendor/pes/pes/src/Bootstrap/Bootstrap.php';
//        }

        self::bootstrapBeforeClass();

        $container =
                (new TestHierarchyContainerConfigurator())->configure(
                           (new TestDbUpgradeContainerConfigurator())->configure(new Container())
                        );
        // mazání - zde jen pro případ, že minulý test nebyl dokončen
        self::deleteRecords($container);

        // toto je příprava testu
        /** @var MenuItemAssetDao $menuItemAssetsDao */
        $menuItemAssetsDao = $container->get(MenuItemAssetDao::class);
        $testRowData = new RowData();
        // POZOR - natvrdo 20
        $testRowData->import(
            [
                'menu_item_id_FK'=>20,
                'filepath'=>'test/cesta/k/souboru/obraz.jpg',
                'editor_login_name'=> self::TEST_USER
            ]
        );
        $menuItemAssetsDao->insert($testRowData);
        self::$id = $menuItemAssetsDao->getLastInsertIdTouple();
    }

    private static function deleteRecords(Container $container) {
        /** @var MenuItemAssetDao $menuItemAssetsDao */
        $menuItemAssetsDao = $container->get(MenuItemAssetDao::class);
        $rows = $menuItemAssetsDao->find('editor_login_name=:editor_login_name', [':editor_login_name'=>self::TEST_USER]);
        foreach ($rows as $rowData) {
            $menuItemAssetsDao->delete($rowData);
        }
    }

    protected function setUp(): void {
        $this->container =
            (new TestHierarchyContainerConfigurator())->configure(
                       (new TestDbUpgradeContainerConfigurator())->configure(new Container())
                    );
        $this->menuItemAssetRepo = $this->container->get(MenuItemAssetRepo::class);
    }

    protected function tearDown(): void {
        $this->menuItemAssetRepo->flush();
    }

    public static function tearDownAfterClass(): void {
        $container =
            (new TestHierarchyContainerConfigurator())->configure(
                       (new TestDbUpgradeContainerConfigurator())->configure(new Container())
                    );

        self::deleteRecords($container);
    }

    public function testSetUp() {
        $this->assertInstanceOf(MenuItemAssetRepo::class, $this->menuItemAssetRepo);
    }

    public function testGetNonExisted() {
        $enroll = $this->menuItemAssetRepo->get(['id'=>0]);
        $this->assertNull($enroll);
    }

    public function testGetAndRemoveAfterSetup() {
        $menuItemAsset = $this->menuItemAssetRepo->get(self::$id);    // !!!! jenom po insertu v setUp - hodnotu vrací dao
        $this->assertInstanceOf(MenuItemAsset::class, $menuItemAsset);

        $menuItemAsset = $this->menuItemAssetRepo->remove($menuItemAsset);
        $this->assertNull($menuItemAsset);
    }

    public function testGetAfterRemove() {
        $menuItemAsset = $this->menuItemAssetRepo->get(self::$id);
        $this->assertNull($menuItemAsset);
    }

    public function testAdd() {

        $menuItemAsset = new MenuItemAsset();
        $menuItemAsset->setMenuItemIdFk('20');
        $menuItemAsset->setFilepath('test/testAdd/filepath/ff.gif');
        $menuItemAsset->setEditorLoginName(self::TEST_USER);
        $this->menuItemAssetRepo->add($menuItemAsset);
        $this->assertTrue($menuItemAsset->isPersisted());  // autoincrement id
    }

    public function testFindAll() {
        $assets = $this->menuItemAssetRepo->findAll();
        $this->assertTrue(is_array($assets));
    }

    public function testGetByFilepath() {
        $asset = $this->menuItemAssetRepo->getByFilename('test/testAdd/filepath/ff.gif');
        $this->assertInstanceOf(MenuItemAsset::class, $asset);
    }
}
