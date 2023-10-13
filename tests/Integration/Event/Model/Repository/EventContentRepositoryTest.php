<?php
declare(strict_types=1);
namespace Test\Integration\Event\Model\Repository;

use Test\AppRunner\AppRunner;
use Pes\Container\Container;

use Container\EventsModelContainerConfigurator;
use Test\Integration\Event\Container\TestDbEventsContainerConfigurator;

use Events\Model\Entity\EventContent;
use Events\Model\Entity\EventContentInterface;
use Events\Model\Dao\EventContentDao;
use Events\Model\Repository\EventContentRepo;
use Events\Model\Dao\InstitutionDao;
use Events\Model\Dao\InstitutionTypeDao;

use Model\Repository\Exception\OperationWithLockedEntityException;
use Model\RowData\RowData;


/**
 * Description of EventContentRepositoryTest
 */
class EventContentRepositoryTest extends AppRunner {
    private $container;

    /**
     *
     * @var EventContentRepo
     */
    private $eventContentRepo;

    private static $eventContentId;
    private static $eventContentTitle = "testEventContent";

    private static $pripravaInstitutionId;
    private static $institutionName = "proTestEventContentRepo";

    private static $pripravaInstitutionTypeId;
    private static $institutionType = "proTestEventContentRepo";

    private static $idI_poAdd;
    private static $idR_poAddRereaded; //pro test

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

        // toto je příprava testu
        /** @var EventContentDao $eventContentDao */
        $eventContentDao = $container->get(EventContentDao::class);
        $rowData = new RowData();
        $rowData->offsetSet('party', "Kluk, Ťuk, Muk, Kuk, Buk, Guk");
        $rowData->offsetSet('perex', "sůheůrjheů");       
        $rowData->offsetSet('title', self::$eventContentTitle . "titlePrednasky");
        $rowData->offsetSet('event_content_type_id_fk', 3 ); //tj. Prednaska
        $eventContentDao->insert($rowData);
        self::$eventContentId = $eventContentDao->getLastInsertedPrimaryKey()[$eventContentDao->getAutoincrementFieldName()];

        //----------------priprava------------------------------------------------------
        /** @var InstitutionDao $institutionDao */
        $institutionDao = $container->get(InstitutionDao::class);
        $rowData = new RowData();
        $rowData->import([
            'name' => self::$institutionName
        ]);
        $institutionDao->insert($rowData);
        self::$pripravaInstitutionId = $institutionDao->getLastInsertedPrimaryKey()[$institutionDao->getAutoincrementFieldName()];
        /** @var InstitutionTypeDao $institutionTypeDao */
        $institutionTypeDao = $container->get(InstitutionTypeDao::class);
        $rowData = new RowData();
        $rowData->import([
            'institution_type' => self::$institutionType
        ]);
        $institutionTypeDao->insert($rowData);
        self::$pripravaInstitutionTypeId = $institutionTypeDao->getLastInsertedPrimaryKey()[$institutionTypeDao->getAutoincrementFieldName()];
    }



    private static function deleteRecords(Container $container) {
        /** @var EventContentDao $eventContentDao */
        $eventContentDao = $container->get(EventContentDao::class);
        $rows = $eventContentDao->find("title LIKE '". self::$eventContentTitle . "%'", []);
        foreach($rows as $row) {
                $eventContentDao->delete($row);
        }
        //-------------------------------------------------------------
         /** @var InstitutionDao $institutionDao */
        $institutionDao = $container->get(InstitutionDao::class);
        $rows = $institutionDao->find( "name LIKE '". self::$institutionName . "%'", [] );
        foreach($rows as $row) {
            $ok = $institutionDao->delete($row);
        }

         /** @var InstitutionTypeDao $institutionTypeDao */
        $institutionTypeDao = $container->get(InstitutionTypeDao::class);
        $rows = $institutionTypeDao->find( "institution_type LIKE '". self::$institutionType . "%'", [] );
        foreach($rows as $row) {
            $ok = $institutionTypeDao->delete($row);
        }

    }



    protected function setUp(): void {
        $this->container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(new Container())
            );
        $this->eventContentRepo = $this->container->get(EventContentRepo::class);
    }



    protected function tearDown(): void {
        //$this->eventContentRepo->flush();
        $this->eventContentRepo->__destruct();
    }

    public static function tearDownAfterClass(): void {
        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(
                    (new Container( )  ) )
            );
        self::deleteRecords($container);
    }

    public function testSetUp() {
        $this->assertInstanceOf(EventContentRepo::class, $this->eventContentRepo);
    }

    public function testGetNonExisted() {
        $event = $this->eventContentRepo->get(0);
        $this->assertNull($event);
    }

    public function testGetAfterSetup() {
        $eventContent = $this->eventContentRepo->get(self::$eventContentId);    // !!!! jenom po insertu v setUp - hodnotu vrací dao
        $this->assertInstanceOf(EventContentInterface::class, $eventContent);
    }



    public function testAdd() {
        $eventContent = new EventContent();
        $eventContent->setInstitutionIdFk(null);
        $eventContent->setParty("testEventContentKluk, Ťuk, Muk, Kuk, Buk, Guk");
        $eventContent->setPerex("testEventContent Perexůrjheů");
        $eventContent->setTitle("testEventContent TitlePřednáška");
        $eventContent->setEventContentTypeIdFk( 3 );  //Přednáška

        $this->eventContentRepo->add($eventContent); //zapise hned

          /** @var EventContent $eventContentRereaded */
        $eventContentRereaded = $this->eventContentRepo->get($eventContent->getId());
        $this->assertInstanceOf(EventContentInterface::class, $eventContentRereaded);
        $this->assertTrue($eventContentRereaded->isPersisted());
        $this->assertFalse($eventContentRereaded->isLocked());

        //$eventContentRereaded, $eventContent ... jsou odkazy na stejny objekt
        self::$idI_poAdd = $eventContent->getId();
        self::$idR_poAddRereaded = $eventContentRereaded->getId();
        $this->assertEquals(self::$idI_poAdd, self::$idR_poAddRereaded );

        $eventContent->setInstitutionIdFk( self::$pripravaInstitutionId ); //zapise do entity, ktera je v repository
    }



    public function testAddAndReread_II() {
        $eventContent = $this->eventContentRepo->get(self::$idI_poAdd);

        /** @var EventContent $eventContentRereaded2 */
        $eventContentRereaded2 = $this->eventContentRepo->get($eventContent->getId());
        $this->assertInstanceOf(EventContent::class, $eventContentRereaded2);
        $this->assertTrue($eventContentRereaded2->isPersisted());
        $this->assertFalse($eventContentRereaded2->isLocked());

        $this->assertEquals(self::$pripravaInstitutionId, $eventContentRereaded2->getInstitutionIdFk() );
    }


    public function testFindAll() {
        $eventContents = $this->eventContentRepo->findAll();
        $this->assertTrue(is_array($eventContents));
    }



    public function testFind() {
        $eventContents = $this->eventContentRepo->find("perex LIKE 'testEventContent%'", []);
        $this->assertTrue(is_array($eventContents));
    }



    public function testGetAfterAdd() {
        $eventContent = $this->eventContentRepo->get(self::$idI_poAdd);
        $this->assertInstanceOf(EventContent::class, $eventContent);
        $this->assertIsString($eventContent->getParty());
    }


    public function testGetAndRemoveAfterAdd() {
        $eventContent = $this->eventContentRepo->get(self::$idI_poAdd);
        $this->eventContentRepo->remove($eventContent);
        $this->assertTrue($eventContent->isPersisted());
        $this->assertTrue($eventContent->isLocked());
    }



    public function testAddAndReread() {
        $eventContent = new EventContent();
        $eventContent->setParty("testEventContent Fšicí");
        $eventContent->setPerex("testEventContent Vážení přátelé ANO!");
        $eventContent->setTitle("testEventContent Přednáška pro fšecky.");
        $eventContent->setEventContentTypeIdFk( 3 );   //"Přednáška"
        
        $this->eventContentRepo->add($eventContent);  //zapise hned, auto gener.klic
        //$this->eventContentRepo->flush();

        $eventContentRe = $this->eventContentRepo->get($eventContent->getId());
        $this->assertTrue($eventContentRe->isPersisted() );
        $this->assertTrue(is_string($eventContent->getPerex()));
    }


    public function testRemove_OperationWithLockedEntity() {
        $eventContent = $this->eventContentRepo->get(self::$eventContentId);
        $this->assertInstanceOf( EventContent::class, $eventContent);
        $this->assertTrue($eventContent->isPersisted());
        $this->assertFalse($eventContent->isLocked());

        $eventContent->lock();
        $this->expectException( OperationWithLockedEntityException::class);
        $this->eventContentRepo->remove($eventContent);
    }


    public function testRemove() {
        $eventContent = $this->eventContentRepo->get(self::$eventContentId);
        $this->assertInstanceOf(EventContent::class, $eventContent);
        $this->assertTrue($eventContent->isPersisted());
        $this->assertFalse($eventContent->isLocked());

        $this->eventContentRepo->remove($eventContent);

        $this->assertTrue($eventContent->isPersisted());
        $this->assertTrue($eventContent->isLocked());   // maže až při flush
        $this->eventContentRepo->flush();
        // uz neni locked
        $this->assertFalse($eventContent->isLocked());

        // pokus o čtení, entita uz  neni
        $eventContent = $this->eventContentRepo->get(self::$eventContentId);
        $this->assertNull($eventContent);
    }



}
