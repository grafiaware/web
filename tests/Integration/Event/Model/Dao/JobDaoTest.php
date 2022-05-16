<?php
declare(strict_types=1);

use Test\AppRunner\AppRunner;

use Pes\Container\Container;

use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Dao\JobDao;
use Events\Model\Dao\PozadovaneVzdelaniDao;
use Events\Model\Dao\CompanyDao;

use Model\RowData\RowData;
use Model\RowData\RowDataInterface;

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

    private static $id; //
    private static $stupen_fk;
    private static $company_id;

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
        
        // do company ulozit -- potrebuju company_id pro job
        // do pozadovane vzdelani ulozit -- potrebuji stupen  pro job 
        // v job - id je autoincrement
        /** @var CompanyDao $companyDao */
        $companyDao = $container->get(CompanyDao::class);
        $companyData = new RowData();
        $companyData->import(['eventInstitutionName30' => "chacha" ]);
        $companyData->import(['name' => "CompanyName pro JobDaoTest" ]);
        $companyDao->insert($companyData);
        self::$company_id = $companyDao->lastInsertIdValue();
        
         /** @var PozadovaneVzdelaniDao $pozadovaneVzdelaniDao */
        $pozadovaneVzdelaniDao = $container->get(PozadovaneVzdelaniDao::class);
        $pozadovaneVzdelaniData = new RowData();
        $pozadovaneVzdelaniData->import(['stupen' => "999" ]);
        $pozadovaneVzdelaniData->import(['vzdelani' => "vzdelani 999" ]);
        $pozadovaneVzdelaniDao->insert($pozadovaneVzdelaniData);      
        self::$stupen_fk = $pozadovaneVzdelaniDao->get(['stupen' => "999" ]) ['stupen'] ;      
    }

    protected function setUp(): void {
        $this->container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        $this->dao = $this->container->get(JobDao::class);  // vždy nový objekt
    }

    protected function tearDown(): void {
    }

    public static function tearDownAfterClass(): void {
         $container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(
                    (new Container(
                        )
                    )
                )
            );
                
        /** @var JobDao $jobDao */
        $jobDao = $container->get(JobDao::class);    
        $jobData = $jobDao->get(['id' => self::$id ]);
        if  ($jobData) {
            $jobDao->delete($jobData);
        }
        
        /** @var PozadovaneVzdelaniDao $pozadovaneVzdelaniDao */
        $pozadovaneVzdelaniDao = $container->get(PozadovaneVzdelaniDao::class);    
        $pozadovaneVzdelaniData = $pozadovaneVzdelaniDao->get(['stupen' => "999"]);
        $ok = $pozadovaneVzdelaniDao->delete($pozadovaneVzdelaniData);
         
        /** @var CompanyDao $companyDao */
        $companyDao = $container->get(CompanyDao::class);    
        $companyData = $companyDao->get(['id' =>  self::$company_id ]);
        $ok = $companyDao->delete($companyData);
    }

    public function testSetUp() {
        $this->assertIsNumeric(self::$company_id);
        $this->assertIsNumeric(self::$stupen_fk);
      
        $this->assertInstanceOf(JobDao::class, $this->dao);

    }
      public function testInsert() {                  
        $rowData = new RowData();
        $rowData->import( ['company_id' => self::$company_id, 'pozadovane_vzdelani_stupen' => self::$stupen_fk  ]);
        $this->dao->insert($rowData);
        
        self::$id = $this->dao->getLastInsertIdTouple();
        $this->assertIsArray(self::$id);
        $numRows = $this->dao->getRowCount();
        $this->assertEquals(1, $numRows);
    }

    public function testGetExistingRow() {
        $jobRow = $this->dao->get(self::$id);
        $this->assertInstanceOf(RowDataInterface::class, $jobRow);
    }

    
    public function test8Columns() {
        $jobRow = $this->dao->get(self::$id);
        $this->assertCount(8, $jobRow);
    }

    
    public function testUpdate() {
        $jobRow = $this->dao->get(self::$id);
        $name = $jobRow['nazev'];
        $this->assertNull($jobRow['nazev'] );
        
        $this->setUp();
        $updated = 'updated name'; //. $name;
        $jobRow['nazev'] = $updated;
        $this->dao->update($jobRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $jobRowRereaded = $this->dao->get(self::$id);
        $this->assertEquals($jobRow, $jobRowRereaded);
        $this->assertContains('updated', $jobRowRereaded['nazev']);
    }

    public function testFind() {
        $jobRow = $this->dao->find();
        $this->assertIsArray($jobRow);
        $this->assertGreaterThanOrEqual(1, count($jobRow));
        $this->assertInstanceOf(RowDataInterface::class, $jobRow[0]);
    }

    public function testDelete() {
        $jobRow = $this->dao->get(self::$id);
        $this->dao->delete($jobRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $companyRow = $this->dao->get(self::$id);
        $this->assertNull($companyRow);
    }
}
