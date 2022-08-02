<?php
declare(strict_types=1);
namespace Test\Integration\Event\Model\Repository;

use Test\AppRunner\AppRunner;
use Pes\Container\Container;
use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Model\Repository\Exception\OperationWithLockedEntityException;
use Model\RowData\RowData;

use Events\Model\Dao\LoginDao;
use Events\Model\Dao\EventDao;

use Events\Model\Dao\EnrollDao;
use Events\Model\Repository\EnrollRepo;
use Events\Model\Entity\Enroll;
use Events\Model\Entity\EnrollInterface;



/**
 * Description of EnrollRepositoryTest
 *
 * @author vlse2610
 */
class EnrollRepositoryTest extends AppRunner {
    private $container;

    /**
     *
     * @var EnrollRepo
     */
    private $enrollRepo;

    private static $enrollLoginString = "testEnroll";
    private static $pripravaLoginKlicTouples1;
    private static $pripravaLoginKlicTouples2;
    private static $pripravaEventId;
    

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

    }
    
    
    private static function insertRecords(Container $container) {
        /** @var LoginDao $loginDao */
        // toto je příprava testu, vlozi 1. login
        $loginDao = $container->get(LoginDao::class);
        $rowData = new RowData();        
        $loginNam = self::$enrollLoginString . (string) (random_int(0, 999));
        $rowData->offsetSet('login_name', $loginNam );
        $loginDao->insert($rowData);   
        self::$pripravaLoginKlicTouples1 = $loginDao->getPrimaryKeyTouples($rowData->getArrayCopy());
         // toto je příprava testu, vlozi 2. login
        $loginNam = self::$enrollLoginString . (string) (random_int(0, 999));
        $rowData->offsetSet('login_name', $loginNam );
        $loginDao->insert($rowData);   
        self::$pripravaLoginKlicTouples2 = $loginDao->getPrimaryKeyTouples($rowData->getArrayCopy());
        
        //vlozi event
        /** @var EventDao $eventDao */
        $eventDao = $container->get(EventDao::class);
        $rowData = new RowData();         
        $rowData->offsetSet('published',1 );
        $eventDao->insert($rowData);    
        self::$pripravaEventId = $eventDao->lastInsertIdValue();
        
        //-----------------
        // vlozi enroll
         /** @var EnrollDao $enrollDao */
        $enrollDao = $container->get(EnrollDao::class);
        $rowData = new RowData();        
        $rowData->offsetSet('login_login_name_fk', self::$pripravaLoginKlicTouples1['login_name'] );
        $rowData->offsetSet('event_id_fk', self::$pripravaEventId ) ;
        $enrollDao->insert($rowData);
        
    }

    private static function deleteRecords(Container $container) {        
        /** @var EnrollDao $enrollDao */
        $enrollDao = $container->get(EnrollDao::class);        
        $rows = $enrollDao->find( "login_login_name_fk LIKE '". self::$enrollLoginString . "%'", []);                
        foreach($rows as $row) {
            $ok =  $enrollDao->delete($row);
        }

    }

    protected function setUp(): void {
        $this->container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(
                    (new Container(  ) )  )
            );

        $this->enrollRepo = $this->container->get( EnrollRepo::class );
    }

    protected function tearDown(): void {
        $this->enrollRepo->flush();
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
        $this->assertInstanceOf( EnrollRepo::class, $this->enrollRepo);
    }

    public function testGetNonExisted() {
        $enroll = $this->enrollRepo->get( "QWER45T6U7I89OPOLKJHGFD", 0);
        $this->assertNull($enroll);
    }

//    public function testGetAndRemoveAfterSetup() {
//        $enroll = $this->enrollRepo->get ( self::$pripravaLoginKlicTouples[ 'login_name' ], self::$pripravaEventId );
//        $this->assertInstanceOf(Enroll::class, $enroll);
//
//        $l = $this->enrollRepo->remove($enroll);
//        $this->assertNull($l); //tady nevim zda testovat?... nevraci nic?
//    }

    public function testGetAfterSetUp() {
        $enroll = $this->enrollRepo->get( self::$pripravaLoginKlicTouples1[ 'login_name' ], self::$pripravaEventId);
        $this->assertInstanceOf(EnrollInterface::class, $enroll);
    }

    public function testAdd() {
        $enroll = new Enroll();
        $enroll->setLoginLoginNameFk(self::$pripravaLoginKlicTouples2[ 'login_name' ] );
        $enroll->setEventIdFk(self::$pripravaEventId);
        $this->enrollRepo->add($enroll);
        
        // pro automaticky|generovany klic a pro  overovany klic  - !!! zapise se hned !!!   DaoEditKeyDbVerifiedInterface
        // zde nezapise hned !!!!
        $this->assertFalse($enroll->isPersisted());
        $this->assertTrue($enroll->isLocked());
        // Enroll je zamčena po add.
        
    }
 
    public function testGetAfterAdd() {
        $enroll = $this->enrollRepo->get( self::$pripravaLoginKlicTouples2[ 'login_name' ], self::$pripravaEventId);

//        $login = $this->loginRepo->get(self::$loginKlic);
//        $this->assertInstanceOf(LoginInterface::class, $login);
//        $this->assertTrue(is_string($login->getLoginName()));
    }
//    
//
//
//    
//    public function testAddAndReread() {
//        $login = new Login();
//        $login->setLoginName(self::$loginKlic . "1");
//        $this->loginRepo->add($login);
//        $this->loginRepo->flush();
//        
//        $login = $this->loginRepo->get($login->getLoginName());      
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
//        $login = $this->loginRepo->get(self::$loginKlic . "1");    
//        $this->assertInstanceOf(LoginInterface::class, $login);
//        $this->assertTrue($login->isPersisted());
//        $this->assertFalse($login->isLocked());
//        
//        $login->lock();
//        $this->expectException( OperationWithLockedEntityException::class);
//        $this->loginRepo->remove($login);
//    }
//    
//    
//    
//    
//    public function testRemove() {
//        /** @var Login $login */
//        $login = $this->loginRepo->get(self::$loginKlic . "1" );
//                
//        $this->assertInstanceOf(LoginInterface::class, $login);
//        $this->assertTrue($login->isPersisted());
//        $this->assertFalse($login->isLocked());
//        
//        $this->loginRepo->remove($login);
//        
//        $this->assertTrue($login->isPersisted());
//        $this->assertTrue($login->isLocked());   // zatim zamcena entita, maže až při flush
//        $this->loginRepo->flush();
//        //  uz neni locked
//        $this->assertFalse($login->isLocked());
//        
//        // pokus o čtení, entita Login.self::$loginKlic  uz  neni
//        $login = $this->loginRepo->get(self::$loginKlic . "1" );
//        $this->assertNull($login);
//        
//    }
    
    
    
}

