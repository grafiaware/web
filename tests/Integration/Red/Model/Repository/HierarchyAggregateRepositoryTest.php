<?php
declare(strict_types=1);
namespace Test\Integration\Red\ModelRepository;

use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Container\DbUpgradeContainerConfigurator;
use Container\HierarchyContainerConfigurator;
use Test\Integration\Red\Container\TestHierarchyContainerConfigurator;

use Red\Model\Dao\Hierarchy\HierarchyAggregateReadonlyDao;
use Red\Model\Repository\HierarchyJoinMenuItemRepo;

use Red\Model\Entity\HierarchyAggregateInterface;

/**
 * Description of MenuItemPaperRepositoryTest
 *
 * @author pes2704
 */
class HierarchyAggregateRepositoryTest extends AppRunner {

    private $container;

    /**
     *
     * @var HierarchyJoinMenuItemRepo
     */
    private $hirerchyAggRepo;

    private $title;
    private $langCode;
    private $uid;

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
   }

    protected function setUp(): void {
        $this->container =
                (new TestHierarchyContainerConfigurator())->configure(
                    new Container(
                        (new HierarchyContainerConfigurator())->configure(
                           (new DbUpgradeContainerConfigurator())->configure(
                                new Container())
                        )
                    )// přepisuje ContextFactory a parametry
                );


        $this->hirerchyAggRepo = $this->container->get(HierarchyJoinMenuItemRepo::class);

        /** @var HierarchyAggregateReadonlyDao $hierarchyDao */
        $hierarchyDao = $this->container->get(HierarchyAggregateReadonlyDao::class);
        $node = $hierarchyDao->getByTitleHelper(['lang_code_fk'=>'cs', 'title'=>'Tests Integration']);
        if (!isset($node)) {
            throw new \LogicException("Nelte spoušrět integrační testy - v databázi projektu není položka menu v jazyce 'cs' s názvem 'Tests Integration'");
        }
        $this->langCode = 'cs';
        $this->uid = $node['uid'];
##
    }

    public function testSetUp() {
        $this->assertIsArray($this->langCode, $this->uid);
        $this->assertInstanceOf(HierarchyJoinMenuItemRepo::class, $this->hirerchyAggRepo);
    }

    public function testGet() {
        $entity = $this->hirerchyAggRepo->get($this->langCode, $this->uid);
        $this->assertInstanceOf(HierarchyAggregateInterface::class, $entity);
        $this->assertEquals($this->title, $entity->getMenuItem()->getTitle());
    }

    public function testUpdateChildMenuItem() {
        $entity = $this->hirerchyAggRepo->get($this->langCode, $this->uid);
        $this->assertInstanceOf(HierarchyAggregateInterface::class, $entity);
        $entity->getMenuItem()->setTitle('Tests Integration HierarchyAggregateRepositoryTest');
    }

    public function testAfterUpdateHierarchy() {
        $entity = $this->hirerchyAggRepo->get($this->langCode, $this->uid);
        $this->assertInstanceOf(HierarchyAggregateInterface::class, $entity);
        $this->assertStringStartsWith($this->title, $entity->getMenuItem()->getTitle());
    }
}
