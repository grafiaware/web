<?php
declare(strict_types=1);
namespace Test\Integration\Repository;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use PHPUnit\Framework\TestCase;

use Pes\Container\Container;

use Container\DbUpgradeContainerConfigurator;
use Container\HierarchyContainerConfigurator;
use Test\Integration\Model\Container\TestModelContainerConfigurator;

use Events\Model\Dao\EnrollDao;
use Events\Model\Repository\EnrollRepo;

use Events\Model\Entity\Enroll;


/**
 *
 * @author pes2704
 */
class EnrollRepositoryTest extends TestCase {


    private $container;

    /**
     *
     * @var EnrollRepo
     */
    private $enrollRepo;

    private static $id;

    public static function setUpBeforeClass(): void {
        if ( !defined('PES_DEVELOPMENT') AND !defined('PES_PRODUCTION') ) {
            define('PES_FORCE_DEVELOPMENT', 'force_development');
            //// nebo
            //define('PES_FORCE_PRODUCTION', 'force_production');

            define('PROJECT_PATH', 'c:/ApacheRoot/web/');

            include '../vendor/pes/pes/src/Bootstrap/Bootstrap.php';
        }

        $container =
                (new TestModelContainerConfigurator())->configure(  // přepisuje ContextFactory
                    (new HierarchyContainerConfigurator())->configure(
                       (new DbUpgradeContainerConfigurator())->configure(new Container())
                    )
                );

        // mazání - zde jen pro případ, že minulý test nebyl dokončen
        self::deleteRecords($container);

        // toto je příprava testu
        /** @var EventContentDao $enrollDao */
        $enrollDao = $container->get(EnrollDao::class);

        $enrollDao->insert([
            'login_name' => "testEnroll login name",
            'eventid' => "test_eventid_" . (string) (1000+random_int(0, 999)),
        ]);
        self::$id = $enrollDao->getLastInsertedId();
    }

    private static function deleteRecords(Container $container) {
        /** @var EventContentDao $eventContentDao */
        $eventContentDao = $container->get(EnrollDao::class);
        $eventContentDao->delete(['id'=>0]);
    }

    protected function setUp(): void {
        $this->container =
                (new TestModelContainerConfigurator())->configure(  // přepisuje ContextFactory
                    (new HierarchyContainerConfigurator())->configure(
                       (new DbUpgradeContainerConfigurator())->configure(new Container())
                    )
                );
        $this->enrollRepo = $this->container->get(EnrollRepo::class);
    }

    protected function tearDown(): void {
        $this->enrollRepo->flush();
    }

    public static function tearDownAfterClass(): void {
        $container =
                (new TestModelContainerConfigurator())->configure(  // přepisuje ContextFactory
                    (new HierarchyContainerConfigurator())->configure(
                       (new DbUpgradeContainerConfigurator())->configure(new Container())
                    )
                );

        self::deleteRecords($container);
    }

    public function testSetUp() {
        $this->assertInstanceOf(EnrollRepo::class, $this->enrollRepo);
    }

    public function testGetNonExisted() {
        $enroll = $this->enrollRepo->get(0);
        $this->assertNull($enroll);
    }

    public function testGetAndRemoveAfterSetup() {
        $enroll = $this->enrollRepo->get(self::$id);    // !!!! jenom po insertu v setUp - hodnotu vrací dao
        $this->assertInstanceOf(Enroll::class, $enroll);

        $enroll = $this->enrollRepo->remove($enroll);
        $this->assertNull($enroll);
    }

    public function testGetAfterRemove() {
        $enroll = $this->enrollRepo->get(self::$id);
        $this->assertNull($enroll);
    }

    public function testAdd() {

        $enroll = new Enroll();
        $enroll->setLoginName("testEnroll login name");
        $enroll->setEventid("test_eventid_999");
        $this->enrollRepo->add($enroll);
        $this->assertTrue($enroll->isLocked());
    }

    public function testFindAll() {
        $enroll = $this->enrollRepo->findAll();
        $this->assertTrue(is_array($enroll));
    }

    public function testFindByLoginName() {
        $enrolls = $this->enrollRepo->findByLoginName("testEnroll login name");
        $this->assertTrue(is_array($enrolls));
    }
}
