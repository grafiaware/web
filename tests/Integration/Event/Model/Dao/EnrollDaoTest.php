<?php
declare(strict_types=1);

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Dao\EnrollDao;
use Events\Model\Dao\LoginDao;
use Events\Model\Dao\EventDao;

use Model\RowData\RowData;
use Model\RowData\RowDataInterface;

use Model\Dao\Exception\DaoKeyVerificationFailedException;

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
            $login = $loginDao->get($loginName);
        } while ($login);

        $loginData = new RowData(['login_name' => $loginName]);
        $loginDao->insertWithKeyVerification($loginData);

        self::$login_login_name_fk = $loginDao->get($loginName)['login_name'];
        /** @var EventDao $eventDao */
        $eventDao = $container->get(EventDao::class);
        $eventData = new RowData(["published" => 1]);
        $eventDao->insert($eventData);  // id je autoincrement
        self::$event_id_fk = $eventDao->getLastInsertId();
        $eventData = new RowData(["published" => 1]);
        $eventDao->insert($eventData);  // id je autoincrement
        self::$event_id_fk_2 = $eventDao->getLastInsertId();
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
        $rowData = new RowData(['login_login_name_fk' => self::$login_login_name_fk, 'event_id_fk' => self::$event_id_fk]);
        $this->dao->insert($rowData);
        $this->assertEquals(1, $this->dao->getRowCount());
    }

    public function testGetExistingRow() {
        $enrollRow = $this->dao->get(self::$login_login_name_fk);
        $this->assertInstanceOf(RowDataInterface::class, $enrollRow);
    }

    public function test2Columns() {
        $enrollRow = $this->dao->get(self::$login_login_name_fk);
        $this->assertCount(2, $enrollRow);
    }

    public function testUpdate() {
        $enrollRow = $this->dao->get(self::$login_login_name_fk);
        $eventId = $enrollRow['event_id_fk'];
        $this->assertIsString($enrollRow['login_login_name_fk']);
        $this->assertIsInt($enrollRow['event_id_fk']);
        //
        $this->setUp();
        $enrollRow['event_id_fk'] = self::$event_id_fk_2;
        $this->dao->update($enrollRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $enrollRowRereaded = $this->dao->get(self::$login_login_name_fk);
        $this->assertInstanceOf(RowDataInterface::class, $enrollRowRereaded);
        $this->assertEquals(self::$event_id_fk_2, $enrollRowRereaded['event_id_fk']);

    }

    public function testFind() {
        $enrollRowsArray = $this->dao->find();
        $this->assertIsArray($enrollRowsArray);
        $this->assertGreaterThanOrEqual(1, count($enrollRowsArray));
        $this->assertInstanceOf(RowDataInterface::class, $enrollRowsArray[0]);
    }

    public function testDelete() {
        $enrollRow = $this->dao->get(self::$login_login_name_fk);

        $this->dao->delete($enrollRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $this->dao->delete($enrollRow);
        $this->assertEquals(0, $this->dao->getRowCount());

    }
}
