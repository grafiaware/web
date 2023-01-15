<?php
declare(strict_types=1);
namespace Test\Integration\Dao;

use Test\AppRunner\AppRunner;

use Pes\Container\Container;
use Container\EventsModelContainerConfigurator;
use Test\Integration\Event\Container\TestDbEventsContainerConfigurator;

use Events\Model\Dao\JobDao;
use Events\Model\Dao\JobTagDao;
use Events\Model\Dao\JobToTagDao;
use Events\Model\Dao\CompanyDao;

use Model\RowData\RowData;
use Model\RowData\RowDataInterface;
use Pes\Database\Statement\Exception\ExecuteException;

/**
 * Description of JobTagDaoTest
 *
 * @author vlse2610
 */
class  JobTagDaoTest extends AppRunner {
    private $container;
    /**
     *
     * @var JobTagDao
     */
    private $dao;

    private static $jobIdTouple;
    private static $jobTagTouple;
    private static $jobTagTouple_poUpdate;
    private static $jobToTagTouples;
    private static $companyIdTouple;



    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(new Container())
            );
        
        /** @var CompanyDao $companyDao */
        $companyDao = $container->get(CompanyDao::class);    
        $companyData = new RowData();
        $companyData->offsetSet( 'name' , "pomocna pro jobTagDaoTest"  );
        $companyDao->insert($companyData);
        self::$companyIdTouple = $companyDao->getLastInsertIdTouple();
                
    }

    protected function setUp(): void {
        $this->container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(new Container())
            );
        $this->dao = $this->container->get(JobTagDao::class);  // vždy nový objekt
    }

    protected function tearDown(): void {
    }

    public static function tearDownAfterClass(): void {

    }

    public function testSetUp() {
        $this->assertInstanceOf(JobTagDao::class, $this->dao);
    }
    
     
    public function testInsert() {  
        $rowData = new RowData();
        $rowData->offsetSet( 'tag' , "vesmír a okolí"  );
        $this->dao->insert($rowData);        
        $rowD =  $this->dao->get( ['tag' => "vesmír a okolí"] );
       
        /** @var RowData $rowD */
        $rowArray = $rowD->getArrayCopy();
        self::$jobTagTouple =  $this->dao->getPrimaryKey($rowArray);        
        $this->assertIsArray(self::$jobTagTouple);
                
        $numRows = $this->dao->getRowCount();
        $this->assertEquals(1, $numRows);
                
        
        //vyrobit job vetu
        /** @var JobDao $jobDao */
        $jobDao = $this->container->get(JobDao::class);
        $jobData = new RowData();
        $jobData->import( ['pozadovane_vzdelani_stupen' => 1, 'company_id' =>self::$companyIdTouple['id']] );
        $jobDao->insert($jobData);    
        self::$jobIdTouple = $jobDao->getLastInsertIdTouple();
        
        //vyrobit job_to_tag vetu  
        /** @var JobToTagDao $jobToTagDao */
        $jobToTagDao = $this->container->get(JobToTagDao::class);  
        $jobToTagData = new RowData();
        $jobToTagData->import( [ 'job_tag_tag' => self::$jobTagTouple ['tag']  , 'job_id'=>self::$jobIdTouple['id'] ] );
        $jobToTagDao->insert($jobToTagData);   
        /**  @var RowData  $row */
        $row = $jobToTagDao->get( [ 'job_tag_tag' => self::$jobTagTouple ['tag']  , 'job_id'=>self::$jobIdTouple['id'] ] );
        self::$jobToTagTouples  = $jobToTagDao->getPrimaryKey($row->getArrayCopy());      
        
   }

    public function testGetExistingRow() {
        $jobTagRow = $this->dao->get(self::$jobTagTouple);
        $this->assertInstanceOf(RowDataInterface::class, $jobTagRow);
    }

    public function test1Columns() {
        $jobTagRow = $this->dao->get( self::$jobTagTouple );
        $this->assertCount(1, $jobTagRow);
    }

   public function testUpdate() {
         $jobTagRow = $this->dao->get( self::$jobTagTouple );
         $tag = $jobTagRow['tag'];
         $this->assertIsString( $jobTagRow['tag'] );
        
        $this->setUp();
        $updated = str_replace('oko', 'kiki',$jobTagRow['tag']);
        $jobTagRow['tag'] = $updated;
        $this->dao->update($jobTagRow);
        $this->assertEquals(1, $this->dao->getRowCount());
        
        $rowArray = $jobTagRow->getArrayCopy();
        self::$jobTagTouple_poUpdate =  $this->dao->getPrimaryKey($rowArray);  

        $this->setUp();
        $a = [ 'job_tag_tag' => self::$jobTagTouple_poUpdate ['tag'] , 'job_id'=>self::$jobIdTouple['id'] ] ;
        $jobTagRowRereaded = $this->dao->get(  self::$jobTagTouple_poUpdate   );
        $this->assertEquals($jobTagRow, $jobTagRowRereaded);
        $this->assertStringContainsString('kiki', $jobTagRowRereaded['tag']);  
        
        //kontrola CASCADE u update
        //kontrola, ze v job_to_zag je taky updatovany tag
        /**  @var JobToTagDao  $jobToTagDao */
        $jobToTagDao = $this->container->get(JobToTagDao::class);
        $jobToTagRow = $jobToTagDao->get( [ 'job_tag_tag' => self::$jobTagTouple_poUpdate ['tag']  , 'job_id'=>self::$jobIdTouple['id'] ] );
        $this->assertEquals( self::$jobTagTouple_poUpdate ['tag'], $jobToTagRow['job_tag_tag'] );        
    }

    
    public function testFind() {
        $jobTagRow = $this->dao->find();
        $this->assertIsArray($jobTagRow);
        $this->assertGreaterThanOrEqual(1, count($jobTagRow));
        $this->assertInstanceOf(RowDataInterface::class, $jobTagRow[0]);
    }

   
    public function testDeleteException() {   
        // kontrola RESTRICT = že nevymaže job_tag, kdyz je  pouzit v job_to_tag
        $jobTagRow = $this->dao->get(self::$jobTagTouple_poUpdate);
        $this->expectException(ExecuteException::class);
        $this->dao->delete($jobTagRow);                
    }
    
    public function testDelete() {
        //delete Company - amze job, jobToTag
        /** @var CompanyDao $companyDao */
        $companyDao = $this->container->get(CompanyDao::class);    
        $companyData = $companyDao->get(self::$companyIdTouple );
        $ok = $companyDao->delete($companyData);              
                
        //pak smazat jobTag  
        $this->setUp();
        $jobTagRow = $this->dao->get(self::$jobTagTouple_poUpdate);
        $this->dao->delete($jobTagRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        //kontrola, ze smazano
        $this->setUp();
        $jobTagRow = $this->dao->get(self::$jobTagTouple_poUpdate);
        $this->assertNull($jobTagRow);
         
   }
}

