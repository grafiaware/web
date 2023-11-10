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
    private $daoRole;   
    private static $roleTouple ;
    private static $credentialsPKTouple;
    private static $loginPKTouple;
 

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
         
        $this->daoRole = $this->container->get(RoleDao::class);  // vždy nový objekt
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
        $this->assertInstanceOf(RoleDao::class, $this->daoRole);
    }


    public function testInsert() {
        $rowDataRole = new RowData();
        $rowDataRole->offsetSet( 'role' , self::ROLE_PREFIX."Barbuch" );
        $this->daoRole->insert($rowDataRole);

        self::$roleTouple =  $this->daoRole->getLastInsertedPrimaryKey();
        $this->assertIsArray( self::$roleTouple );

        $numRows = $this->daoRole->getRowCount();
        $this->assertEquals(1, $numRows);
        //-------------------------------------------------

        //nova credentials s propojenim na insertovanou roli vyse
        /** @var CredentialsDao $credentialsDao */
        $credentialsDao = $this->container->get(CredentialsDao::class);             
        
        $credentialsData = new RowData();
        $credentialsData->import( ['role_fk' => $rowDataRole['role'], 'password_hash' => 'a' ] );
        $credentialsData->import( ['login_name_fk' => self::$loginPKTouple ['login_name'] ] );
        $credentialsDao->insert($credentialsData);
        self::$credentialsPKTouple  = $credentialsDao->getLastInsertedPrimaryKey();

    }


    public function testGetExistingRow() {
        $roleRow = $this->daoRole->get( self::$roleTouple );
        $this->assertInstanceOf(RowDataInterface::class, $roleRow);
    }

    
    public function test1Columns() {
        $roleRow = $this->daoRole->get( self::$roleTouple );
        $this->assertCount(1, $roleRow);
    }



    public function testUpdate() {
        // updatuje, je nastaven CASCADE na tabulce credentials propojene pres role_fk        
        $roleRow = $this->daoRole->get( ['role' => self::ROLE_PREFIX."Barbuch"] );
        $this->assertIsString( $roleRow['role'] );

        $this->setUp();
        $roleUpdated = str_replace('buch', 'bubuchac',$roleRow['role']);
        $roleRow['role'] = $roleUpdated;
        $ok = $this->daoRole->update($roleRow);
        $this->isTrue($ok);                                     
    }


    public function testCascadeAfterUpdate()   {
        // testuji, zda  role_fk v credentials a updatovana.role v tabulce role jsou stejne
        /** @var CredentialsDao $credentialsDao */
        $credentialsDao = $this->container->get(CredentialsDao::class);
        $credentialsRow = $credentialsDao->get(self::$credentialsPKTouple );
        
        $this->assertEquals( $credentialsRow['role_fk'], self::ROLE_PREFIX."Barbubuchac" );            
    }
       

    public function testFind_findAll() {
        $credentialsRows = $this->daoRole->find();
        $this->assertIsArray($credentialsRows);
        $this->assertGreaterThanOrEqual(1, count($credentialsRows));
        $this->assertInstanceOf(RowDataInterface::class, $credentialsRows[0]);
        
        $credentialsRows = $this->daoRole->findAll();
        $this->assertIsArray($credentialsRows);
        $this->assertGreaterThanOrEqual(1, count($credentialsRows));
        $this->assertInstanceOf(RowDataInterface::class, $credentialsRows[0]);
    }

    
    public function testDeleteException() {
        // kontrola RESTRICT = že nevymaže role, kdyz je  pouzita v  tabulce credentials
        /**  @var RowData  $loginRow */
        $roleRow = $this->daoRole->get( ['role'=> self::ROLE_PREFIX."Barbubuchac" ] );
        $this->expectException(ExecuteException::class);
        $this->daoRole->delete($roleRow);
       }

       
    public function testDelete() {
        //napred smazat vsechny zavisle {credentials}na roli a loginu, pak mazat role
        
        /** @var CredentialsDao $credentialsDao */
        $credentialsDao = $this->container->get(CredentialsDao::class);
        $credentialsRows =  $credentialsDao->find( " login_name_fk LIKE :name_like", ['name_like'=> self::ROLE_PREFIX."%"] ) ;
        foreach ($credentialsRows as $credRow) {
            $credentialsDao->delete($credRow);
        }
                
        $roleRow = $this->daoRole->get([ 'role' => self::ROLE_PREFIX."Barbubuchac" ] );
        $ok = $this->daoRole->delete($roleRow);
        $this->isTrue($ok);
                               

        //kontrola, ze smazano
        $this->setUp();
        $roleRow = $this->daoRole->get( [ 'role' => self::ROLE_PREFIX."Barbubuchac" ]);
        $this->assertNull($roleRow);
         //kontrola, ze smazano
        $this->setUp();
        $roleRow = $this->daoRole->get( [ 'role' => self::ROLE_PREFIX."Barbuch" ]);
        $this->assertNull($roleRow);

   }
    
}

