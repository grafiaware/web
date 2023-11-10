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
    private static $credentialsPKTouple;
    private static $loginPKTouple;
    
    
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
        self::$loginPKTouple = $loginDao->getLastInsertedPrimaryKey();           

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
               
        
        /** @var CredentialsDao $credentialsDao */
        $credentialsDao = $container->get(CredentialsDao::class);
        $credentialsRows =  $credentialsDao->find( " login_name_fk LIKE :name_like", ['name_like'=> self::ROLE_PREFIX."%"] ) ;
        foreach ($credentialsRows as $credRow) {
            $credentialsDao->delete($credRow);
        }
        
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
        
        
        
        
        /** @var CredentialsDao $credentialsDao */
        $credentialsDao = $container->get(CredentialsDao::class);
        $credentialsRows =  $credentialsDao->find( " login_name_fk LIKE :name_like", ['name_like'=> self::ROLE_PREFIX."%"] ) ;
        foreach ($credentialsRows as $credRow) {
            $credentialsDao->delete($credRow);
        }
        
        $roleDao = $container->get(RoleDao::class);
        $roleRows = $roleDao->find(" role LIKE :role_like", ['role_like'=> self::ROLE_PREFIX."%"]);
        foreach ($roleRows as $roleRow) {
                $roleDao->delete($roleRow);
        }
               
        //este smazat login
        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        $loginData = $loginDao->get( [ 'login_name' => self::ROLE_PREFIX."Login1" ] );
        if (isset($loginData) )  {
                $loginDao->delete($loginData);
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

        //nova credentials s propojenim na insertovanou roli vyse
        /** @var CredentialsDao $credentialsDao */
        $credentialsDao = $this->container->get(CredentialsDao::class);
       
//        $credRow = $credentialsDao->get(self::$credentialsPKloginNameTouple );
//        $credRow->import(['role_fk' => self::$roleTouple [role_fk] ]);
//        $credentialsDao->update($credRow);
//        
        
        $credentialsData = new RowData();
        $credentialsData->import( ['role_fk' => $rowDataRole['role'], 'password_hash' => 'a' ] );
        $credentialsData->import( ['login_name_fk' => self::$loginPKTouple ['login_name'] ] );
        $credentialsDao->insert($credentialsData);
        self::$credentialsPKTouple  = $credentialsDao->getLastInsertedPrimaryKey();

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
        $roleRow = $this->roleDAO->get( ['role' => self::ROLE_PREFIX."Barbuch"] );
        $this->assertIsString( $roleRow['role'] );

        $this->setUp();
        $roleUpdated = str_replace('buch', 'bubuchac',$roleRow['role']);
        $roleRow['role'] = $roleUpdated;
        $ok = $this->roleDAO->update($roleRow);
        $this->isTrue($ok);                                     
    }


    public function testCascadeAfterUpdate()   {
        // testuji, zda  role_fk v credentials a updatovana.role v tabulce role jsou stejne
        /** @var CredentialsDao $credentialsDao */
        $credentialsDao = $this->container->get(CredentialsDao::class);
        $credentialsRow = $credentialsDao->get(self::$credentialsPKTouple );
        
        $this->assertEquals( $credentialsRow['role_fk'], self::ROLE_PREFIX."Barbubuchac" );
            
    }
       

    public function testFind() {
        $credentialsRows = $this->roleDAO->find();
        $this->assertIsArray($credentialsRows);
        $this->assertGreaterThanOrEqual(1, count($credentialsRows));
        $this->assertInstanceOf(RowDataInterface::class, $credentialsRows[0]);
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

