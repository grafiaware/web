<?php
declare(strict_types=1);
namespace Test\Integration\Event\Model\Repository;

use Test\AppRunner\AppRunner;
use Pes\Container\Container;


use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Dao\LoginDao;
use Events\Model\Repository\LoginRepo;

use Events\Model\Entity\Login;

use Model\RowData\RowData;

/**
 *
 * @author pes2704
 */
class LoginRepositoryTest extends AppRunner {

    private $container;

    /**
     *
     * @var LoginRepo
     */
    private $loginRepo;

    private static $loginKlic = "testLogin";
    

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        $container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(
                    (new Container( ) ) )
            );

        // mazání - zde jen pro případ, že minulý test nebyl dokončen
        self::deleteRecords($container);
        
        self::insertRecords($container);


//        // toto je příprava testu
//        /** @var LoginDao $loginDao */
//        $loginDao = $container->get(LoginDao::class);
//        $rowData = new RowData();
//        $rowData->offsetSet('login_name', self::$loginKlic);
//        $loginDao->insert($rowData);
    }
    
    
    private static function insertRecords(Container $container) {
        // toto je příprava testu, vlozi 1 login
        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        $rowData = new RowData();        
        $rowData->offsetSet('login_name', self::$loginKlic);
        $loginDao->insert($rowData);
    }

    private static function deleteRecords(Container $container) {        
        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        
        $rows = $loginDao->find( "login_name LIKE '". self::$loginKlic . "%'", []);                
        foreach($rows as $row) {
            $ok =  $loginDao->delete($row);
        }

    }

    protected function setUp(): void {
        $this->container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(
                    (new Container(  ) )  )
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
                    (new Container( ) ) )
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
        $login = $this->loginRepo->get(self::$loginKlic);
        $this->assertInstanceOf(Login::class, $login);

        $login = $this->loginRepo->remove($login);
        $this->assertNull($login);
    }

    public function testGetAfterRemove() {
        $login = $this->loginRepo->get(self::$loginKlic);
        $this->assertNull($login);
    }

    public function testAdd() {
        $login = new Login();
        $login->setLoginName(self::$loginKlic);
        $this->loginRepo->add($login);
        
        // pro automaticky|generovany klic a pro  overovany klic (tento pripad zde ) - !!! zapise se hned !!!   DaoEditKeyDbVerifiedInterface
        $this->assertTrue($login->isPersisted());
        $this->assertFalse($login->isLocked());
        // Login není zamčena po add. protože LoginDao je typu Model\Dao\DaoKeyDbVerifiedInterface
        // je isPersisted hned po ->add() protože je typu Model\Dao\DaoKeyDbVerifiedInterface
    }

    
    public function testGetAfterAdd() {
        $login = $this->loginRepo->get(self::$loginKlic);
        $this->assertInstanceOf(Login::class, $login);
        $this->assertTrue(is_string($login->getLoginName()));
    }
    


    
    public function testAddAndReread() {
        $login = new Login();
        $login->setLoginName(self::$loginKlic . "1");
        $this->loginRepo->add($login);
        $this->loginRepo->flush();
        
        $login = $this->loginRepo->get($login->getLoginName());      
        $this->assertTrue($login->isPersisted() );
        $this->assertFalse($login->isLocked());
        $this->assertTrue(is_string($login->getLoginName()));
        $this->assertEquals(self::$loginKlic . "1", $login->getLoginName());
    }

    
    //remove
    
//    public function testGetAndRemoveAfterAdd() {
//        $login = $this->loginRepo->get("testLogin");
//        $this->loginRepo->remove($login);
//        $this->assertTrue($login->isLocked(), 'Login není zamčena po remove.');
//    }
    
   
    
    
    
}
