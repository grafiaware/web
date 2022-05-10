<?php

declare(strict_types=1);

use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Dao\JobDao;
use Events\Model\Dao\JobTagDao;
use Events\Model\Dao\JobToTagDao;

use Model\RowData\RowData;
use Model\RowData\RowDataInterface;

/**
 *
 * @author 
 */
class JobToTagTest extends AppRunner {

    private $container;
    /**
     *
     * @var JobToTagDao
     */
    private $dao;
    private static $job_id_fk;
    private static $job_tag_tag_fk;

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
        // nový Job, job_id a job_tag_tag  pro TestCase
        //
        /** @var JobDao $jobDao */
        $jobDao = $container->get(JobDao::class);
        // 
        do {
            $jobIdNew = mt_rand();
            $job = $jobDao->get(['id' => $jobIdNew]);
        } while ($job);
        $jobData = new RowData();
        $jobData->import(['id' => $jobIdNew]);
        $jobDao->insert($jobData);

        
        self::$job_id_fk = $jobDao->get(['job_id' => $jobIdNew])['job_id'];
        /** @var JobTagDao $jobTagDao */
        $jobTagDao = $container->get(JobTagDao::class);
        $jobTagData = new RowData();
        
        $jobTagData->import(["tag" => "nalepka" .  $mt_rand() ]);
        $jobTagDao->insert($jobTagData);  
        self::$job_tag_tag_fk = $jobTagDao->lastInsertIdValue(); //to je vlastne "nalepka" .  $mt_rand()
//        $eventData = new RowData();
//        $eventData->import(["published" => 1]);
//        $eventDao->insert($eventData);  // id je autoincrement
//        self::$event_id_fk_2 = $eventDao->lastInsertIdValue();
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
        $this->dao = $this->container->get(JobToTag::class);  // vždy nový objekt
    }

    protected function tearDown(): void {
    }

    public static function tearDownAfterClass(): void {

    }

    public function testSetUp() {
        $this->aasassertIsIn(self::$job_id_fk);
        $this->assertIsString(self::$job_tag_tag_fk);
        $this->assertInstanceOf(JobToTag::class, $this->dao);
    }

//**    
    
    public function testInsert() {
        $rowData = new RowData();
        $rowData->import(['login_login_name_fk' => self::$login_login_name_fk, 'event_id_fk' => self::$event_id_fk]);
        $this->dao->insert($rowData);
        $this->assertEquals(1, $this->dao->getRowCount());
    }

    public function testGetByPk() {
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
