<?php
declare(strict_types=1);

use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Dao\RepresentativeDao;
use Events\Model\Dao\LoginDao;
use Events\Model\Dao\CompanyDao;

use Model\RowData\RowData;
use Model\RowData\RowDataInterface;


/**
 * Description of RepresentativeDaoTest
 *
 * @author vlse2610
 */
class RepresentativeDaoTest extends AppRunner {

    private $container;
    /**
     *
     * @var RepresentativeDao
     */
    private $dao;
    /**
     * 
     * @var CompanyDao
     */
    private  $companyDao;
    
    private static $login_login_name;
    private static $company_id;
    private static $login_login_name2;
    

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
        // nový login login_name a company_id pro TestCase
        $prefix = "RepresentativeDaoTest";
        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        do {
            $loginName = $prefix."_".uniqid();
            $loginPouzit = $loginDao->get(['login_name' => $loginName]);
        } while ($loginPouzit);
        $loginData = new RowData();
        $loginData->import(['login_name' => $loginName]);
        $loginDao->insert($loginData);
        self::$login_login_name = $loginDao->get(['login_name' => $loginName])['login_name'];
        
        do {
            $loginName = $prefix."_".uniqid();
            $loginPouzit = $loginDao->get(['login_name' => $loginName]);
        } while ($loginPouzit);
        $loginData = new RowData();
        $loginData->import(['login_name' => $loginName]);
        $loginDao->insert($loginData);
        self::$login_login_name2 = $loginDao->get(['login_name' => $loginName])['login_name'];
        
        /** @var Company $companyDao */
        $companyDao = $container->get(CompanyDao::class);
        $companyData = new RowData();
        $companyData->import(["name" => "companyProRepresentative"]);
        $companyDao->insert($companyData);  // id je autoincrement
        self::$company_id = $companyDao->lastInsertIdValue();         
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
        $this->dao = $this->container->get(RepresentativeDao::class);  // vždy nový objekt        
        $this->companyDao = $this->container->get(CompanyDao::class);
    }

    protected function tearDown(): void {                
    }   

    public static function tearDownAfterClass(): void {
        self::bootstrapBeforeClass();
        $container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(
                    (new Container(
                        )
                    )
                )
            );
                     
            //maze po sobe  vyrobene věty v tabulkach 
            /** @var LoginDao $loginDao */
            $loginDao = $container->get(LoginDao::class);   
            $loginRow = $loginDao->get( ['login_name' => self::$login_login_name2 ] ) ;
            $loginDao->delete($loginRow);
             
            $loginRow = $loginDao->get(['login_name' => self::$login_login_name ]);
            $loginDao->delete($loginRow);
            
            /** @var RepresentativeDao $representativeDao */
            $representativeDao = $container->get(RepresentativeDao::class);   
            $representativeRow = $representativeDao->get(['login_login_name' => self::$login_login_name ]);
            if ( $representativeRow ) {
                $representativeDao->delete($representativeRow);
            }
            
            /** @var CompanyDao $companyDao */
            $companyDao = $container->get(CompanyDao::class);  
            $companyRow = $companyDao->get( [ "id" => self::$company_id  ] ) ;  
            $companyDao->delete($companyRow);
    
    }

    

    public function testSetUp() {
        $this->assertIsString(self::$login_login_name);
        $this->assertIsString(self::$company_id);
        $this->assertInstanceOf(RepresentativeDao::class, $this->dao);

    }

    public function testInsert() {
        $rowData = new RowData();
        $rowData->import(['login_login_name' => self::$login_login_name, 'company_id' => self::$company_id ]);
        $this->dao->insert($rowData);
        $this->assertEquals(1, $this->dao->getRowCount());
    }

    public function testGet() {
        $representativeRow = $this->dao->get(['login_login_name' => self::$login_login_name ]);
        $this->assertInstanceOf(RowDataInterface::class, $representativeRow);
    }

    public function test2Columns() {
        $representativeRow = $this->dao->get(['login_login_name' => self::$login_login_name, 'company_id' => self::$company_id ]);
        $this->assertCount(2, $representativeRow);
    }


    public function testFindExistingRowsByCompanyId() {
        $representativeRows = $this->dao->findByCompanyIdFk(['company_id' => self::$company_id]);
        $this->assertIsArray($representativeRows);
        $this->assertGreaterThan(0, count($representativeRows));
        $this->assertInstanceOf(RowDataInterface::class, $representativeRows[0]);
    }

    public function testUpdate() {
        $representativeRow = $this->dao->get(['login_login_name' => self::$login_login_name ]);
        $loginNameId = $representativeRow['login_login_name'];
        $this->assertIsString($representativeRow['login_login_name']);
        $this->assertIsInt($representativeRow['company_id']);
        //
        $this->setUp();
        $representativeRow['login_login_name'] = self::$login_login_name2;
        $this->dao->update($representativeRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $representativeRowRereaded = $this->dao->get(['login_login_name' => self::$login_login_name2 ]);
        $this->assertInstanceOf(RowDataInterface::class, $representativeRowRereaded);
        $this->assertEquals( self::$login_login_name2, $representativeRowRereaded['login_login_name']);

    }
    

    public function testFindByCompanyIdFk() {
        $representativeRowRereaded = $this->dao->findByCompanyIdFk( ['company_id' => self::$company_id] );
        $this->assertIsArray($representativeRowRereaded);
        $this->assertGreaterThanOrEqual(1, count($representativeRowRereaded));
        $this->assertInstanceOf(RowDataInterface::class, $representativeRowRereaded[0]);
    }

    public function testFind() {
        $representativeRowaArray = $this->dao->find();
        $this->assertIsArray($representativeRowaArray);
        $this->assertGreaterThanOrEqual(1, count($representativeRowaArray));
        $this->assertInstanceOf(RowDataInterface::class, $representativeRowaArray[0]);
    }

    public function testDelete() {
        $representativeRow = $this->dao->get(['login_login_name' => self::$login_login_name2 ]);

        $this->dao->delete($representativeRow);
        $this->assertEquals(1, $this->dao->getRowCount());
      
        $this->setUp();
        $this->dao->delete($representativeRow);
        $this->assertEquals(0, $this->dao->getRowCount());

    }
}
