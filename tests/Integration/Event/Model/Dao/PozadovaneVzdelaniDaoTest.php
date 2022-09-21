<?php
declare(strict_types=1);
namespace Test\Integration\Dao;

use Test\AppRunner\AppRunner;

use Pes\Container\Container;
use Test\Integration\Event\Container\EventsModelContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Dao\JobDao;
use Events\Model\Dao\CompanyDao;
use Events\Model\Dao\PozadovaneVzdelaniDao;

use Model\RowData\RowData;
use Model\RowData\RowDataInterface;
use Pes\Database\Statement\Exception\ExecuteException;

/**
 * Description of PozadovaneVzdelaniTest
 *
 * @author vlse2610
 */
class  PozadovaneVzdelaniDaoTest extends AppRunner {
    private $container;
    /**
     *
     * @var PozadovaneVzdelaniDao
     */
    private $dao;

    private static $pozadovaneVzdelaniTouple;
    private static $pozadovaneVzdelaniTouple_poUpdate;
    private static $jobIdTouple;   
    private static $companyIdTouple;


    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );        
        /** @var CompanyDao $companyDao */
        $companyDao = $container->get(CompanyDao::class);    
        $companyData = new RowData();
        $companyData->offsetSet( 'name' , "pomocna pro PozadovaneVzdelaniTest"  );
        $companyDao->insert($companyData);
        self::$companyIdTouple = $companyDao->getLastInsertIdTouple();                                
    }

    
    protected function setUp(): void {
        
        $this->container =
            (new EventsModelContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        $this->dao = $this->container->get(PozadovaneVzdelaniDao::class);  // vždy nový objekt
    }

    protected function tearDown(): void {
    }

    public static function tearDownAfterClass(): void {
        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
         /** @var CompanyDao $companyDao */
        $companyDao = $container->get(CompanyDao::class);            
        $companyData = $companyDao->get(self::$companyIdTouple);
        $companyDao->delete($companyData);

    }

    public function testSetUp() {
        $this->assertInstanceOf(PozadovaneVzdelaniDao::class, $this->dao);
    }
    
     
    public function testInsert() {  
        $rowData = new RowData();
        $rowData->offsetSet( 'stupen' , "100"  );
        $rowData->offsetSet( 'vzdelani' , "AAAAA"  );
        $this->dao->insert($rowData);       
        /** @var RowData $rowD */
        $rowD =  $this->dao->get( ['stupen' => "100"] );
       
    
        $rowArray = $rowD->getArrayCopy();
        self::$pozadovaneVzdelaniTouple =  $this->dao->getPrimaryKeyTouples($rowArray);        
        $this->assertIsArray(self::$pozadovaneVzdelaniTouple);
                
        $numRows = $this->dao->getRowCount();
        $this->assertEquals(1, $numRows);
                
        
        //vyrobit job vetu
        /** @var JobDao $jobDao */
        $jobDao = $this->container->get(JobDao::class);
        $jobData = new RowData();
        $jobData->import( ['pozadovane_vzdelani_stupen' => 100, 'company_id' =>self::$companyIdTouple['id']] );
        $jobDao->insert($jobData);    
        self::$jobIdTouple = $jobDao->getLastInsertIdTouple();        
   }

   
    public function testGetExistingRow() {
        $pozadovaneVzdelaniRow = $this->dao->get(self::$pozadovaneVzdelaniTouple);
        $this->assertInstanceOf(RowDataInterface::class, $pozadovaneVzdelaniRow);
    }

    
    public function test2Columns() {
        $pozadovaneVzdelaniRow = $this->dao->get( self::$pozadovaneVzdelaniTouple );
        $this->assertCount(2, $pozadovaneVzdelaniRow);
    }

    
    
   public function testUpdate() {
        /** @var RowData $pozadovaneVzdelaniRow */   
        $pozadovaneVzdelaniRow = $this->dao->get( self::$pozadovaneVzdelaniTouple );
        $this->assertIsInt( $pozadovaneVzdelaniRow['stupen'] );
        $this->assertIsString( $pozadovaneVzdelaniRow['vzdelani'] );
                 
        $this->setUp();                      
        $pozadovaneVzdelaniRow->offsetSet( 'vzdelani' , "BBBBB"  );
        $pozadovaneVzdelaniRow->offsetSet( 'stupen' , "200"  );

        $ok = $this->dao->update($pozadovaneVzdelaniRow);
        $this->assertEquals(1, $this->dao->getRowCount());
        
        //--------------------------------------------------------
        //kontrola stupen v job - je CASCADE
         /** @var JobDao $jobDao */
        $jobDao = $this->container->get(JobDao::class);
        $jobData = $jobDao->get( self::$jobIdTouple );
        $this->assertEquals( $pozadovaneVzdelaniRow['stupen'], $jobData['pozadovane_vzdelani_stupen'] );

        /** @var RowData $rowD */
        $rowD =  $this->dao->get( ['stupen' => "200"] );           
        $rowArray = $rowD->getArrayCopy();
        self::$pozadovaneVzdelaniTouple_poUpdate =  $this->dao->getPrimaryKeyTouples($rowArray);         
    }

    
    public function testFind() {
        $pozadovaneVzdelaniRow = $this->dao->find();
        $this->assertIsArray($pozadovaneVzdelaniRow);
        $this->assertGreaterThanOrEqual(1, count($pozadovaneVzdelaniRow));
        $this->assertInstanceOf(RowDataInterface::class, $pozadovaneVzdelaniRow[0]);
    }

   
    public function testDeleteException() {   
        // kontrola RESTRICT = že nevymaže pozadovane vzdelani, kdyz je pouzit v job
        $pozadovaneVzdelaniRow = $this->dao->get(self::$pozadovaneVzdelaniTouple_poUpdate);
        $this->expectException(ExecuteException::class);
        $this->dao->delete($pozadovaneVzdelaniRow);                
    }
    
    
    public function testDelete() {
      
        //smazat job, kde pouzito pozadovane_vzdelani_stupen       
        /** @varJobDao $jobDao */
        $jobDao = $this->container->get(JobDao::class);            
        /** @var RowData $jobRow */
        $jobRow = $jobDao->get(self::$jobIdTouple);
        $ok = $jobDao->delete( $jobRow );
        $this->assertEquals(1, $jobDao->getRowCount());
                
        //pak smazat pozadovane vzdelani 
        $this->setUp();
        $pozadovaneVzdelaniRow = $this->dao->get(self::$pozadovaneVzdelaniTouple_poUpdate);
        $this->dao->delete($pozadovaneVzdelaniRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        //kontrola, ze smazano
        $this->setUp();
        $pozadovaneVzdelaniRow = $this->dao->get(self::$pozadovaneVzdelaniTouple_poUpdate);
        $this->assertNull($pozadovaneVzdelaniRow);
        
   }
}


