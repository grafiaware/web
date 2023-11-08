<?php
declare(strict_types=1);
namespace Test\Integration\Dao;

use Test\AppRunner\AppRunner;

use Pes\Container\Container;
use Container\AuthContainerConfigurator;
use Container\DbOldContainerConfigurator;
//use Test\Integration\Event\Container\TestDbEventsContainerConfigurator;

use Auth\Model\Dao\RoleDao;
use Auth\Model\Dao\CredentialsDao;
use Auth\Model\Dao\LoginDao;

use Model\RowData\RowData;
use Model\RowData\RowDataInterface;
use Pes\Database\Statement\Exception\ExecuteException;

/**
 * Description of RoleDaoTest
 *
 * @author vlse2610
 */
class RoleDaoTest extends AppRunner {

    const ROLE_PREFIX = "ROLEDaoTest_";

    private $container;
    /**
     *
     * @var RoleDao
     */
    private $roleDAO;

    
    private static $roleTouple ;
    private static $credentialsPKloginNameTouple;
    
    
//    private static $companyPrimaryKey;
//    private static $jobPrimaryKey;
//    private static $visitorJobTouples;
//    private static $visitorProfileTouples;
//    private static $representativeIdTouple;


    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        self::removeRecords();
        
//        $container =
//            (new AuthContainerConfigurator())->configure(
//                (new TestDbEventsContainerConfigurator())->configure(new Container())
//            );
        
         $container =
            (new AuthContainerConfigurator())->configure(
                (new DbOldContainerConfigurator())->configure(
                    (new Container(
            //         $this->getApp()->getAppContainer()       bez app kontejneru
                        )
                    )
                )
            );

        
        //nove Login
        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        $rowData = new RowData();
        $rowData->offsetSet('login_name', self::ROLE_PREFIX."Login1");
        $loginDao->insert($rowData);   
         
                

        // nova credentials - priprava potrebne propojene tabulky
        /** @var CredentialsDao $credentialsDao */
        $credentialsDao = $container->get(CredentialsDao::class);
        $rowData = new RowData();
        $rowData->offsetSet('login_name_fk', self::ROLE_PREFIX."Login" );
        
        xcxcb
        $rowData->offsetSet('eventInstitutionName30', 'ShortyCo.');
        $credentialsDao->insert($rowData);
        self::$companyPrimaryKey =  $companyDao->getLastInsertedPrimaryKey();

//        // nova job - priprava potrebne propojene tabulky
//         /** @var JobDao $jobDao */
//        $jobDao = $container->get(JobDao::class);
//        $jobData = new RowData();
//        $jobData->import([ 'company_id' => self::$companyPrimaryKey['id'],
//                           'pozadovane_vzdelani_stupen' => 1
//                         ]);
//        $ok = $jobDao->insert($jobData);
//        self::$jobPrimaryKey = $jobDao->getLastInsertedPrimaryKey();
    }

    
    private static function removeRecords() {
//        $container =
//            (new EventsModelContainerConfigurator())->configure(
//                (new TestDbEventsContainerConfigurator())->configure(new Container())
//            );
        $container =
            (new AuthContainerConfigurator())->configure(
                (new DbOldContainerConfigurator())->configure(
                    (new Container(
                    //   $this->getApp()->getAppContainer()       bez app kontejneru
                        )
                    )
                )
            );
        
//        //napred smazat vsechny zavisle, pak smazat login
//        /** @var VisitorProfileDao $visitorProfileDao */
//        $visitorProfileDao = $container->get(VisitorProfileDao::class);
//        /**  @var RowData  $visitorData */
//        $visitorData = $visitorProfileDao->getUnique( [ 'login_login_name' => self::LOGIN_NAME_PREFIX."Barbucha" ] );
//        if(isset($visitorData)) {
//            $visitorProfileDao->delete($visitorData);
//        }
//        /** @var CompanyDao $companyDao */
//        $companyDao = $container->get(CompanyDao::class);
//        $companyRows = $companyDao->find("name LIKE :name_like", ['name_like'=> self::COMPANY_NAME_PREFIX."%"]);
//        foreach ($companyRows as $companyRow) {
//            $companyDao->delete($companyRow);
        
        /** @var RoleDao $roleDao */
        $roleDao = $container->get(RoleDao::class);
        $roleRows = $roleDao->find(" role LIKE :role_like", ['role_like'=> self::ROLE_PREFIX."%"]);
        foreach ($roleRows as $roleRow) {
            $roleDao->delete($roleRow);
        }
        
        
        //pak smazat login
        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        $loginData = $loginDao->get( [ 'login_name' => self::ROLE_PREFIX."Login1" ] );
        if (isset($loginData) )  {
                $loginDao->delete($loginData);
            }

        }

    
    protected function setUp(): void {
//        $this->container =
//            (new EventsModelContainerConfigurator())->configure(
//                (new TestDbEventsContainerConfigurator())->configure(new Container())
//            );
         $this->container =
            (new AuthContainerConfigurator())->configure(
                (new DbOldContainerConfigurator())->configure(
                    (new Container(
            //         $this->getApp()->getAppContainer()       bez app kontejneru
                        )
                    )
                )
            );
         
        $this->roleDAO = $this->container->get(RoleDao::class);  // vždy nový objekt
    }

    protected function tearDown(): void {
    }

    public static function tearDownAfterClass(): void {
        
        $container =
            (new AuthContainerConfigurator())->configure(
                (new DbOldContainerConfigurator())->configure(
                    (new Container(
                    //   $this->getApp()->getAppContainer()       bez app kontejneru
                        )
                    )
                )
            );        
        $roleDao = $container->get(RoleDao::class);
            $roleRows = $roleDao->find(" role LIKE :role_like", ['role_like'=> self::ROLE_PREFIX."%"]);
            foreach ($roleRows as $roleRow) {
                $roleDao->delete($roleRow);
            }

    }

    public function testSetUp() {
        $this->assertInstanceOf(RoleDao::class, $this->roleDAO);
    }


   public function testInsert() {
        $rowDataRole = new RowData();
        $rowDataRole->offsetSet( 'role' , self::ROLE_PREFIX."Barbuch" );
        $this->roleDAO->insert($rowDataRole);

        self::$roleTouple =  $this->roleDAO->getLastInsertedPrimaryKey();
        $this->assertIsArray( self::$roleTouple );

        $numRows = $this->roleDAO->getRowCount();
        $this->assertEquals(1, $numRows);
        //-------------------------------------------------

        //nova credentials s insertovanou roli vyse
        /** @var CredentialsDao $credentialsDao */
        $credentialsDao = $this->container->get(CredentialsDao::class);
        $credentialsData = new RowData();
        $credentialsData->import( ['role_fk' => $rowDataRole['role'], 'password_hash' => 'a' ] );
        $credentialsDao->insert($credentialsData);
        self::$credentialsPKloginNameTouple  = $credentialsDao->getLastInsertedPrimaryKey();
//
//
//        // nova visitor_job_request
//        /** @var VisitorJobRequestDao $visitorJobRequestDao */
//        $visitorJobRequestDao = $this->container->get(VisitorJobRequestDao::class);
//        $visitorJobRequesData = new RowData();
//        $visitorJobRequesData->import( ['login_login_name' => self::$loginNameTouple ['login_name']  , 'job_id'=>self::$jobPrimaryKey['id'],
//                                        'position_name' => 'sedící spící'] );
//        $visitorJobRequestDao->insert($visitorJobRequesData);
//        self::$visitorJobTouples  = $visitorJobRequestDao->getLastInsertedPrimaryKey();
//
//        // nova visitor_profile
//        /** @var VisitorProfileDao $visitorProfileDao */
//        $visitorProfileDao = $this->container->get(VisitorProfileDao::class);
//        $visitorProfileData = new RowData();
//        $visitorProfileData->import( [ 'login_login_name' => self::$loginNameTouple ['login_name']   ] );
//        $visitorProfileDao->insert($visitorProfileData);
//        self::$visitorProfileTouples  = $visitorProfileDao->getLastInsertedPrimaryKey();
//
//        // nova enroll - priprava potrebne propojene tabulky ...melo by byt take ...a neni udelano
   }


    public function testGetExistingRow() {
        $roleRow = $this->roleDAO->get( self::$roleTouple );
        $this->assertInstanceOf(RowDataInterface::class, $roleRow);
    }

    public function test1Columns() {
        $roleRow = $this->roleDAO->get( self::$roleTouple );
        $this->assertCount(1, $roleRow);
    }



    public function testUpdate() {
        // updatuje, je nastaven CASCADE na tabulce credentials propojene pres role_fk        
        $roleRow = $this->dao->get( ['role_fk' => self::ROLE_PREFIX."Barbuch"] );
        $this->assertIsString( $roleRow['role_fk'] );

        $this->setUp();
        $roleUpdated = str_replace('buch', 'bubuchac',$loginRow['role_fk']);
        $roleRow['role_fk'] = $roleUpdated;
        $ok = $this->roleDAO->update($roleRow);
        $this->isTrue($ok);
                        
         //$this->setUp();
         
    }


    public function testCascadeAfterUpdate()   {
        // testuji, zda  role_fk v credentials a updatovana.role v tabulce role jsou stejne
         /** @var CredentialsDao $credentialsDao */
        $credentialsDao = $this->container->get(CredentialsDao::class);
        $credentialsRow = $credentialsDao->get(self::$credentialsPKloginNameTouple );
        
        $this->assertEquals( $credentialsRow['role_fk'], self::ROLE_PREFIX."Barbubuchac" );
            
    }
       

    public function testFind() {
        $credentialsRows = $this->roleDAO->find();
        $this->assertIsArray($credentialsRows);
        $this->assertGreaterThanOrEqual(1, count($credentialsRows));
        $this->assertInstanceOf(CredentialsDao::class, $credentialsRows[0]);
    }

    
//    public function testDeleteException() {
//        // kontrola RESTRICT = že nevymaže login, kdyz je  pouzit v jine tabulce (yde napr. v representative,... atd.)
//        /**  @var RowData  $loginRow */
//        $loginRow = $this->dao->get( self::$loginNameTouple );
//        $this->expectException(ExecuteException::class);
//        $this->dao->delete($loginRow);
//       }
//
//    public function testDelete() {
////        throw new \Exception("A dost!");
//        //napred smazat vsechny zavisle, pak smazat login
//        /** @var VisitorProfileDao $visitorProfileDao */
//        $visitorProfileDao = $this->container->get(VisitorProfileDao::class);
//        /**  @var RowData  $visitorData */
//        $visitorData = $visitorProfileDao->get( [ 'login_login_name' => self::$loginNameTouple ['login_name']  ] );
//        $ok1 = $visitorProfileDao->delete($visitorData);
//
//        /** @var CompanyDao $companyDao */
//        $companyDao = $this->container->get(CompanyDao::class);
//        $companyRow = $companyDao->get( ['id' => self::$companyPrimaryKey ['id']  ]);
//        $ok2 = $companyDao->delete($companyRow);
//        //smazani company smazalo job, company_contact, company_address, representative
//        //smazani job smazalo visitor_job_request, job_to_tag
//
//        //pak smazat login
//        $this->setUp();
//        $loginRow = $this->dao->get( self::$loginNameTouple );
//        $this->dao->delete($loginRow);
//        $this->assertEquals(1, $this->dao->getRowCount());
//
//        //kontrola, ze smazano
//        $this->setUp();
//        $loginRow = $this->dao->get( self::$loginNameTouple );
//        $this->assertNull($loginRow);
//
//   }
    
}

