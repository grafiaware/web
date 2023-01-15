<?php
declare(strict_types=1);
namespace Test\Integration\Event\Model\Repository;

use Test\AppRunner\AppRunner;
use Pes\Container\Container;
use Container\EventsModelContainerConfigurator;
use Test\Integration\Event\Container\TestDbEventsContainerConfigurator;

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
    private static $pripravaLoginKlicTouples3;
    private static $pripravaLoginKlicTouples4;

    private static $pripravaEventId;
    private static $startTimestamp = '2000-01-01 01:01:01' ;



    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(
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
        $loginNam = self::$enrollLoginString . (string) (random_int(0, 9999));
        $rowData->offsetSet('login_name', $loginNam );
        $loginDao->insert($rowData);
        self::$pripravaLoginKlicTouples1 = $loginDao->getPrimaryKey($rowData->getArrayCopy());
         // toto je příprava testu, vlozi 2. login
        $loginNam = self::$enrollLoginString . (string) (random_int(0, 9999));
        $rowData->offsetSet('login_name', $loginNam );
        $loginDao->insert($rowData);
        self::$pripravaLoginKlicTouples2 = $loginDao->getPrimaryKey($rowData->getArrayCopy());
          // toto je příprava testu, vlozi 3. login
        $loginNam = self::$enrollLoginString . (string) (random_int(0, 9999));
        $rowData->offsetSet('login_name', $loginNam );
        $loginDao->insert($rowData);
        self::$pripravaLoginKlicTouples3 = $loginDao->getPrimaryKey($rowData->getArrayCopy());
          // toto je příprava testu, vlozi 4. login
        $loginNam = self::$enrollLoginString . (string) (random_int(0, 9999));
        $rowData->offsetSet('login_name', $loginNam );
        $loginDao->insert($rowData);
        self::$pripravaLoginKlicTouples4 = $loginDao->getPrimaryKey($rowData->getArrayCopy());

        //vlozi event
        /** @var EventDao $eventDao */
        $eventDao = $container->get(EventDao::class);
        $rowData = new RowData();
        $rowData->offsetSet('published',1 );
        $rowData->offsetSet('start', self::$startTimestamp  );
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

         /** @var EventDao $eventDao */
        $eventDao = $container->get(EventDao::class);
        $rows = $eventDao->find( "  `start` = '" . self::$startTimestamp  . "'"   /*, [] */ );
        foreach($rows as $row) {
            $ok = $eventDao->delete($row);
        }

         /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        $rows = $loginDao->find( " login_name LIKE '". self::$enrollLoginString . "%'", [] );
        foreach($rows as $row) {
            $ok = $loginDao->delete($row);
        }

    }

    protected function setUp(): void {
        $this->container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(
                    (new Container(  ) )  )
            );

        $this->enrollRepo = $this->container->get( EnrollRepo::class );
    }

    protected function tearDown(): void {
        //$this->enrollRepo->flush();
        $this->enrollRepo->__destruct();
    }

    public static function tearDownAfterClass(): void {
        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(
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
        // Enroll je zamčena po add - az do vykonani flush

    }

    public function testGetAfterAdd() {
        /** @var EnrollInterface $enroll */
        $enroll = $this->enrollRepo->get( self::$pripravaLoginKlicTouples2[ 'login_name' ], self::$pripravaEventId);
        $this->assertInstanceOf(EnrollInterface::class, $enroll);
        $this->assertTrue(is_string($enroll->getLoginLoginNameFk()));
    }

    public function testAddAndReread() {
        /** @var Enroll $enroll */
        $enroll = new Enroll();
        $enroll->setLoginLoginNameFk( self::$pripravaLoginKlicTouples3[ 'login_name' ] );
        $enroll->setEventIdFk( self::$pripravaEventId );
        $this->enrollRepo->add($enroll);

        $this->enrollRepo->flush();

        $enroll1 = $this->enrollRepo->get( $enroll->getLoginLoginNameFk(),  $enroll->getEventIdFk() );
        $this->assertTrue($enroll1->isPersisted() );
        $this->assertFalse($enroll1->isLocked());

        $this->assertTrue(is_string( $enroll1->getLoginLoginNameFk()));
        $this->assertEquals( self::$pripravaLoginKlicTouples3[ 'login_name' ], $enroll1->getLoginLoginNameFk() );
    }

    public function testFindByEvent(){
        $enrolls = $this->enrollRepo->findByEvent(self::$pripravaEventId);
        $this->assertTrue(is_array($enrolls));
    }

    public function testFindByLoginName(){
        $enrolls = $this->enrollRepo->findByLoginName(self::$pripravaLoginKlicTouples1['login_name'] );
        $this->assertTrue(is_array($enrolls));
    }


    public function testFindAll() {
        $enrolls = $this->enrollRepo->findAll();
        $this->assertTrue(is_array($enrolls));
    }



    public function testRemove_OperationWithLockedEntity() {
        /** @var Enroll $enroll */
        $enroll = new Enroll();
        $enroll->setLoginLoginNameFk( self::$pripravaLoginKlicTouples4[ 'login_name' ] );
        $enroll->setEventIdFk( self::$pripravaEventId );
        $this->enrollRepo->add($enroll);

        $this->assertFalse($enroll->isPersisted());
        $this->assertTrue($enroll->isLocked());

        $this->expectException( OperationWithLockedEntityException::class);
        $this->enrollRepo->remove($enroll);
   }



    public function testRemove() {
        /** @var Enroll $enroll */
        $enroll = $this->enrollRepo->get( self::$pripravaLoginKlicTouples2[ 'login_name' ], self::$pripravaEventId);

        $this->assertInstanceOf(EnrollInterface::class, $enroll);
        $this->assertTrue($enroll->isPersisted());
        $this->assertFalse($enroll->isLocked());

        $this->enrollRepo->remove($enroll);

        $this->assertTrue($enroll->isPersisted());
        $this->assertTrue($enroll->isLocked());   // zatim zamcena entita, maže až při flush
        $this->enrollRepo->flush();
        //  uz neni locked
        $this->assertFalse($enroll->isLocked());

        // pokus o čtení, entita Login.self::$loginKlic  uz  neni
        $enroll = $this->enrollRepo->get( self::$pripravaLoginKlicTouples2[ 'login_name' ], self::$pripravaEventId);
        $this->assertNull($enroll);

    }



}

