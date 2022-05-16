<?php
declare(strict_types=1);

use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Dao\EnrollDao;
use Events\Model\Dao\LoginDao;
use Events\Model\Dao\EventDao;

use Model\RowData\RowData;
use Model\RowData\RowDataInterface;

/**
 *
 * @author pes2704
 */
class EnrollDaoTest extends AppRunner {

    private $container;
    /**
     *
     * @var EnrollDao
     */
    private $dao;
    private static $login_login_name_fk;
    private static $event_id_fk;
    private static $event_id_fk_2;

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        $container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(
                    (new Container(
                        )
                    )
                )
            );
        // nový login login_name a event id pro TestCase
        $prefix = "testVisitorForEnrollDaoTest";
        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        // prefix + uniquid - bez zamykání db
        do {
            $loginName = $prefix."_".uniqid();
            $login = $loginDao->get(['login_name' => $loginName]);
        } while ($login);

        $loginData = new RowData();
        $loginData->import(['login_name' => $loginName]);
        $loginDao->insert($loginData);

        self::$login_login_name_fk = $loginDao->get(['login_name' => $loginName])['login_name'];
        /** @var EventDao $eventDao */
        $eventDao = $container->get(EventDao::class);
        $eventData = new RowData();
        $eventData->import(["published" => 1]);
        $eventDao->insert($eventData);  // id je autoincrement
        self::$event_id_fk = $eventDao->lastInsertIdValue();
        $eventData = new RowData();
        $eventData->import(["published" => 1]);
        $eventDao->insert($eventData);  // id je autoincrement
        self::$event_id_fk_2 = $eventDao->lastInsertIdValue();
    }

    protected function setUp(): void {
        $this->container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(
                    (new Container(
                        )
                    )
                )
            );
        $this->dao = $this->container->get(EnrollDao::class);  // vždy nový objekt
    }

    protected function tearDown(): void {
    }

    public static function tearDownAfterClass(): void {

    }

    public function testSetUp() {
        $this->assertIsString(self::$login_login_name_fk);
        $this->assertIsString(self::$event_id_fk);
        $this->assertInstanceOf(EnrollDao::class, $this->dao);

    }

    public function testInsert() {
        $rowData = new RowData();
        $rowData->import(['login_login_name_fk' => self::$login_login_name_fk, 'event_id_fk' => self::$event_id_fk]);
        $this->dao->insert($rowData);
        $this->assertEquals(1, $this->dao->getRowCount());
    }

    public function testGet() {
        $enrollRow = $this->dao->get(['login_login_name_fk' => self::$login_login_name_fk, 'event_id_fk' => self::$event_id_fk]);
        $this->assertInstanceOf(RowDataInterface::class, $enrollRow);
    }

    public function test2Columns() {
        $enrollRow = $this->dao->get(['login_login_name_fk' => self::$login_login_name_fk, 'event_id_fk' => self::$event_id_fk]);
        $this->assertCount(2, $enrollRow);
    }

    public function testFindExistingRowsByLoginName() {
        $enrollRows = $this->dao->findByLoginNameFk(['login_login_name_fk' => self::$login_login_name_fk]);
        $this->assertIsArray($enrollRows);
        $this->assertGreaterThan(0, count($enrollRows));
        $this->assertInstanceOf(RowDataInterface::class, $enrollRows[0]);
    }

    public function testFindExistingRowsByEventId() {
        $enrollRows = $this->dao->findByEventIdFk(['event_id_fk' => self::$event_id_fk]);
        $this->assertIsArray($enrollRows);
        $this->assertGreaterThan(0, count($enrollRows));
        $this->assertInstanceOf(RowDataInterface::class, $enrollRows[0]);
    }

    public function testUpdate() {
        $enrollRow = $this->dao->get(['login_login_name_fk' => self::$login_login_name_fk, 'event_id_fk' => self::$event_id_fk]);
        $eventId = $enrollRow['event_id_fk'];
        $this->assertIsString($enrollRow['login_login_name_fk']);
        $this->assertIsInt($enrollRow['event_id_fk']);
        //
        $this->setUp();
        $enrollRow['event_id_fk'] = self::$event_id_fk_2;
        $this->dao->update($enrollRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $enrollRowRereaded = $this->dao->get(['login_login_name_fk' => self::$login_login_name_fk, 'event_id_fk' => self::$event_id_fk_2]);
        $this->assertInstanceOf(RowDataInterface::class, $enrollRowRereaded);
        $this->assertEquals(self::$event_id_fk_2, $enrollRowRereaded['event_id_fk']);

    }

    public function testFindByLoginNameFk() {
        $enrollRowsRereaded = $this->dao->findByLoginNameFk(['login_login_name_fk' => self::$login_login_name_fk]);
        $this->assertIsArray($enrollRowsRereaded);
        $this->assertGreaterThanOrEqual(1, count($enrollRowsRereaded));
        $this->assertInstanceOf(RowDataInterface::class, $enrollRowsRereaded[0]);
    }

    public function testFindByEventIdFk() {
        $enrollRowsRereaded = $this->dao->findByEventIdFk(['event_id_fk' => self::$event_id_fk_2]);
        $this->assertIsArray($enrollRowsRereaded);
        $this->assertGreaterThanOrEqual(1, count($enrollRowsRereaded));
        $this->assertInstanceOf(RowDataInterface::class, $enrollRowsRereaded[0]);
    }

    public function testFind() {
        $enrollRowsArray = $this->dao->find();
        $this->assertIsArray($enrollRowsArray);
        $this->assertGreaterThanOrEqual(1, count($enrollRowsArray));
        $this->assertInstanceOf(RowDataInterface::class, $enrollRowsArray[0]);
    }

    public function testDelete() {
        $enrollRow = $this->dao->get(['login_login_name_fk' => self::$login_login_name_fk, 'event_id_fk' => self::$event_id_fk_2]);

        $this->dao->delete($enrollRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $this->dao->delete($enrollRow);
        $this->assertEquals(0, $this->dao->getRowCount());

    }
}
