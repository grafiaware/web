<?php
declare(strict_types=1);
namespace Test\Integration\Dao;

use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Container\EventsModelContainerConfigurator;   //EventsContainerConfigurator
use Test\Integration\Event\Container\TestDbEventsContainerConfigurator;

use Events\Model\Dao\VisitorJobRequestDao;
use Events\Model\Dao\LoginDao;
use Events\Model\Dao\JobDao;
use Events\Model\Dao\CompanyDao;

use Model\RowData\RowData;
use Model\RowData\RowDataInterface;

/**
 * Description of VisitorJobRequestTest
 *
 * @author vlse2610
 */
class VisitorJobRequestDaoTest  extends AppRunner {
    private $container;
    /**
     *
     * @var  VisitorJobRequestDao
     */
    private $dao;

    private static $loginName;
    private static $companyPrimaryKey;
    private static $jobPrimaryKey;

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        $container =
            (new EventsModelContainerConfigurator())->configure(   (new TestDbEventsContainerConfigurator())->configure( (new Container()   )     )
            );

        // nový login login_name, company, job
        $prefix = "Visi.JobReq.DaoTest";
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
        self::$loginName = $loginDao->get(['login_name' => $loginName])['login_name'];

        /** @var CompanyDao $companyDao */
        $companyDao = $container->get( CompanyDao::class);
        $companyData = new RowData();
        $companyData->offsetSet( 'name' , "pomocna pro VisitorJobRequestDaoTest"  );
        $companyDao->insert($companyData);
        self::$companyPrimaryKey = $companyDao->getLastInsertedPrimaryKey();

        /** @var JobDao $jobDao */
        $jobDao = $container->get(JobDao::class);
        $jobData = new RowData();
        $jobData->import( ['pozadovane_vzdelani_stupen' => 1, 'company_id' =>self::$companyPrimaryKey['id']] );
        $jobDao->insert($jobData);
        self::$jobPrimaryKey = $jobDao->getLastInsertedPrimaryKey();
    }

    protected function setUp(): void {
        $this->container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(
                    (new Container(  )  )
                )
            );
        $this->dao = $this->container->get(VisitorJobRequestDao::class);  // vždy nový objekt
    }



    protected function tearDown(): void {
    }

    public static function tearDownAfterClass(): void {
        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(
                    (new Container( ) )
                )
            );

        //smaze company a job
        $companyDao = $container->get(CompanyDao::class);
        $companyRow = $companyDao->get( self::$companyPrimaryKey );
        $companyDao->delete($companyRow);
        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        $loginRow  = $loginDao->get( ['login_name' => self::$loginName ] );
        $loginDao->delete($loginRow);
    }


    public function testSetUp() {
        $this->assertInstanceOf(VisitorJobRequestDao::class, $this->dao);
    }


    public function testInsert() {
        $rowData = new RowData();
        $rowData->import( [  'login_login_name' => self::$loginName,
                             'job_id' => self::$jobPrimaryKey['id'],
                             'position_name' => 'název pozice Tesař'
                          ] );
        $this->dao->insert($rowData);
        $this->assertEquals(1, $this->dao->getRowCount());
    }

    public function testGet() {
        $visitorJobRequestRow = $this->dao->get( ['login_login_name' => self::$loginName,  'job_id' =>  self::$jobPrimaryKey['id'] ] );
        $this->assertInstanceOf(RowDataInterface::class, $visitorJobRequestRow);
    }

    public function test13Columns() {
        $visitorJobRequestRow = $this->dao->get( ['login_login_name' => self::$loginName,  'job_id' =>  self::$jobPrimaryKey['id'] ] );
        $this->assertCount(13, $visitorJobRequestRow);
    }



    public function testUpdate() {
        $visitorJobRequestRow = $this->dao->get( ['login_login_name' => self::$loginName, 'job_id' =>  self::$jobPrimaryKey['id']  ]);
        $loginName = $visitorJobRequestRow['login_login_name'];
        $this->assertIsString($visitorJobRequestRow['login_login_name']);


        $this->setUp();
        $visitorJobRequestRow['name'] = "jmeno nevim čeho";
        $this->dao->update($visitorJobRequestRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $visitorJobRequestRowRereaded = $this->dao->get( ['login_login_name' => self::$loginName, 'job_id' =>  self::$jobPrimaryKey['id'] ]);
        $this->assertInstanceOf(RowDataInterface::class, $visitorJobRequestRowRereaded);
        $this->assertEquals( "jmeno nevim čeho", $visitorJobRequestRowRereaded['name']);

     }



    public function testFind() {
        $visitorJobRequestRowsArray = $this->dao->find();
        $this->assertIsArray($visitorJobRequestRowsArray);
        $this->assertGreaterThanOrEqual(1, count($visitorJobRequestRowsArray));
        $this->assertInstanceOf(RowDataInterface::class, $visitorJobRequestRowsArray[0]);
    }



    public function testDelete() {
        $visitorJobRequestRow = $this->dao->get( ['login_login_name' => self::$loginName, 'job_id' =>  self::$jobPrimaryKey['id']]);
        $this->dao->delete($visitorJobRequestRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $this->dao->delete($visitorJobRequestRow);
        $this->assertEquals(0, $this->dao->getRowCount());

   }


}
