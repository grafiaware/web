<?php
declare(strict_types=1);
namespace Test\Integration\Event\Model\Repository;

use Test\AppRunner\AppRunner;
use Pes\Container\Container;
use Container\EventsModelContainerConfigurator;
use Test\Integration\Event\Container\TestDbEventsContainerConfigurator;

use Model\Repository\Exception\OperationWithLockedEntityException;
use Model\RowData\RowData;

use Events\Model\Dao\JobDao;
use Events\Model\Dao\CompanyDao;
use Events\Model\Dao\JobTagDao;
use Events\Model\Dao\JobToTagDao;

use Events\Model\Repository\JobToTagRepo;
use Events\Model\Entity\JobToTag;
use Events\Model\Entity\JobToTagInterface;




/**
 * Description of JobToTagRepositoryTest
 *
 * @author vlse2610
 */
class JobToTagRepositoryTest  extends AppRunner {
    private $container;

    /**
     *
     * @var JobToTagRepo
     */
    private $jobToTagRepo;

    private static $jobToTagName = "testJobToTagRepo";
    private static $companyId;
    private static $jobId1;
    private static $jobId2;
    private static $jobId3;
    private static $jobId4;
    private static $jobTagId;





    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(
                    (new Container( ) ) )
            );

        // mazání - zde jen pro případ, že minulý test nebyl dokončen
        self::deleteRecords($container);

        self::insertRecords($container);

    }


    private static function insertRecords(Container $container) {

         /** @var CompanyDao $companyDao */
        $companyDao = $container->get(CompanyDao::class);
        $rowData = new RowData();
        $rowData->import([
            'name' => self::$jobToTagName  ]);
        $companyDao->insert($rowData);
        self::$companyId = $companyDao->getLastInsertedPrimaryKey()[$companyDao->getAutoincrementFieldName()];


        /** @var JobDao $jobDao */
        $jobDao = $container->get(JobDao::class);
        $rowData = new RowData();
        $rowData->import([
            'pozadovane_vzdelani_stupen' => 1,
            'company_id'  =>   self::$companyId,
            'nazev'   =>  self::$jobToTagName . "1" ]);
        $jobDao->insert($rowData);
              self::$jobId1 = $jobDao->getLastInsertedPrimaryKey()[$jobDao->getAutoincrementFieldName()];
        $rowData = new RowData();
        $rowData->import([
            'pozadovane_vzdelani_stupen' => 1,
            'company_id'  =>   self::$companyId,
            'nazev'   =>  self::$jobToTagName . "2" ]);
        $jobDao->insert($rowData);
              self::$jobId2 = $jobDao->getLastInsertedPrimaryKey()[$jobDao->getAutoincrementFieldName()];
        $rowData = new RowData();
        $rowData->import([
            'pozadovane_vzdelani_stupen' => 1,
            'company_id'  =>   self::$companyId,
            'nazev'   =>  self::$jobToTagName . "3" ]);
        $jobDao->insert($rowData);
                self::$jobId3 = $jobDao->getLastInsertedPrimaryKey()[$jobDao->getAutoincrementFieldName()];
        $rowData = new RowData();
        $rowData->import([
            'pozadovane_vzdelani_stupen' => 1,
            'company_id'  =>   self::$companyId,
            'nazev'   =>  self::$jobToTagName . "4" ]);
        $jobDao->insert($rowData);
                self::$jobId4 = $jobDao->getLastInsertedPrimaryKey()[$jobDao->getAutoincrementFieldName()];


        /** @var JobTagDao $jobTagDao */
        $jobTagDao = $container->get(JobTagDao::class);
        $rowData = new RowData();
        $rowData->import([
            'tag' => self::$jobToTagName,
            ]);
        $jobTagDao->insert($rowData);
                    
        self::$jobTagId = $jobTagDao->getLastInsertedPrimaryKey(); //je array
        

        //-----------------
        // vlozi JobToTag
         /** @var JobToTagDao $jobToTagDao */
        $jobToTagDao = $container->get(JobToTagDao::class);
        $rowData = new RowData();
        $rowData->offsetSet('job_tag_id', self::$jobTagId ['id'] );
        $rowData->offsetSet('job_id', self::$jobId1 ) ;
        $jobToTagDao->insert($rowData);
    }

    private static function deleteRecords(Container $container) {
//        /** @var JobToTagDao $jobToTagDao */
//        $jobToTagDao = $container->get(JobToTagDao::class);
//        $rows = $jobToTagDao->find( " job_tag_id LIKE '". self::$jobTagId . "%'", []);
//        foreach($rows as $row) {
//            $ok =  $jobToTagDao->delete($row);
//        }

         /** @var JobDao $jobDao */  //job
        $jobDao = $container->get(JobDao::class);
        $rows = $jobDao->find(  " nazev LIKE '". self::$jobToTagName . "%'", []);
        foreach($rows as $row) {
            $ok = $jobDao->delete($row);
        }


         /** @var CompanyDao $companyDao */
        $companyDao = $container->get(CompanyDao::class);
        $rows = $companyDao->find( " name LIKE '". self::$jobToTagName . "%'", [] );
        foreach($rows as $row) {
            $ok = $companyDao->delete($row);
        }

         /** @var JobTagDao $jobTagDao */
        $jobTagDao = $container->get(JobTagDao::class);
        $rows = $jobTagDao->find( " tag LIKE '". self::$jobToTagName . "%'", [] );
        foreach($rows as $row) {
            $ok = $jobTagDao->delete($row);
        }

    }

    protected function setUp(): void {
        $this->container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(
                    (new Container(  ) )  )
            );

        $this->jobToTagRepo = $this->container->get( JobToTagRepo::class );
    }

    protected function tearDown(): void {
        //$this->->flush();
        $this->jobToTagRepo->__destruct();
    }

    public static function tearDownAfterClass(): void {
        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(
                    (new Container( ) ) )
            );

        self::deleteRecords($container);
    }


    public function testSetUp() {
        $this->assertInstanceOf( JobToTagRepo::class, $this->jobToTagRepo);
    }


    public function testGetNonExisted() {
        $jobToTag = $this->jobToTagRepo->get( "QWER45T6U7I89OPOLKJHGFD", 0);
        $this->assertNull($jobToTag);
    }



    public function testGetAfterSetUp() {
        $jobToTag = $this->jobToTagRepo->get(  self::$jobId1, self::$jobTagId ['id'] );
        $this->assertInstanceOf(JobToTagInterface::class, $jobToTag);
    }

    public function testAdd() {
        $jobToTag = new JobToTag();
        $jobToTag->setJobId(self::$jobId2 );
        $jobToTag->setJobTagId(self::$jobTagId ['id']);

        $this->jobToTagRepo->add($jobToTag);

        // pro automaticky|generovany klic a pro  overovany klic  - !!! zapise se hned !!!   DaoEditKeyDbVerifiedInterface
        // zde nezapise hned !!!!
        $this->assertFalse($jobToTag->isPersisted());
        $this->assertTrue($jobToTag->isLocked());
        // JobToTag je zamčena po add - az do vykonani flush

    }

    public function testGetAfterAdd() {
        /** @var JobToTagInterface $jobToTag */
        $jobToTag = $this->jobToTagRepo->get( self::$jobId2, self::$jobTagId ['id'] );
        $this->assertInstanceOf(JobToTagInterface::class, $jobToTag);
    }

    public function testAddAndReread() {
        /** @var JobToTag $jobToTag */
        $jobToTag = new JobToTag();
        $jobToTag->setJobId( self::$jobId3 );
        $jobToTag->setJobTagId(self::$jobTagId ['id']);
        $this->jobToTagRepo->add($jobToTag);

        $this->jobToTagRepo->flush();

        $jobToTagR = $this->jobToTagRepo->get( $jobToTag->getJobId(),  $jobToTag->getJobTagId() );
        $this->assertInstanceOf(JobToTagInterface::class, $jobToTagR);
        $this->assertTrue($jobToTagR->isPersisted() );
        $this->assertFalse($jobToTagR->isLocked());

        //$this->assertEquals( self::$jobToTagName, $jobToTagR->getJobTagTag() );
    }

    public function testFindByJobId(){
        $jobToTagArray = $this->jobToTagRepo->findByJobId(self::$jobId1);
        $this->assertTrue(is_array($jobToTagArray));
    }

    public function testFindByJobTagId(){
        $jobToTagArray = $this->jobToTagRepo->findByJobTagId( self::$jobTagId['id'] );
        $this->assertTrue(is_array($jobToTagArray));
    }


    public function testFindAll() {
        $jobToTagArray = $this->jobToTagRepo->findAll();
        $this->assertTrue(is_array($jobToTagArray));
    }



    public function testRemove_OperationWithLockedEntity() {
        /** @var JobToTag $jobToTag */
        $jobToTag = new JobToTag();
        $jobToTag->setJobId( self::$jobId4 );
        $jobToTag->setJobTagId(self::$jobTagId ['id'] );
        $this->jobToTagRepo->add($jobToTag);

        $this->assertFalse($jobToTag->isPersisted());
        $this->assertTrue($jobToTag->isLocked());

        $this->expectException( OperationWithLockedEntityException::class);
        $this->jobToTagRepo->remove($jobToTag);
   }



    public function testRemove() {
        /** @var JobToTag $jobToTag */
        $jobToTag = $this->jobToTagRepo->get(self::$jobId3, self::$jobTagId ['id'] );

        $this->assertInstanceOf(JobToTagInterface::class, $jobToTag);
        $this->assertTrue($jobToTag->isPersisted());
        $this->assertFalse($jobToTag->isLocked());

        $this->jobToTagRepo->remove($jobToTag);

        $this->assertTrue($jobToTag->isPersisted());
        $this->assertTrue($jobToTag->isLocked());   // zatim zamcena entita, maže až při flush
        $this->jobToTagRepo->flush();
        //  uz neni locked
        $this->assertFalse($jobToTag->isLocked());

        // pokus o čtení, entita JobToTag  uz  neni
        $jobToTag = $this->jobToTagRepo->get( $jobToTag->getJobId(),  $jobToTag->getJobTagId() );
        $this->assertNull($jobToTag);

    }


}

