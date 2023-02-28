<?php
declare(strict_types=1);
namespace Test\Integration\Dao;

use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Container\EventsModelContainerConfigurator;
use Test\Integration\Event\Container\TestDbEventsContainerConfigurator;

use Events\Model\Dao\JobDao;
use Events\Model\Dao\JobTagDao;
use Events\Model\Dao\JobToTagDao;

use Events\Model\Dao\PozadovaneVzdelaniDao;
use Events\Model\Dao\CompanyDao;

use Model\RowData\RowData;
use Model\RowData\RowDataInterface;

use Model\Dao\Exception\DaoKeyVerificationFailedException;

/**
 *
 * @author
 */
class JobToTagDaoTest extends AppRunner {
    private $container;
    /**
     *
     * @var JobToTagDao
     */
    private $dao;

    private static $company_id;
    private static $stupen_fk;
    private static $job_id_fk;
    //private static $job_tag_tag_fk;
    //private static $job_tag_tag_fk2;
    private static $jobTagIdTouple;
    private static $jobTagIdTouple2;

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure( (new Container(   )  ) )
            );

        // nový  Company, PozadovaneVzdelani, Job, JobTtag,
        /** @var CompanyDao $companyDao */
        $companyDao = $container->get(CompanyDao::class);
        $companyData = new RowData();
        $companyData->import(['eventInstitutionName30' => "chacha" ]);
        $companyData->import(['name' => "CompanyName pro JobToTagDaoTest" ]);
        $companyDao->insert($companyData);
        self::$company_id = $companyDao->getLastInsertedPrimaryKey()[$companyDao->getAutoincrementFieldName()];

         /** @var PozadovaneVzdelaniDao $pozadovaneVzdelaniDao */
        $pozadovaneVzdelaniDao = $container->get(PozadovaneVzdelaniDao::class);
        $pozadovaneVzdelaniData = new RowData();
        $pozadovaneVzdelaniData->import(['stupen' => "999" ]);
        $pozadovaneVzdelaniData->import(['vzdelani' => "vzdelani 999" ]);
        // neodchycená výjimka znamená, že test neproběhne, protože setUpBeforeClass tady skončí
        try {
            $pozadovaneVzdelaniDao->insert($pozadovaneVzdelaniData);
            self::$stupen_fk = $pozadovaneVzdelaniDao->get(['stupen' => "999" ]) ['stupen'] ;
        } catch (DaoKeyVerificationFailedException $e) {
            self::$stupen_fk = "999" ;  // nouzovka
        }

         /** @var JobDao $jobDao */
        $jobDao = $container->get(JobDao::class);
        $jobData = new RowData();
        $jobData->import([ 'company_id' => self::$company_id,
                           'pozadovane_vzdelani_stupen' => self::$stupen_fk
                         ]);
        $ok = $jobDao->insert($jobData);
        self::$job_id_fk = $jobDao->getLastInsertedPrimaryKey()[$jobDao->getAutoincrementFieldName()];
        //lastInsertIdValue je metoda z  DaoAutoincrementTrait

        /** @var JobTagDao $jobTagDao */
        $jobTagDao = $container->get(JobTagDao::class);
        $jobTagData = new RowData();
        $jobTagData->import(["tag" => "nalepka moje"  ]);
        $ok =  $jobTagDao->insert($jobTagData);        
        self::$jobTagIdTouple =  $jobTagDao->getLastInsertedPrimaryKey();
        //self::$job_tag_tag_fk = "nalepka moje";

        $jobTagData = new RowData();
        $jobTagData->import(["tag" => "nalepka moje druha"  ]);
        $ok =  $jobTagDao->insert($jobTagData);
        self::$jobTagIdTouple2 =  $jobTagDao->getLastInsertedPrimaryKey();
        //self::$job_tag_tag_fk2 = "nalepka moje druha";
    }

    protected function setUp(): void {
        $this->container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(  (new Container() )  )
            );
        $this->dao = $this->container->get( JobToTagDao::class);  // vždy nový objekt
    }


    protected function tearDown(): void {
    }

    public static function tearDownAfterClass(): void {
        self::bootstrapBeforeClass();
        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure( (new Container( ) )  )
            );



        //maze po sobe  vyrobene věty v tabulkach
        /** @var JobToTagDao $jobToTagDao */
        $jobToTagDao = $container->get(JobToTagDao::class);  ////aby slo smazat  job a job_tag
        $jobToTagData = $jobToTagDao->get(['job_tag_id' =>  self::$jobTagIdTouple['id'], 'job_id' => self::$job_id_fk  ]);
        if ($jobToTagData) {
                $ok = $jobToTagDao->delete($jobToTagData);}
        $jobToTagData = $jobToTagDao->get(['job_tag_id' =>  self::$jobTagIdTouple2['id'], 'job_id' => self::$job_id_fk  ]);
        if ($jobToTagData) {
                $ok = $jobToTagDao->delete($jobToTagData);}

        /** @var JobDao $jobDao */  //aby slo smazat   company, pozadovane_vzdelani
        $jobDao = $container->get(JobDao::class);
        $jobRow = $jobDao->get( [ 'id' => self::$job_id_fk ] );
        $ok = $jobDao->delete($jobRow);
        /** @var JobTagDao $jobTagDao */
        $jobTagDao = $container->get(JobTagDao::class);
        $jobTagData = $jobTagDao->get(['id' =>  self::$jobTagIdTouple['id'] ]);
        $ok = $jobTagDao->delete($jobTagData);
        $jobTagData = $jobTagDao->get(['id' =>  self::$jobTagIdTouple2['id'] ]);
        $ok = $jobTagDao->delete($jobTagData);
        /** @var PozadovaneVzdelaniDao $pozadovaneVzdelaniDao */
        $pozadovaneVzdelaniDao = $container->get(PozadovaneVzdelaniDao::class);
        $pozadovaneVzdelaniData = $pozadovaneVzdelaniDao->get(['stupen' => 999]);
        $ok = $pozadovaneVzdelaniDao->delete($pozadovaneVzdelaniData);
        /** @var CompanyDao $companyDao */
        $companyDao = $container->get(CompanyDao::class);
        $companyData = $companyDao->get(['id' =>  self::$company_id ]);
        $ok = $companyDao->delete($companyData);

    }

    public function testSetUp() {
        $this->assertIsString(self::$job_id_fk);   //lastInsertId vraci z DB string
        $this->assertIsArray(self::$jobTagIdTouple);
        $this->assertIsArray(self::$jobTagIdTouple2);
        $this->assertInstanceOf(JobToTagDao::class, $this->dao);
    }

//**

    public function testInsert() {
        $rowData = new RowData();
        $rowData->import(['job_id' =>  self::$job_id_fk ]);
        $rowData->import(['job_tag_id' =>  self::$jobTagIdTouple['id'] /*$job_tag_tag_fk */ ]);
        $this->dao->insert($rowData);
        $this->assertEquals(1, $this->dao->getRowCount());
    }

    public function test2Columns() {
        $jobToTagRow = $this->dao->get(['job_id' =>  self::$job_id_fk, 'job_tag_id' =>  self::$jobTagIdTouple['id'] ] );
        $this->assertCount(2, $jobToTagRow);
    }



    public function testGet() {
        $jobToTagRows = $this->dao->get( ['job_id' =>  self::$job_id_fk ,  'job_tag_id' =>  self::$jobTagIdTouple['id'] ] );
        $this->assertInstanceOf(RowDataInterface::class, $jobToTagRows );
    }



    public function testFindExistingRowsByJobId() {
        $jobToTagRows = $this->dao->findByJobIdFk( ['job_id' => self::$job_id_fk ]);
        $this->assertIsArray($jobToTagRows);
        $this->assertGreaterThan(0, count($jobToTagRows));
        $this->assertInstanceOf(RowDataInterface::class, $jobToTagRows[0]);
    }

//    public function testFindExistingRowsByJobTagTag() {
//        $jobToTagRows = $this->dao->findByJobTagFk( ['job_tag_tag' => self::$job_tag_tag_fk] );
//        $this->assertIsArray($jobToTagRows);
//        $this->assertGreaterThan(0, count($jobToTagRows));
//        $this->assertInstanceOf(RowDataInterface::class, $jobToTagRows[0]);
//    }
    public function testFindExistingRowsByJobTagId() {
        $jobToTagRows = $this->dao->findByJobTagIdFk( [ 'job_tag_id' =>  self::$jobTagIdTouple['id']   ] );
        $this->assertIsArray($jobToTagRows);
        $this->assertGreaterThan(0, count($jobToTagRows));
        $this->assertInstanceOf(RowDataInterface::class, $jobToTagRows[0]);
    }



    public function testUpdate() {
        $jobToTagRows = $this->dao->get( ['job_id' =>  self::$job_id_fk ,   'job_tag_id' =>  self::$jobTagIdTouple['id']  ] );
        $this->assertIsInt($jobToTagRows['job_id']);
        $this->assertIsString($jobToTagRows['job_tag_id']);

        $this->setUp(); //nove dao
        $jobToTagRows['job_tag_id'] =  self::$jobTagIdTouple2['id'] ;
        $this->dao->update( $jobToTagRows );
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $jobToTagRowsRereaded = $this->dao->get( ['job_id' =>  self::$job_id_fk ,   'job_tag_id' =>  self::$jobTagIdTouple2['id'] ] );
        $this->assertInstanceOf(RowDataInterface::class, $jobToTagRowsRereaded);
        $this->assertEquals(self::$jobTagIdTouple2['id'], $jobToTagRowsRereaded['job_tag_id']);

    }


    public function testFindByJobIdFk() {
        $jobToTagRowsRereaded = $this->dao->findByJobIdFk(['job_id' => self::$job_id_fk]);
        $this->assertIsArray($jobToTagRowsRereaded);
        $this->assertGreaterThanOrEqual(1, count($jobToTagRowsRereaded));
        $this->assertInstanceOf(RowDataInterface::class, $jobToTagRowsRereaded[0]);
    }

    public function testFindByJobTagTagFk() {
        $jobToTagRowsRereaded = $this->dao->findByJobTagFk(['job_tag_tag' => self::$job_tag_tag_fk2]);
        $this->assertIsArray($jobToTagRowsRereaded);
        $this->assertGreaterThanOrEqual(1, count($jobToTagRowsRereaded));
        $this->assertInstanceOf(RowDataInterface::class, $jobToTagRowsRereaded[0]);
    }

    public function testFind() {
        $jobToTagRowsArray = $this->dao->find();
        $this->assertIsArray($jobToTagRowsArray);
        $this->assertGreaterThanOrEqual(1, count($jobToTagRowsArray));
        $this->assertInstanceOf(RowDataInterface::class, $jobToTagRowsArray[0]);
    }


    public function testDelete() {
        $jobToTagRow = $this->dao->get( ['job_id' =>  self::$job_id_fk ,  'job_tag_tag' =>  self::$job_tag_tag_fk2 ] );

        $this->dao->delete($jobToTagRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $this->dao->delete($jobToTagRow);
        $this->assertEquals(0, $this->dao->getRowCount());

    }
}
