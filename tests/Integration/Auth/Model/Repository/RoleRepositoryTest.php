<?php
declare(strict_types=1);
namespace Test\Integration\Auth\Model\Repository;

use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Container\AuthContainerConfigurator;
use Container\AuthDbContainerConfigurator;

use Auth\Model\Entity\Role;
use Auth\Model\Entity\RoleInterface;
use Auth\Model\Dao\RoleDao;
use Auth\Model\Repository\RoleRepo;
use Model\RowData\RowData;

use Model\Repository\Exception\OperationWithLockedEntityException;



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
                (new AuthDbContainerConfigurator())->configure(
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
        // toto je příprava testu, vlozi 1 role
        /** @var RoleDao $roleDao */
        $roleDao = $container->get(RoleDao::class);
        $rowData = new RowData();
        $rowData->offsetSet('role', self::$roleKlic);
        $roleDao->insert($rowData);
    }

    private static function deleteRecords(Container $container) {
        /** @var RoleDao $roleDao */
        $roleDao = $container->get(RoleDao::class);

        $rows = $roleDao->find( "role LIKE '". self::$roleKlic . "%'", []); 
        foreach($rows as $row) {
            $ok =  $roleDao->delete($row);
        }
    }

    protected function setUp(): void {
//        $this->container =
//            (new EventsModelContainerConfigurator())->configure(
//                (new TestDbEventsContainerConfigurator())->configure(
//                    (new Container(  ) )  )
//            );

        $container =
            (new AuthContainerConfigurator())->configure(
                (new AuthDbContainerConfigurator())->configure(
                    (new Container(
//                            $this->getApp()->getAppContainer()       bez app kontejneru
                        )
                    )
                )
            );
        $this->roleRepo = $container->get(RoleRepo::class);
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
                (new AuthDbContainerConfigurator())->configure(
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
        $role->setRole(self::$roleKlic);
        $this->roleRepo->add($role);

        // pro automaticky|generovany klic a pro  overovany klic (tento pripad zde ) - !!! zapise se hned !!!   DaoEditKeyDbVerifiedInterface
        $this->assertTrue($role->isPersisted());
        $this->assertFalse($role->isLocked());
        // Role není zamčena po add. protože RoleDao je typu Model\Dao\DaoKeyDbVerifiedInterface
        // je isPersisted hned po ->add() protože je typu Model\Dao\DaoKeyDbVerifiedInterface
    }


    public function testGetAfterAdd() {
        $role = $this->roleRepo->get(self::$roleKlic);
        $this->assertInstanceOf(RoleInterface::class, $role);
        $this->assertTrue(is_string($role->getRole()));
    }




    public function testAddAndReread() {
        $role = new Role();
        $role->setRole(self::$roleKlic . "1");
        $this->roleRepo->add($role);
        $this->roleRepo->flush();

        $role = $this->roleRepo->get($role->getRole());
        $this->assertTrue($role->isPersisted() );
        $this->assertFalse($role->isLocked());
        $this->assertTrue(is_string($role->getRole()));
        $this->assertEquals(self::$roleKlic . "1", $role->getRole());
    }

    
    
    public function testRemove_OperationWithLockedEntity() {
        /** @var Role $role */
        $role = $this->roleRepo->get(self::$roleKlic . "1");
        $this->assertInstanceOf(RoleInterface::class, $role);
        $this->assertTrue($role->isPersisted());
        $this->assertFalse($role->isLocked());

        $role->lock();
        $this->expectException( OperationWithLockedEntityException::class);
        $this->roleRepo->remove($role);
    }




    public function testRemove() {
        /** @var Role $role */
        $role = $this->roleRepo->get(self::$roleKlic . "1" );

        $this->assertInstanceOf(RoleInterface::class, $role);
        $this->assertTrue($role->isPersisted());
        $this->assertFalse($role->isLocked());

        $this->roleRepo->remove($role);

        $this->assertTrue($role->isPersisted());
        $this->assertTrue($role->isLocked());   // zatim zamcena entita, maže až při flush
        $this->roleRepo->flush();
        //  uz neni locked
        $this->assertFalse($role->isLocked());

        // pokus o čtení, entita Role s self::$roleKlic . "1" uz  neni
        $role = $this->roleRepo->get(self::$roleKlic . "1" );
        $this->assertNull($role);

    }



}
