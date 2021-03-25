<?php
declare(strict_types=1);
namespace Test\Integration\Repository;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use PHPUnit\Framework\TestCase;

use Pes\Http\Factory\EnvironmentFactory;

use Application\WebAppFactory;

use Pes\Container\Container;

use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Dao\LoginDao;
use Events\Model\Repository\LoginRepo;

use Events\Model\Entity\Login;


/**
 *
 * @author pes2704
 */
class LoginRepositoryTest extends TestCase {

    private $container;

    /**
     *
     * @var LoginRepo
     */
    private $loginRepo;

    static $uidForEmail;

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
        self::deleteRecords($container);

        // toto je příprava testu
        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);

        $loginDao->insertWithKeyVerification(['login_name'=>"testLogin"]);
    }

    private static function deleteRecords(Container $container) {
        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        $loginDao->delete(['login_name'=>"testLogin"]);
    }

    protected function setUp(): void {
//        $environment = $this->mock(
//                ['HTTP_USER_AGENT'=>'AppRunner']
//
//                );
//        $this->app = (new WebAppFactory())->createFromEnvironment($environment);

        $this->container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(
                    (new Container(
                        )
                    )
                )
            );

        $this->loginRepo = $this->container->get(LoginRepo::class);
    }

    protected function tearDown(): void {
        $this->loginRepo->flush();
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
        $this->assertInstanceOf(LoginRepo::class, $this->loginRepo);
    }

    public function testGetNonExisted() {
        $login = $this->loginRepo->get("QWER45T6U7I89OPOLKJHGFD");
        $this->assertNull($login);
    }

    public function testGetAndRemoveAfterSetup() {
        $login = $this->loginRepo->get("testLogin");
        $this->assertInstanceOf(Login::class, $login);

        $login = $this->loginRepo->remove($login);
        $this->assertNull($login);
    }

    public function testGetAfterRemove() {
        $login = $this->loginRepo->get("testLogin");
        $this->assertNull($login);
    }

    public function testAdd() {
        $login = new Login();
        $login->setLoginName("testLogin");
        $this->loginRepo->add($login);
        $this->assertTrue($login->isPersisted());
        // Login není zamčena po add. protože LoginDao je typu Model\Dao\DaoKeyDbVerifiedInterface
        // naopak je isPersisted hned po ->add() protože je typu Model\Dao\DaoKeyDbVerifiedInterface
    }

    public function testGetAfterAdd() {
        $login = $this->loginRepo->get("testLogin");
        $this->assertInstanceOf(Login::class, $login);
        $this->assertTrue(is_string($login->getLoginName()));
    }

    public function testGetAndRemoveAfterAdd() {
        $login = $this->loginRepo->get("testLogin");
        $this->loginRepo->remove($login);
        $this->assertTrue($login->isLocked(), 'Login není zamčena po remove.');
    }

    public function testAddAndReread() {
        $login = new Login();
        $login->setLoginName("testLogin");
        $this->loginRepo->add($login);
        $this->loginRepo->flush();
        $login = $this->loginRepo->get($login->getLoginName());
        $this->assertTrue($login->isPersisted(), 'Login není persistován.');
        $this->assertTrue(is_string($login->getLoginName()));
    }

}
