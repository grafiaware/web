<?php
declare(strict_types=1);
namespace Test\Integration\Dao;

use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Container\EventsModelContainerConfigurator;
use Test\Integration\Event\Container\TestDbEventsContainerConfigurator;

use Events\Model\Dao\JobDao;
use Events\Model\Dao\PozadovaneVzdelaniDao;
use Events\Model\Dao\CompanyDao;
use Events\Model\Dao\JobToTagDao;
use Events\Model\Dao\VisitorJobRequestDao;
use Events\Model\Dao\LoginDao;

use Model\RowData\RowData;
use Model\RowData\RowDataInterface;

use Model\Dao\Exception\DaoKeyVerificationFailedException;

/**
 * Description of JobDaoTest
 *
 * @author vlse2610
 */
class JobDaoTest  extends AppRunner {
    private $container;
    /**
     *
     * @var JobDao
     */
    private $dao;

    private static $jobPrimaryKey;
    private static $stupen_fk;
    private static $companyPrimaryKey;
    private static $login_login_name;
    //private static $jobTagTouple;
    private static $jobTagIdTouple;

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(   (new Container(  )   )    )
            );

        // do company ulozit -- potrebuju company_id pro job
        // do pozadovane vzdelani ulozit -- potrebuji stupen  pro job
        // v job - id je autoincrement
        /** @var CompanyDao $companyDao */
        $companyDao = $container->get(CompanyDao::class);
        $companyData = new RowData();
        $companyData->import(['name' => "CompanyName pro JobDaoTest" ]);
        $companyDao->insert($companyData);
        self::$companyPrimaryKey = $companyDao->getLastInsertedPrimaryKey();

        /** @var PozadovaneVzdelaniDao $pozadovaneVzdelaniDao */
        $pozadovaneVzdelaniDao = $container->get(PozadovaneVzdelaniDao::class);
        $pozadovaneVzdelaniData = new RowData();
        $pozadovaneVzdelaniData->import(['stupen' => "999", 'vzdelani' => "vzdelani 999 proJobDaoTest" ]);
        // neodchycená výjimka znamená, že test neproběhne, protože setUpBeforeClass tady skončí
        try {
            $pozadovaneVzdelaniDao->insert($pozadovaneVzdelaniData);
            self::$stupen_fk = $pozadovaneVzdelaniDao->get(['stupen' => "999" ]) ['stupen'] ;
        } catch (DaoKeyVerificationFailedException $e) {
            self::$stupen_fk = "999" ;  // nouzovka
        }
        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        do {
            $loginName =  uniqid();
            $loginPouzit = $loginDao->get(['login_name' => $loginName]);
        } while ($loginPouzit);
        $loginData = new RowData();
        $loginData->import(['login_name' => $loginName]);
        $loginDao->insert($loginData);
        self::$login_login_name = $loginDao->get(['login_name' => $loginName])['login_name'];

        //*
        //self::$jobTagTouple = [ 'job_tag_tag' => 'technická'];
        self::$jobTagIdTouple = [ 'id' => '3'];    //'technická'
    }

    protected function setUp(): void {
        $this->container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(new Container())
            );
        $this->dao = $this->container->get(JobDao::class);  // vždy nový objekt
    }

    protected function tearDown(): void {
    }

    public static function tearDownAfterClass(): void {
         $container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure( (new Container(  ) )   )
            );

        /** @var PozadovaneVzdelaniDao $pozadovaneVzdelaniDao */
        $pozadovaneVzdelaniDao = $container->get(PozadovaneVzdelaniDao::class);
        $pozadovaneVzdelaniData = $pozadovaneVzdelaniDao->get(['stupen' => "999"]);
        $ok = $pozadovaneVzdelaniDao->delete($pozadovaneVzdelaniData);

        /** @var CompanyDao $companyDao */
        $companyDao = $container->get(CompanyDao::class);
        $companyData = $companyDao->get(self::$companyPrimaryKey);
        $ok = $companyDao->delete($companyData);

        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        $loginData = $loginDao->get(['login_name' => self::$login_login_name]);
        $loginDao->delete($loginData);

    }

    public function testSetUp() {
        $this->assertIsNumeric(self::$companyPrimaryKey['id']);
        $this->assertIsNumeric(self::$stupen_fk);

        $this->assertInstanceOf(JobDao::class, $this->dao);

    }
      public function testInsert() {
        //vvrobit job
        $rowData = new RowData();
        $rowData->import( ['company_id' => self::$companyPrimaryKey['id'], 'pozadovane_vzdelani_stupen' => self::$stupen_fk  ]);
        $this->dao->insert($rowData);
        self::$jobPrimaryKey = $this->dao->getLastInsertedPrimaryKey();
        $this->assertIsArray(self::$jobPrimaryKey);
        $numRows = $this->dao->getRowCount();
        $this->assertEquals(1, $numRows);


        //vyrobit job_to_tag
        /** @var JobToTagDao $jobToTagDao */
        $jobToTagDao = $this->container->get(JobToTagDao::class);
        $jobToTagData = new RowData();
        $jobToTagData->import( [ 'job_tag_id' => self::$jobTagIdTouple ['id']  , 'job_id'=>self::$jobPrimaryKey['id'] ] );
        $jobToTagDao->insert($jobToTagData);

        //vyrobit visitor_job_request
        /** @var VisitorJobRequestDao $visitorJobRequestDao */
        $visitorJobRequestDao =  $this->container->get( VisitorJobRequestDao::class);
        $visitorJobRequestData = new RowData();
        $visitorJobRequestData->import( ['job_id'=>self::$jobPrimaryKey['id'], 'login_login_name' => self::$login_login_name,
                                         'position_name'=>"název pozice" ] );
        $visitorJobRequestDao->insert( $visitorJobRequestData );

    }

    public function testGetExistingRow() {
        $jobRow = $this->dao->get(self::$jobPrimaryKey);
        $this->assertInstanceOf(RowDataInterface::class, $jobRow);
    }


    public function test8Columns() {
        $jobRow = $this->dao->get(self::$jobPrimaryKey);
        $this->assertCount(8, $jobRow);
    }


    public function testUpdate() {
        $jobRow = $this->dao->get(self::$jobPrimaryKey);
        $name = $jobRow['nazev'];
        $this->assertNull($jobRow['nazev'] );

        $this->setUp();
        $updated = 'updated name'; //. $name;
        $jobRow['nazev'] = $updated;
        $this->dao->update($jobRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $jobRowRereaded = $this->dao->get(self::$jobPrimaryKey);
        $this->assertEquals($jobRow, $jobRowRereaded);
        $this->assertEquals($updated, $jobRowRereaded['nazev']);
    }

    public function testFind() {
        $jobRow = $this->dao->find();
        $this->assertIsArray($jobRow);
        $this->assertGreaterThanOrEqual(1, count($jobRow));
        $this->assertInstanceOf(RowDataInterface::class, $jobRow[0]);
    }

    public function testDelete() {
        $jobRow = $this->dao->get(self::$jobPrimaryKey);
        $this->dao->delete($jobRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $companyRow = $this->dao->get(self::$jobPrimaryKey);
        $this->assertNull($companyRow);


        //kontrola CASCADE
        //job_to_tag
        /** @var JobToTagDao $jobToTagDao */
        $jobToTagDao = $this->container->get(JobToTagDao::class);
        $jobToTagData = $jobToTagDao->get( ['job_id' => self::$jobPrimaryKey['id'], 'job_tag_id' => self::$jobTagIdTouple['id'] ] );
        $this->assertNull($jobToTagData);

        //visitor_job_request
        /** @var VisitorJobRequestDao $visitorJobRequestDao */
        $visitorJobRequestDao = $this->container->get( VisitorJobRequestDao::class);
        $visitorJobRequestData = $visitorJobRequestDao->get( ['login_login_name' => self::$login_login_name, 'job_id' => self::$jobPrimaryKey['id']] );
        $this->assertNull($visitorJobRequestData);

    }
}
