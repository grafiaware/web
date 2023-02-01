<?php
declare(strict_types=1);
namespace Test\Integration\Dao;

use Test\AppRunner\AppRunner;

use Pes\Container\Container;
use Container\EventsModelContainerConfigurator;
use Test\Integration\Event\Container\TestDbEventsContainerConfigurator;

use Events\Model\Dao\CompanyDao;
use Events\Model\Dao\VisitorJobRequestDao;
use Events\Model\Dao\VisitorProfileDao;
use Events\Model\Dao\RepresentativeDao;
use Events\Model\Dao\JobDao;

use Events\Model\Dao\LoginDao;

use Model\RowData\RowData;
use Model\RowData\RowDataInterface;
use Pes\Database\Statement\Exception\ExecuteException;

/**
 * Description of LoginDaoTest
 *
 * @author vlse2610
 */
class LoginDaoTest extends AppRunner {

    const LOGIN_NAME_PREFIX = "LoginDaoTest_";
    const COMPANY_NAME_PREFIX = "LoginDaoTest_";

    private $container;
    /**
     *
     * @var LoginDao
     */
    private $dao;

    private static $loginNameTouple ;
    private static $companyPrimaryKey;
    private static $jobPrimaryKey;
    private static $visitorJobTouples;
    private static $visitorProfileTouples;
    private static $representativeIdTouple;


    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        self::removeRcords();
        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(new Container())
            );

        // nova company - priprava potrebne propojene tabulky
        /** @var CompanyDao $companyDao */
        $companyDao = $container->get(CompanyDao::class);
        $rowData = new RowData();
        $rowData->offsetSet('name', self::COMPANY_NAME_PREFIX."Company1");
        $rowData->offsetSet('eventInstitutionName30', 'ShortyCo.');
        $companyDao->insert($rowData);
        self::$companyPrimaryKey =  $companyDao->getLastInsertedPrimaryKey();

        // nova job - priprava potrebne propojene tabulky
         /** @var JobDao $jobDao */
        $jobDao = $container->get(JobDao::class);
        $jobData = new RowData();
        $jobData->import([ 'company_id' => self::$companyPrimaryKey['id'],
                           'pozadovane_vzdelani_stupen' => 1
                         ]);
        $ok = $jobDao->insert($jobData);
        self::$jobPrimaryKey = $jobDao->getLastInsertedPrimaryKey();
    }

    private static function removeRcords() {
        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(new Container())
            );
        //napred smazat vsechny zavisle, pak smazat login
        /** @var VisitorProfileDao $visitorProfileDao */
        $visitorProfileDao = $container->get(VisitorProfileDao::class);
        /**  @var RowData  $visitorData */
        $visitorData = $visitorProfileDao->getUnique( [ 'login_login_name' => self::LOGIN_NAME_PREFIX."Barbucha" ] );
        if(isset($visitorData)) {
            $visitorProfileDao->delete($visitorData);
        }
        /** @var CompanyDao $companyDao */
        $companyDao = $container->get(CompanyDao::class);
        $companyRows = $companyDao->find("name LIKE :name_like", ['name_like'=> self::COMPANY_NAME_PREFIX."%"]);
        foreach ($companyRows as $companyRow) {
            $companyDao->delete($companyRow);
            //pak smazat login
            $loginDao = $container->get(LoginDao::class);
            $LoginRows = $loginDao->find("login_name LIKE :login_name_like", ['login_name_like'=> self::LOGIN_NAME_PREFIX."%"]);
            foreach ($LoginRows as $loginRow) {
                $loginDao->delete($loginRow);
            }
        }
    }

    protected function setUp(): void {
        $this->container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(new Container())
            );
        $this->dao = $this->container->get(LoginDao::class);  // vždy nový objekt
    }

    protected function tearDown(): void {
    }

    public static function tearDownAfterClass(): void {

    }

    public function testSetUp() {
        $this->assertInstanceOf(LoginDao::class, $this->dao);
    }


    public function testInsert() {
        $rowData = new RowData();
        $rowData->offsetSet( 'login_name' , self::LOGIN_NAME_PREFIX."Barbucha" );
        $this->dao->insert($rowData);

        self::$loginNameTouple =  $this->dao->getLastInsertedPrimaryKey();
        $this->assertIsArray( self::$loginNameTouple );

        $numRows = $this->dao->getRowCount();
        $this->assertEquals(1, $numRows);
        //-------------------------------------------------

        //nova representative
        /** @var RepresentativeDao $representativeDao */
        $representativeDao = $this->container->get(RepresentativeDao::class);
        $representativeData = new RowData();
        $representativeData->import( ['login_login_name' => $rowData['login_name'], 'company_id' =>self::$companyPrimaryKey['id']] );
        $representativeDao->insert($representativeData);
        self::$representativeIdTouple  = $representativeDao->getLastInsertedPrimaryKey();


        // nova visitor_job_request
        /** @var VisitorJobRequestDao $visitorJobRequestDao */
        $visitorJobRequestDao = $this->container->get(VisitorJobRequestDao::class);
        $visitorJobRequesData = new RowData();
        $visitorJobRequesData->import( ['login_login_name' => self::$loginNameTouple ['login_name']  , 'job_id'=>self::$jobPrimaryKey['id'],
                                        'position_name' => 'sedící spící'] );
        $visitorJobRequestDao->insert($visitorJobRequesData);
        self::$visitorJobTouples  = $visitorJobRequestDao->getLastInsertedPrimaryKey();

        // nova visitor_profile
        /** @var VisitorProfileDao $visitorProfileDao */
        $visitorProfileDao = $this->container->get(VisitorProfileDao::class);
        $visitorProfileData = new RowData();
        $visitorProfileData->import( [ 'login_login_name' => self::$loginNameTouple ['login_name']   ] );
        $visitorProfileDao->insert($visitorProfileData);
        self::$visitorProfileTouples  = $visitorProfileDao->getLastInsertedPrimaryKey();

        // nova enroll - priprava potrebne propojene tabulky ...melo by byt take ...a neni udelano
    }


    public function testGetExistingRow() {
        $loginRow = $this->dao->get(self::$loginNameTouple);
        $this->assertInstanceOf(RowDataInterface::class, $loginRow);
    }

    public function test1Columns() {
        $loginRow = $this->dao->get( self::$loginNameTouple );
        $this->assertCount(1, $loginRow);
    }



    public function testUpdate_and_Exception() {
        // neupdatuje, protoze nastaven RESTRICT na tabulkach propojenych pres login_name
        // vznikne Exception
        $loginRow = $this->dao->get( ['login_name' => self::LOGIN_NAME_PREFIX."Barbucha"] );
        $this->assertIsString( $loginRow['login_name'] );

        $this->setUp();
        $nameUpdated = str_replace('bucha', 'bubuchac',$loginRow['login_name']);
        $loginRow['login_name'] = $nameUpdated;
        $this->expectException(ExecuteException::class);
        $this->dao->update($loginRow);

   }




    public function testAfterUpdate_and_Exception()   {
        // testuji, zda  login_name v login, a login_login_name ve visitor_profile  zustalo stejne
        $loginRowRereaded = $this->dao->get( self::$loginNameTouple  );

        /** @var VisitorProfileDao $visitorProfileDao */
        $visitorProfileDao = $this->container->get(VisitorProfileDao::class);
        /**  @var RowData  $row */
        $row = $visitorProfileDao->get( [ 'login_login_name' => self::$loginNameTouple ['login_name']  ] );

        $this->assertEquals( $row['login_login_name'], $loginRowRereaded['login_name'] );
    }


    public function testFind() {
        $loginRows = $this->dao->find();
        $this->assertIsArray($loginRows);
        $this->assertGreaterThanOrEqual(1, count($loginRows));
        $this->assertInstanceOf(RowDataInterface::class, $loginRows[0]);
    }


    public function testDeleteException() {
        // kontrola RESTRICT = že nevymaže login, kdyz je  pouzit v jine tabulce (yde napr. v representative,... atd.)
        /**  @var RowData  $loginRow */
        $loginRow = $this->dao->get( self::$loginNameTouple );
        $this->expectException(ExecuteException::class);
        $this->dao->delete($loginRow);
       }

    public function testDelete() {
//        throw new \Exception("A dost!");
        //napred smazat vsechny zavisle, pak smazat login
        /** @var VisitorProfileDao $visitorProfileDao */
        $visitorProfileDao = $this->container->get(VisitorProfileDao::class);
        /**  @var RowData  $visitorData */
        $visitorData = $visitorProfileDao->get( [ 'login_login_name' => self::$loginNameTouple ['login_name']  ] );
        $ok1 = $visitorProfileDao->delete($visitorData);

        /** @var CompanyDao $companyDao */
        $companyDao = $this->container->get(CompanyDao::class);
        $companyRow = $companyDao->get( ['id' => self::$companyPrimaryKey ['id']  ]);
        $ok2 = $companyDao->delete($companyRow);
        //smazani company smazalo job, company_contact, company_address, representative
        //smazani job smazalo visitor_job_request, job_to_tag

        //pak smazat login
        $this->setUp();
        $loginRow = $this->dao->get( self::$loginNameTouple );
        $this->dao->delete($loginRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        //kontrola, ze smazano
        $this->setUp();
        $loginRow = $this->dao->get( self::$loginNameTouple );
        $this->assertNull($loginRow);

   }
}

