<?php
declare(strict_types=1);
namespace Test\Integration\Red\ModelRepository;

use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Container\DbUpgradeContainerConfigurator;
use Container\HierarchyContainerConfigurator;

use Test\Integration\Red\Container\TestDbUpgradeContainerConfigurator;
use Test\Integration\Red\Container\TestHierarchyContainerConfigurator;

use Red\Model\Dao\Hierarchy\HierarchyAggregateReadonlyDao;
use Red\Model\Repository\HierarchyJoinMenuItemRepo;
use Red\Model\Entity\HierarchyAggregateInterface;

use Pes\Database\Handler\ConnectionInfo;
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

    private static $title;
    private static $langCode;
    private static $uid;

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        $container =
                (new TestHierarchyContainerConfigurator())->configure(
                           (new TestDbUpgradeContainerConfigurator())->configure(new Container())
                        );
        self::$title = 'Tests Integration';
        self::$langCode = 'cs';
        /** @var HierarchyAggregateReadonlyDao $hierarchyDao */
        $hierarchyDao = $container->get(HierarchyAggregateReadonlyDao::class);
        $node = $hierarchyDao->getByTitleHelper(['lang_code_fk'=>'cs', 'title'=> self::$title]);
        if (!isset($node)) {
            /** @var ConnectionInfo $connection */
            $connection = $container->get(ConnectionInfo::class);
            $langCode = self::$langCode;
            $title = self::$title;
            throw new \LogicException("Nelte spouštět integrační testy - v databázi '{$connection->getDbName()}' není položka menu v jazyce '$langCode' s názvem položky menu '$title'");
        }
        self::$uid = $node['uid'];
   }

    protected function setUp(): void {
        $this->container =
                (new TestHierarchyContainerConfigurator())->configure(
                           (new TestDbUpgradeContainerConfigurator())->configure(new Container())
                        );
        $this->hirerchyAggRepo = $this->container->get(HierarchyJoinMenuItemRepo::class);

    }

    public function tearDown(): void {
        $this->hirerchyAggRepo->flush();
    }

    public function testSetUp() {
        $this->assertInstanceOf(HierarchyJoinMenuItemRepo::class, $this->hirerchyAggRepo);
    }

    public function testGet() {
        $entity = $this->hirerchyAggRepo->get(self::$langCode, self::$uid);
        $this->assertInstanceOf(HierarchyAggregateInterface::class, $entity);
        $this->assertEquals(self::$title, $entity->getMenuItem()->getTitle());
    }

    public function testUpdateChildMenuItem() {
        $entity = $this->hirerchyAggRepo->get(self::$langCode, self::$uid);
        $this->assertInstanceOf(HierarchyAggregateInterface::class, $entity);
        $entity->getMenuItem()->setTitle('Tests Integration HierarchyAggregateRepositoryTest');
    }

    public function testAfterUpdateHierarchy() {
        $entity = $this->hirerchyAggRepo->get(self::$langCode, self::$uid);
        $this->assertInstanceOf(HierarchyAggregateInterface::class, $entity);
        $this->assertStringStartsWith(self::$title, $entity->getMenuItem()->getTitle());
        $entity->getMenuItem()->setTitle(self::$title);
    }

    /**
     * Tento test je tady proto, aby došlo k zápisu
     */
    public function testAfterUpdate2Hierarchy() {
        $entity = $this->hirerchyAggRepo->get(self::$langCode, self::$uid);
        $this->assertInstanceOf(HierarchyAggregateInterface::class, $entity);
        $this->assertEquals(self::$title, $entity->getMenuItem()->getTitle());
    }
}
