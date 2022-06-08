<?php
declare(strict_types=1);

use Test\AppRunner\AppRunner;

use Pes\Container\Container;
use Test\Integration\Event\Container\EventsContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

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
    private static $jobToTagTouple;
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
        $companyData->offsetSet( 'name' , "pomocna pro jobTagDaoTest"  );
        $companyDao->insert($companyData);
        self::$companyIdTouple = $companyDao->getLastInsertIdTouple();
                
    }

    protected function setUp(): void {
        $this->container =
            (new EventsContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
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
        self::$jobTagTouple =  $this->dao->get( ['tag' => "vesmír a okolí"] );
        //$this->assertIsArray(self::$jobTagIdTouple);
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
        $row = $jobToTagDao->get( [ 'job_tag_tag' => self::$jobTagTouple ['tag']  , 'job_id'=>self::$jobIdTouple['id'] ] );
                //getPrimaryKeyTouples($jobToTagData);
                //[ 'job_tag_tag' => self::$jobTagTouple ['tag']  , 'job_id'=>self::$jobIdTouple['id'] ];
        
        
   }
//
//    public function testGetExistingRow() {
//        $eventLinkPhaseRow = $this->dao->get(self::$eventLinkPhaseIdTouple);
//        $this->assertInstanceOf(RowDataInterface::class, $eventLinkPhaseRow);
//    }
//
//    public function test2Columns() {
//        $eventLinkPhaseRow = $this->dao->get(self::$eventLinkPhaseIdTouple);
//        $this->assertCount(2, $eventLinkPhaseRow);
//    }
//
//    public function testUpdate() {
//        $eventLinkPhaseRow = $this->dao->get(self::$eventLinkPhaseIdTouple);
//        //$name = $eventLinkPhaseRow['text'];
//        $this->assertIsString($eventLinkPhaseRow['text']);
//        //
//        $this->setUp();
//        $updated = str_replace('texxt', '--text--',$eventLinkPhaseRow['text']);
//        $eventLinkPhaseRow['text'] = $updated;
//        $this->dao->update($eventLinkPhaseRow);
//        $this->assertEquals(1, $this->dao->getRowCount());
//
//        $this->setUp();
//        $eventLinkPhaseRowRereaded = $this->dao->get(self::$eventLinkPhaseIdTouple);
//        $this->assertEquals($eventLinkPhaseRow, $eventLinkPhaseRowRereaded);
//        $this->assertStringContainsString('--text--', $eventLinkPhaseRowRereaded['text']);
//    }
//
//    public function testFind() {
//        $eventLinkPhaseRow = $this->dao->find();
//        $this->assertIsArray($eventLinkPhaseRow);
//        $this->assertGreaterThanOrEqual(1, count($eventLinkPhaseRow));
//        $this->assertInstanceOf(RowDataInterface::class, $eventLinkPhaseRow[0]);
//    }

   
//    public function testDeleteException() {     NEJDE smazat
//        // kontrola RESTRICT = ze nevymaže event_link, zustane
//        $eventLinkPhaseRow = $this->dao->get(self::$eventLinkPhaseIdTouple);
//        $this->expectException(ExecuteException::class);
//        $this->dao->delete($eventLinkPhaseRow);                
//    }
//    
    public function testDelete() {
        /** @var CompanyDao $companyDao */
        $companyDao = $this->container->get(CompanyDao::class);    
        $companyData = $companyDao->get(self::$companyIdTouple /*['id' =>  self::$company_id ]*/ );
        $ok = $companyDao->delete($companyData);
                
        //smazat job_to_tag vetu    NENI TREBA , NAOPAK  uz je smzano  ---- otestovat asi ne
        /** @var JobToTagDao $jobToTagDao */
        $jobToTagDao = $this->container->get(JobToTagDao::class);  
        //$jobToTagData = $jobToTagDao->get(self::$jobToTagTouple); 
        $jobToTagData = $jobToTagDao->get([ 'job_tag_tag' => self::$jobTagTouple ['tag']  , 'job_id'=>self::$jobIdTouple['id'] ]);
        $jobToTagDao->delete($jobToTagData);
        
        //smazat job vetu   // taky smazano uz
        /** @var JobDao $jobDao */
        $jobDao = $this->container->get(JobDao::class);
        $jobData = $jobDao->get(self::$jobIdTouple);
        $jobDao->delete($jobData);    
        
        //pak smazat jobTag  ??? asi takz cascade zmizi
        //$this->setUp();
        $jobTagRow = $this->dao->get(self::$jobTagTouple);
        $this->dao->delete($jobTagRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $this->setUp();
        $jobTagRow = $this->dao->get(self::$jobTagTouple);
        $this->assertNull($jobTagRow);
        
  
//        /**  @var EventLinkDao  $eventLinkDao */
//        $eventLinkDao = $this->container->get( EventLinkDao::class);
//        $eventLinkRow = $eventLinkDao->get(self::$eventLinkIdTouple);        
//        $this->assertEquals(  self::$eventLinkPhaseIdTouple['id'], $eventLinkRow['link_phase_id_fk'] );
//        
//         //smazat napred Institution  
//        $eventLinkRow = $eventLinkDao->get(self::$eventLinkIdTouple);
//        $eventLinkDao->delete($eventLinkRow);
//        $this->assertEquals(1, $eventLinkDao->getRowCount());
//                        
//        
//        //pak smazat event_link_phase
//        //$this->setUp();
//        $eventLinkPhaseTypeRow = $this->dao->get(self::$eventLinkPhaseIdTouple);
//        $this->dao->delete($eventLinkPhaseTypeRow);
//        $this->assertEquals(1, $this->dao->getRowCount());
//
//        $this->setUp();
//        $eventLinkPhaseTypeRow = $this->dao->get(self::$eventLinkPhaseIdTouple);
//        $this->assertNull($eventLinkPhaseTypeRow);
   }
}

