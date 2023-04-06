<?php
declare(strict_types=1);
namespace Test\Integration\Repository;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Test\AppRunner\AppRunner;

use Pes\Http\Factory\EnvironmentFactory;

use Application\WebAppFactory;

use Pes\Container\Container;

use Container\DbUpgradeContainerConfigurator;
use Container\RedModelContainerConfigurator;
use Test\Integration\Red\Container\TestDbUpgradeContainerConfigurator;
use Test\Integration\Red\Container\TestHierarchyContainerConfigurator;

use Red\Model\Dao\Hierarchy\HierarchyAggregateReadonlyDao;
use Red\Model\Repository\ItemActionRepoInterface;
use Red\Model\Repository\ItemActionRepo;

use Red\Model\Entity\ItemAction;

/**
 * Description of MenuItemPaperRepositoryTest
 *
 * @author pes2704
 */
class ItemActionRepositoryTest extends AppRunner {

    /**
     *
     * @var ItemActionRepoInterface
     */
    private $itemActionRepo;

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
    }

    protected function setUp(): void {

        $container =
                (new TestHierarchyContainerConfigurator())->configure(
                           (new TestDbUpgradeContainerConfigurator())->configure(new Container())
                        );


        $this->itemActionRepo = $container->get(ItemActionRepo::class);
    }

    public function testSetUp() {
        $this->assertInstanceOf(ItemActionRepoInterface::class, $this->itemActionRepo);
    }

    public function testGetNonExisted() {
        $entity = $this->itemActionRepo->get('ghgug', 111);
        $this->assertNull($entity);

    }

    public function testFindAll() {
        $allEntities = $this->itemActionRepo->findAll();
        if (!$allEntities) {
        throw new \LogicException("Nelte spouštět integrační test ItemActionRepo - v databázi není žádná položka item action.");
        }
        $this->assertIsArray($allEntities);
        $this->assertTrue(count($allEntities) > 0);
    }

    public function testDeleteAll() {
        $allEntities = $this->itemActionRepo->findAll();
        foreach ($allEntities as $entity) {
            $this->itemActionRepo->remove($entity);
        }
        $this->itemActionRepo->flush();
        $allEntities = $this->itemActionRepo->findAll();
        $this->assertIsArray($allEntities);
        $this->assertTrue(count($allEntities) == 0);
    }

    public function testSaveNew() {
        $itemAction = new ItemAction();
        $itemAction->setTypeFk('paper');
        $itemAction->setItemId('111');
        $itemAction->setEditorLoginName('editoří jméno');
        $this->itemActionRepo->add($itemAction);
        $itemAction = new ItemAction();
        $itemAction->setTypeFk('article');
        $itemAction->setItemId('222');
        $itemAction->setEditorLoginName('pan Chytrý');
        $this->itemActionRepo->add($itemAction);

        $this->itemActionRepo->flush();
        $allEntities = $this->itemActionRepo->findAll();
        $this->assertTrue(count($allEntities) == 2);

    }

    public function testGet() {
        $entity = $this->itemActionRepo->get('paper', 111);
        $this->assertInstanceOf(ItemAction::class, $entity);
    }

}
