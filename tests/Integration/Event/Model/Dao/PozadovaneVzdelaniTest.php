<?php
declare(strict_types=1);

use Test\AppRunner\AppRunner;

use Pes\Container\Container;
use Test\Integration\Event\Container\EventsContainerConfigurator;
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
class  PozadovaneVzdelaniTest extends AppRunner {
    private $container;

    /**
     *
     * @var PozadovaneVzdelaniDao
     */
    private $dao;

    private static $pozadovaneVzdelaniTouple;

    private static $jobIdTouple;   
    private static $companyIdTouple;



    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        $container =
            (new EventsContainerConfigurator())->configure(
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
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        $this->dao = $this->container->get(PozadovaneVzdelaniDao::class);  // vždy nový objekt
    }

    protected function tearDown(): void {
    }

    public static function tearDownAfterClass(): void {

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
        $pozadovaneVzdelaniRow = $this->dao->get( self::$pozadovaneVzdelaniTouple );
        $stupen = $pozadovaneVzdelaniRow['stupen'];
        $this->assertIsInt( $pozadovaneVzdelaniRow['stupen'] );
        $vzdelani = $pozadovaneVzdelaniRow['vzdelani'];
        $this->assertIsString( $pozadovaneVzdelaniRow['vzdelani'] );
         
        
        $this->setUp();
        /** @var RowData $pozadovaneVzdelaniRow1 */       
        $pozadovaneVzdelaniRow1 - new RowData();
        $pozadovaneVzdelaniRow1->offsetSet( 'vzdelani' , "BBBBB"  );
        $pozadovaneVzdelaniRow1->offsetSet( 'stupen' , "200"  );

        $this->dao->update($pozadovaneVzdelaniRow1);
        $this->assertEquals(1, $this->dao->getRowCount());
        
        //kontrola v job - je CASCADE
         /** @var JobDao $jobDao */
        $jobDao = $this->container->get(JobDao::class);
        $jobData = $jobDao->get( self::$jobIdTouple );
        $this->assertEquals( $pozadovaneVzdelaniRow1['stupen']  ,$jobData['stupen'] );


        
//        $rowArray = $jobTagRow->getArrayCopy();
//        self::$jobTagTouple_poUpdate =  $this->dao->getPrimaryKeyTouples($rowArray);  
//
//        $this->setUp();
//        $a = [ 'job_tag_tag' => self::$jobTagTouple_poUpdate ['tag'] , 'job_id'=>self::$jobIdTouple['id'] ] ;
//        $jobTagRowRereaded = $this->dao->get(  self::$jobTagTouple_poUpdate   );
//        $this->assertEquals($jobTagRow, $jobTagRowRereaded);
//        $this->assertStringContainsString('kiki', $jobTagRowRereaded['tag']);  
//        
//        //kontrola CASCADE u update
//        //kontrola, ze v job_to_zag je taky updatovany tag
//        /**  @var JobToTagDao  $jobToTagDao */
//        $jobToTagDao = $this->container->get(JobToTagDao::class);
//        $jobToTagRow = $jobToTagDao->get( [ 'job_tag_tag' => self::$jobTagTouple_poUpdate ['tag']  , 'job_id'=>self::$jobIdTouple['id'] ] );
//        $this->assertEquals( self::$jobTagTouple_poUpdate ['tag'], $jobToTagRow['job_tag_tag'] );
        
    }

    
    public function testFind() {
        $pozadovaneVzdelaniRow = $this->dao->find();
        $this->assertIsArray($pozadovaneVzdelaniRow);
        $this->assertGreaterThanOrEqual(1, count($pozadovaneVzdelaniRow));
        $this->assertInstanceOf(RowDataInterface::class, $pozadovaneVzdelaniRow[0]);
    }

   
    public function testDeleteException() {   
        // kontrola RESTRICT = že nevymaže pozadovane vzdelani, kdyz je  pouzit v job
        $pozadovaneVzdelaniRow = $this->dao->get(self::$pozadovaneVzdelaniTouple);
        $this->expectException(ExecuteException::class);
        $this->dao->delete($pozadovaneVzdelaniRow);                
    }
    
//    public function testDelete() {
//        //delete Company - amze job, jobToTag
//        /** @var CompanyDao $companyDao */
//        $companyDao = $this->container->get(CompanyDao::class);    
//        $companyData = $companyDao->get(self::$companyIdTouple );
//        $ok = $companyDao->delete($companyData);              
//                
//        //pak smazat jobTag  
//        $this->setUp();
//        $jobTagRow = $this->dao->get(self::$jobTagTouple_poUpdate);
//        $this->dao->delete($jobTagRow);
//        $this->assertEquals(1, $this->dao->getRowCount());
//
//        //kontrola, ze smazano
//        $this->setUp();
//        $jobTagRow = $this->dao->get(self::$jobTagTouple_poUpdate);
//        $this->assertNull($jobTagRow);
//         
//   }
}


