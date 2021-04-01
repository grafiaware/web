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

use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Dao\LoginDao;
use Events\Model\Dao\VisitorDao;
use Events\Model\Repository\VisitorRepo;

use Events\Model\Entity\Visitor;

use Model\Dao\Exception\DaoKeyVerificationFailedException;

/**
 *
 * @author pes2704
 */
class VisitorRepositoryTest extends TestCase {

    private $container;

    /**
     *
     * @var VisitorRepo
     */
    private $visitorRepo;

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
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(
                    (new Container(
                        )
                    )
                )
            );

        // mazání - zde jen pro případ, že minulý test nebyl dokončen
//        self::deleteRecords($container);

        // toto je příprava testu
        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        /** @var VisitorDao $visitorDao */
        $visitorDao = $container->get(VisitorDao::class);

        try {
            $loginDao->insertWithKeyVerification(['login_name'=>'testVisitor']);
        } catch(DaoKeyVerificationFailedException $e) {

        }
        $visitorDao->insert([
            'login_login_name' => 'testVisitor',
        ]);
        self::$id = $visitorDao->getLastInsertedId();
    }

    private static function deleteRecords(Container $container) {
        /** @var VisitorDao $visitorDao */
//        $visitorDao = $container->get(VisitorDao::class);
//        $visitorDao->delete(['id'=>0]);
    }

    protected function setUp(): void {
        $this->container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        $this->visitorRepo = $this->container->get(VisitorRepo::class);
    }

    protected function tearDown(): void {
        $this->visitorRepo->flush();
    }

    public static function tearDownAfterClass(): void {
        $container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(
                    (new Container(
                        )
                    )
                )
            );

        self::deleteRecords($container);
    }

    public function testSetUp() {
        $this->assertInstanceOf(VisitorRepo::class, $this->visitorRepo);
    }

    public function testGetNonExisted() {
        $visitor = $this->visitorRepo->get(0);
        $this->assertNull($visitor);
    }

    public function testGetAndRemoveAfterSetup() {
        $visitor = $this->visitorRepo->get(self::$id);    // !!!! jenom po insertu v setUp - hodnotu vrací dao
        $this->assertInstanceOf(Visitor::class, $visitor);

        $visitor = $this->visitorRepo->remove($visitor);
        $this->assertNull($visitor);
    }

    public function testGetAfterRemove() {
        $visitor = $this->visitorRepo->get(self::$id);
        $this->assertNull($visitor);
    }

    public function testAdd() {
        $visitor = new Visitor();
        $visitor->setLoginName("testVisitorX");
        $this->visitorRepo->add($visitor);
        $this->assertTrue($visitor->isLocked());
    }

    public function testFindAll() {
        $visitors = $this->visitorRepo->findAll();
        $this->assertTrue(is_array($visitors));
    }

//    public function testGetAfterAdd() {
//        $event = $this->eventTypeRepo->get("XXXXXX");
//        $this->assertInstanceOf(Visitor::class, $event);
//        $this->assertTrue($event->getPublished());
//    }
//
//    public function testGetAndRemoveAfterAdd() {
//        $event = $this->eventTypeRepo->get("XXXXXX");
//        $this->eventTypeRepo->remove($event);
//        $this->assertTrue($event->isLocked(), 'Visitor není zamčena po remove.');
//    }
//
//    public function testAddAndReread() {
//        $event = new Visitor();
//        $event->setLoginName("XXXXXX");
//        $this->eventTypeRepo->add($event);
//        $this->eventTypeRepo->flush();
//        $event = $this->eventTypeRepo->get($event->getLoginName());
//        $this->assertTrue($event->isPersisted(), 'Visitor není persistován.');
//        $this->assertTrue(is_string($event->getLoginName()));
//    }

}
