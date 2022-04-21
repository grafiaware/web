<?php
declare(strict_types=1);


use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Model\RowData\RowData;
use Model\RowData\RowDataInterface;

use Model\Dao\Exception\DaoKeyVerificationFailedException;
use Events\Model\Dao\CompanyDao;
use Events\Model\Dao\CompanyAddressDao;
use Events\Model\Entity\CompanyAddress;

/**
 * 
 */
class CompanyAddressTest  extends AppRunner {

    private $container;

    /**
     *
     * @var CompanyAddressDao
     */
    private $dao;
    
//            `company_address`.`company_id`,  
//            `company_address`.`name`,
//            `company_address`.`lokace`,
//            `company_address`.`psc`,
//            `company_address`.`obec` " 
    
    
    private static $company_company_id_fk;
    private static $company_company_id_fk2;

    

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
        
 
        // nova company 
        /** @var CompanyDao $companyDao */
        $companyDao = $container->get(CompanyDao::class);      
//        do {
//            $idCompany = uniqid();
//            $company = $companyDao->get($idCompany);
//        } while ($company);
//        
        $rowData = new RowData();
        $rowData->offsetSet('name', "Company-nameNNN");        
        $rowData->offsetSet('eventInstitutionName30', null);
        $companyDao->insert($rowData);
        self::$company_company_id_fk =  $companyDao->getLastInsertId();
        
        $rowData = new RowData();
        $rowData->offsetSet('name', "Company-nameNNN");        
        $rowData->offsetSet('eventInstitutionName30', null);
        $companyDao->insert($rowData);
        self::$company_company_id_fk2 =  $companyDao->getLastInsertId();

//        /** @var EventDao $eventDao */
//        $eventDao = $container->get(EventDao::class);
//        $eventData = new RowData(["published" => 1]);
//        $eventDao->insert($eventData);  // id je autoincrement
//        self::$event_id_fk = $eventDao->getLastInsertId();
//        $eventData = new RowData(["published" => 1]);
//        $eventDao->insert($eventData);  // id je autoincrement
//        self::$event_id_fk_2 = $eventDao->getLastInsertId();
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
        $this->dao = $this->container->get(CompanyAddressDao::class);  // vždy nový objekt
    }

    protected function tearDown(): void {
    }

    public static function tearDownAfterClass(): void {

    }

    public function testSetUp() {
        $this->assertInstanceOf(CompanyAddressDao::class, $this->dao);

    }

    public function testInsert() {
        $rowData = new RowData(
               ['company_id' => self::$company_company_id_fk, 
                'name' => 'VelkaFirma',
                'lokace' => 'Mars ',
                'psc' => '02020' ] );
        $this->dao->insert($rowData);
        $this->assertEquals(1, $this->dao->getRowCount());
    }

    public function testGetExistingRow() {
        $companyAddressRow = $this->dao->get(self::$company_company_id_fk);
        $this->assertInstanceOf(RowDataInterface::class, $companyAddressRow);
    }

    public function test5Columns() {
        $companyAddressRow = $this->dao->get(self::$company_company_id_fk);
        $this->assertCount(5, $companyAddressRow);
    }

    public function testUpdate() {
        $companyAddressRow = $this->dao->get(self::$company_company_id_fk);
        $companyAddressId = $companyAddressRow['company_id'];
        $this->assertIsInt($companyAddressRow['company_id']);
        //
        $this->setUp(); //nove dao
        $companyAddressRow['name'] = "mala firma";
        $this->dao->update($companyAddressRow);
        $this->assertEquals(1, $this->dao->getRowCount());
        
        $this->setUp();
        $rowRereaded = $this->dao->get(self::$company_company_id_fk);
        $this->assertEquals("mala firma", $rowRereaded['name']);

        
        $this->setUp(); //nove dao
        $companyAddressRow['company_id'] = self::$company_company_id_fk2;
        $this->dao->update($companyAddressRow);
        $this->assertEquals(1, $this->dao->getRowCount());
        
 
    }
//
//    public function testFind() {
//        $enrollRowsArray = $this->dao->find();
//        $this->assertIsArray($enrollRowsArray);
//        $this->assertGreaterThanOrEqual(1, count($enrollRowsArray));
//        $this->assertInstanceOf(RowDataInterface::class, $enrollRowsArray[0]);
//    }
//
//    public function testDelete() {
//        $enrollRow = $this->dao->get(self::$login_login_name_fk);
//
//        $this->dao->delete($enrollRow);
//        $this->assertEquals(1, $this->dao->getRowCount());
//
//        $this->setUp();
//        $this->dao->delete($enrollRow);
//        $this->assertEquals(0, $this->dao->getRowCount());
//
//    }
}
