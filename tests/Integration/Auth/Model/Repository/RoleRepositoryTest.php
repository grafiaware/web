<?php
declare(strict_types=1);

namespace Test\Integration\Auth\Model\Repository;

use Test\AppRunner\AppRunner;

use Pes\Container\Container;
use Container\AuthContainerConfigurator;
use Container\DbOldContainerConfigurator;

use Model\Repository\Exception\OperationWithLockedEntityException;
use Model\RowData\RowData;

use Auth\Model\Dao\RoleDao;
use Auth\Model\Repository\RoleRepo;
use Auth\Model\Entity\Role;
use Auth\Model\Entity\RoleInterface;


/**
 * Description of RoleRepositoryTest
 *
 * @author vlse2610
 */
class RoleRepositoryTest  extends AppRunner {

    private $container;

    /**
     *
     * @var RoleRepo
     */
    private $roleRepo;

    private static $roleKlic = "testRole";


    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        
        $container =
            (new AuthContainerConfigurator())->configure(
                (new DbOldContainerConfigurator())->configure(
                    (new Container(
//                            $this->getApp()->getAppContainer()       bez app kontejneru
                        )
                    )
                )
            );
        // mazání - zde jen pro případ, že minulý test nebyl dokončen
        self::deleteRecords($container);

        self::insertRecords($container);

    }


    private static function insertRecords(Container $container) {
        // toto je příprava testu, vlozi 1 login
        /** @var LoginDao $loginDao */
//        $loginDao = $container->get(LoginDao::class);
//        $rowData = new RowData();
//        $rowData->offsetSet('login_name', self::$loginKlic);
//        $loginDao->insert($rowData);
    }

    private static function deleteRecords(Container $container) {
        /** @var LoginDao $loginDao */
//        $loginDao = $container->get(LoginDao::class);
//
//        $rows = $loginDao->find( "login_name LIKE '". self::$loginKlic . "%'", []);
//        foreach($rows as $row) {
//            $ok =  $loginDao->delete($row);
//        }

    }

    protected function setUp(): void {
//        $this->container =
//            (new EventsModelContainerConfigurator())->configure(
//                (new TestDbEventsContainerConfigurator())->configure(
//                    (new Container(  ) )  )
//            );

        $this->container =
            (new AuthContainerConfigurator())->configure(
                (new DbOldContainerConfigurator())->configure(
                    (new Container(
//                            $this->getApp()->getAppContainer()       bez app kontejneru
                        )
                    )
                )
            );
        $this->roleRepo = $this->container->get(RoleRepo::class);
    }
    

    protected function tearDown(): void {
        $this->roleRepo->__destruct();
    }

    public static function tearDownAfterClass(): void {
//        $container =
//            (new EventsModelContainerConfigurator())->configure(
//                (new TestDbEventsContainerConfigurator())->configure(
//                    (new Container( ) ) )
//            );
        
        $container =
            (new AuthContainerConfigurator())->configure(
                (new DbOldContainerConfigurator())->configure(
                    (new Container(
//                            $this->getApp()->getAppContainer()       bez app kontejneru
                        )
                    )
                )
            );

        self::deleteRecords($container);
    }

    public function testSetUp() {
        $this->assertInstanceOf(RoleRepo::class, $this->roleRepo);
    }

    public function testGetNonExisted() {
        $role = $this->roleRepo->get("QWER45T6U7I89OPOLKJHGFD");
        $this->assertNull($role);
    }

    public function testGetAndRemoveAfterSetup() {
        $role = $this->roleRepo->get(self::$roleKlic);
        $this->assertInstanceOf(RoleInterface::class, $role);

        $ok= $this->roleRepo->remove($role);
        $this->assertNull($ok);
    }

    public function testGetAfterRemove() {
        $role = $this->roleRepo->get(self::$roleKlic);
        $this->assertNull($role);
    }

    public function testAdd() {
        $role = new Role();
        $role->setLoginName(self::$roleKlic);
        $this->roleRepo->add($role);

        // pro automaticky|generovany klic a pro  overovany klic (tento pripad zde ) - !!! zapise se hned !!!   DaoEditKeyDbVerifiedInterface
        $this->assertTrue($role->isPersisted());
        $this->assertFalse($role->isLocked());
        // Role není zamčena po add. protože RoleDao je typu Model\Dao\DaoKeyDbVerifiedInterface
        // je isPersisted hned po ->add() protože je typu Model\Dao\DaoKeyDbVerifiedInterface
    }

//
//    public function testGetAfterAdd() {
//        $login = $this->roleRepo->get(self::$loginKlic);
//        $this->assertInstanceOf(LoginInterface::class, $login);
//        $this->assertTrue(is_string($login->getLoginName()));
//    }
//
//
//
//
//    public function testAddAndReread() {
//        $login = new Login();
//        $login->setLoginName(self::$loginKlic . "1");
//        $this->roleRepo->add($login);
//        $this->roleRepo->flush();
//
//        $login = $this->roleRepo->get($login->getLoginName());
//        $this->assertTrue($login->isPersisted() );
//        $this->assertFalse($login->isLocked());
//        $this->assertTrue(is_string($login->getLoginName()));
//        $this->assertEquals(self::$loginKlic . "1", $login->getLoginName());
//    }
//
//
//
//    public function testRemove_OperationWithLockedEntity() {
//        /** @var Login $login */
//        $login = $this->roleRepo->get(self::$loginKlic . "1");
//        $this->assertInstanceOf(LoginInterface::class, $login);
//        $this->assertTrue($login->isPersisted());
//        $this->assertFalse($login->isLocked());
//
//        $login->lock();
//        $this->expectException( OperationWithLockedEntityException::class);
//        $this->roleRepo->remove($login);
//    }
//
//
//
//
//    public function testRemove() {
//        /** @var Login $login */
//        $login = $this->roleRepo->get(self::$loginKlic . "1" );
//
//        $this->assertInstanceOf(LoginInterface::class, $login);
//        $this->assertTrue($login->isPersisted());
//        $this->assertFalse($login->isLocked());
//
//        $this->roleRepo->remove($login);
//
//        $this->assertTrue($login->isPersisted());
//        $this->assertTrue($login->isLocked());   // zatim zamcena entita, maže až při flush
//        $this->roleRepo->flush();
//        //  uz neni locked
//        $this->assertFalse($login->isLocked());
//
//        // pokus o čtení, entita Login.self::$loginKlic  uz  neni
//        $login = $this->roleRepo->get(self::$loginKlic . "1" );
//        $this->assertNull($login);
//
//    }



}
