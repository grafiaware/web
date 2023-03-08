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

    private static $jobPrimaryKey;
    private static $jobTagIdTouple;
    private static $jobTagIdTouple_poUpdate;
    private static $jobToTagPrimaryKey;
    private static $companyPrimaryKey;



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
        self::$companyPrimaryKey = $companyDao->getLastInsertedPrimaryKey();

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
        self::$jobTagIdTouple =  $this->dao->getLastInsertedPrimaryKey();
        $this->assertIsArray(self::$jobTagIdTouple);

        $numRows = $this->dao->getRowCount();
        $this->assertEquals(1, $numRows);

        //self::$jobTagIdTouple =  $this->dao->get(self::$jobTagIdTouple);
        
        //vyrobit job vetu
        /** @var JobDao $jobDao */
        $jobDao = $this->container->get(JobDao::class);
        $jobData = new RowData();
        $jobData->import( ['pozadovane_vzdelani_stupen' => 1, 'company_id' =>self::$companyPrimaryKey['id']] );
        $jobDao->insert($jobData);
        self::$jobPrimaryKey = $jobDao->getLastInsertedPrimaryKey();

        //vyrobit job_to_tag vetu
        /** @var JobToTagDao $jobToTagDao */
        $jobToTagDao = $this->container->get(JobToTagDao::class);
        $jobToTagData = new RowData();
        $jobToTagData->import( [ 'job_tag_id' => self::$jobTagIdTouple ['id']  , 'job_id'=>self::$jobPrimaryKey['id'] ] );
        $jobToTagDao->insert($jobToTagData);
        self::$jobToTagPrimaryKey  = $jobToTagDao->getLastInsertedPrimaryKey();

   }

    public function testGetExistingRow() {
        $jobTagRow = $this->dao->get(self::$jobTagIdTouple);
        $this->assertInstanceOf(RowDataInterface::class, $jobTagRow);
    }

    public function test2Columns() {
        $jobTagRow = $this->dao->get( self::$jobTagIdTouple );
        $this->assertCount(2, $jobTagRow);
    }

   public function testUpdate() {
         $jobTagRow = $this->dao->get( self::$jobTagIdTouple );
         $tag = $jobTagRow['tag'];
         $this->assertIsString( $jobTagRow['tag'] );

        $this->setUp();
        $updated = str_replace('oko', 'kiki',$jobTagRow['tag']);
        $jobTagRow['tag'] = $updated;
        $this->dao->update($jobTagRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        $rowArray = $jobTagRow->getArrayCopy();
        self::$jobTagIdTouple_poUpdate =  $this->dao->getPrimaryKey($rowArray);

        $this->setUp();
        $a = [ //'job_tag_tag' => self::$jobTagIdTouple_poUpdate ['tag'] ,
               'job_id'=>self::$jobPrimaryKey['id'], 
               'job_tag_id' => self::$jobTagIdTouple_poUpdate ['id'] ] ;
        $jobTagRowRereaded = $this->dao->get(  self::$jobTagIdTouple_poUpdate   );
        $this->assertEquals($jobTagRow, $jobTagRowRereaded);
        $this->assertStringContainsString('kiki', $jobTagRowRereaded['tag']);

//        //kontrola CASCADE u update        
//        /**  @var JobToTagDao  $jobToTagDao */
//        $jobToTagDao = $this->container->get(JobToTagDao::class);
//        $jobToTagRow = $jobToTagDao->get( [ 'job_tag_id' => self::$jobTagIdTouple_poUpdate ['id']  , 'job_id'=>self::$jobPrimaryKey['id'] ] );
//        $this->assertInstanceOf(RowDataInterface::class, $jobToTagRow);  
}


    public function testFind() {
        $jobTagRow = $this->dao->find();
        $this->assertIsArray($jobTagRow);
        $this->assertGreaterThanOrEqual(1, count($jobTagRow));
        $this->assertInstanceOf(RowDataInterface::class, $jobTagRow[0]);
    }


    public function testDeleteException() {
        // kontrola RESTRICT = že nevymaže job_tag, kdyz je  pouzit v job_to_tag
        $jobTagRow = $this->dao->get(self::$jobTagIdTouple_poUpdate);
        $this->expectException(ExecuteException::class);
        $this->dao->delete($jobTagRow);
    }

    public function testDelete() {
        //delete Company - amze job, jobToTag
        /** @var CompanyDao $companyDao */
        $companyDao = $this->container->get(CompanyDao::class);
        $companyData = $companyDao->get(self::$companyPrimaryKey );
        $ok = $companyDao->delete($companyData);
        
      
        //pak smazat jobTag
        $this->setUp();
        $jobTagRow = $this->dao->get(self::$jobTagIdTouple_poUpdate);
        $this->dao->delete($jobTagRow);
        $this->assertEquals(1, $this->dao->getRowCount());

        //kontrola, ze smazano
        $this->setUp();
        $jobTagRow = $this->dao->get(self::$jobTagIdTouple_poUpdate);
        $this->assertNull($jobTagRow);

   }
}

