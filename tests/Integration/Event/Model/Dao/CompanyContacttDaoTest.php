<?php
declare(strict_types=1);

use Test\AppRunner\AppRunner;
use Pes\Container\Container;

use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Dao\CompanyContactDao;
use Events\Model\Dao\CompanyDao;
use Model\Dao\Exception\DaoForbiddenOperationException;
use Model\Dao\Exception\DaoKeyVerificationFailedException;
use Model\RowData\RowData;
use Model\RowData\RowDataInterface;

/**
 * 
 */
class CompanyContactDaoTest extends AppRunner {


    private $container;

    /**
     *
     * @var CompanyContactDao
     */
    private $dao;

    private static $company_company_id_fk;
    private static $id;

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
     
        $rowData = new RowData();
        $rowData->offsetSet('name', "Company-nameNNN");        
        $rowData->offsetSet('eventInstitutionName30', null);
        $companyDao->insert($rowData);
        self::$company_company_id_fk =  $companyDao->getLastInsertIdTouple();
        
        
    }

    protected function setUp(): void {
        $this->container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        $this->dao = $this->container->get(CompanyContactDao::class);  // vždy nový objekt
    }

    protected function tearDown(): void {
    }

    public static function tearDownAfterClass(): void {

    }

    
    public function testSetUp() {
        $this->assertInstanceOf(CompanyContactDao::class, $this->dao);

    }
//    public function testInsert() {
//        $rowData = new RowData();
//        
////$rowData->offsetSet('company_id', "testEvenao-title");        
//        $rowData->offsetSet('name', null);
//        $rowData->offsetSet('phones', null);
//        $rowData->offsetSet('mobiles', null);
//        $rowData->offsetSet('emails', null);
// 
//        $this->dao->insert($rowData);
//        self::$id =  $this->dao->getLastInsertId();
//        $this->assertGreaterThan(0, (int) self::$id);
//        $numRows = $this->dao->getRowCount();
//        $this->assertEquals(1, $numRows);
//    }
//
//    public function testGetExistingRow() {
//        $companyContactRow = $this->dao->get(self::$id);
//        $this->assertInstanceOf(RowDataInterface::class, $companyContactRow);
//    }
//
//    public function test6Columns() {
//        $companyContactRow = $this->dao->get(self::$id);
//        $this->assertCount(6, $companyContactRow);
//    }

//    public function testUpdate() {
//        $companyContactRow = $this->dao->get(self::$id);
//        $name = $companyContactRow['mk'];
//        $this->assertIsString($companyContactRow['title']);
//        //
//        $this->setUp();
//        $updated = str_replace('-title', '-title_updated', $name);
//        $eventContentTypeRow['title'] = $updated;
//        $this->dao->update($eventContentTypeRow);
//        $this->assertEquals(1, $this->dao->getRowCount());
//
//        $this->setUp();
//        $eventContentTypeRowRereaded = $this->dao->get(self::$id);
//        $this->assertEquals($eventContentTypeRow, $eventContentTypeRowRereaded);
//        $this->assertContains('-title_updated', $eventContentTypeRowRereaded['title']);
//    }
//
//    public function testFind() {
//        $eventContentTypeRow = $this->dao->find();
//        $this->assertIsArray($eventContentTypeRow);
//        $this->assertGreaterThanOrEqual(1, count($eventContentTypeRow));
//        $this->assertInstanceOf(RowDataInterface::class, $eventContentTypeRow[0]);
//    }
//
//    public function testDelete() {
//        $eventContentTypeRow = $this->dao->get(self::$id);
//        $this->dao->delete($eventContentTypeRow);
//        $this->assertEquals(1, $this->dao->getRowCount());
//
//        $this->setUp();
//        $eventContentTypeRow = $this->dao->get(self::$id);
//        $this->assertNull($eventContentTypeRow);
//    }
}
