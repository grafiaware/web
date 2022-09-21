<?php
declare(strict_types=1);
namespace Test\Integration\Event\Model\Repository;

use Test\AppRunner\AppRunner;
use Pes\Container\Container;
use Test\Integration\Event\Container\EventsModelContainerConfigurator;
use Test\Integration\Event\Container\DbEventsContainerConfigurator;

use Events\Model\Dao\CompanyDao;
use Events\Model\Dao\JobDao;

use Events\Model\Repository\JobRepo;
use Events\Model\Entity\Job;
use Events\Model\Entity\JobInterface;

use Model\Repository\Exception\OperationWithLockedEntityException;
use Model\RowData\RowData;


 /**
 * Description of JobRepositoryTest
 *
 * @author vlse2610
 */
class JobRepositoryTest extends AppRunner {
    private $container;
    /**
     *
     * @var JobRepo
     */
    private $jobRepo;

    private static $jobName = "proJobRepoTest";    
    private static $jobId;
    private static $companyId;

    
    /**
     * 
     * @var Job
     */
    private static $job2;            


    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
         // mazání - zde jen pro případ, že minulý test nebyl dokončen
        self::deleteRecords($container);        
        self::insertRecords($container);                       
    }
    
    
    private static function insertRecords( Container $container) {
        /** @var CompanyDao $companyDao */
        $companyDao = $container->get(CompanyDao::class);  
        $rowData = new RowData();
        $rowData->import([
            'name' => self::$jobName  ]);
        $companyDao->insert($rowData);                
        self::$companyId = $companyDao->lastInsertIdValue(); 
        
        /** @var JobDao $jobDao */
        $jobDao = $container->get(JobDao::class);  
        $rowData = new RowData();
        $rowData->import([
            'pozadovane_vzdelani_stupen' => 1,  
            'company_id'  =>   self::$companyId,
            'nazev'   =>  self::$jobName  ]);
        $jobDao->insert($rowData);                
        self::$jobId = $jobDao->lastInsertIdValue(); 
    }

    
    private static function deleteRecords(Container $container) {
        /** @var JobDao $jobDao */
        $jobDao = $container->get( JobDao::class);
        $rows = $jobDao->find( " nazev LIKE '". "%" . self::$jobName . "%'", [] );
        foreach($rows as $row) {
            $ok = $jobDao->delete($row);
        }         
        
        /** @var JobDao $jobDao */
        $companyDao = $container->get( CompanyDao::class);
        $rows = $companyDao->find( " name LIKE '". "%" . self::$jobName . "%'", [] );
        foreach($rows as $row) {
            $ok = $companyDao->delete($row);
        }         
    } 

    

    protected function setUp(): void {
        $this->container =
            (new EventsModelContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        $this->jobRepo = $this->container->get(JobRepo::class);
    }
    
    
    
    protected function tearDown(): void {
        //$this->e>flush();
        $this->jobRepo->__destruct();        
    }
    

    public static function tearDownAfterClass(): void {
        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new DbEventsContainerConfigurator())->configure(new Container())
            );
        self::deleteRecords($container);
    }
    
    
    public function testSetUp() {
        $this->assertInstanceOf( JobRepo::class, $this->jobRepo );
    }
    

    public function testGetNonExisted() {
        $job = $this->jobRepo->get(0);
        $this->assertNull($job);
    }
    
    
    public function testGetAfterSetup() {
        $job = $this->jobRepo->get(self::$jobId);    
        $this->assertInstanceOf(JobInterface::class, $job);
    }


    public function testGetAndRemoveAfterSetup() {
        $job = $this->jobRepo->get(self::$jobId);    
        $this->assertInstanceOf(JobInterface::class, $job);
        
        $this->jobRepo->remove($job);        
        $this->assertTrue($job->isPersisted());
        $this->assertTrue($job->isLocked()); 
    }

    
    public function testGetAfterRemove() {
        $job = $this->jobRepo->get(self::$jobId);
        $this->assertNull($job);
    }

   
    
    public function testAdd() {
        $job = new Job();      
        $job->setNazev( self::$jobName  . "trrwqz.zu?aa" );
        $job->setCompanyId(  self::$companyId );
        $job->setPozadovaneVzdelaniStupen(1);
        $this->jobRepo->add($job);  //zapise hned               
        $this->assertTrue($job->isPersisted());
        $this->assertFalse($job->isLocked());
        
        self::$job2 = $this->jobRepo->get($job->getId());        
    }
    
    
    public function testReread() {        
        $jobRereaded = $this->jobRepo->get( self::$job2->getId() );
     
        $this->assertInstanceOf(JobInterface::class, $jobRereaded);
        $this->assertTrue($jobRereaded->isPersisted());
        $this->assertFalse($jobRereaded->isLocked());        
    }
    

    public function testfindAll() {
        $jobArray = $this->jobRepo->findAll();
        $this->assertTrue(is_array($jobArray));
    }
    
    
    public function testFind() {      
       $jobArray = $this->jobRepo->find( " nazev LIKE '%" . self::$jobName . "%'", []); 
       $this->assertTrue(is_array($jobArray));
       $this->assertGreaterThan(0,count($jobArray)); //jsou tam                        
    }


    public function testRemove_OperationWithLockedEntity() {
        $job = $this->jobRepo->get(self::$job2->getId() );    
        $this->assertInstanceOf(JobInterface::class, $job);
        $this->assertTrue($job->isPersisted());
        $this->assertFalse($job->isLocked());
        
        $job->lock();
        $this->expectException( OperationWithLockedEntityException::class);
        $this->jobRepo->remove($job);
    }

    
    public function testRemove() {
        $job = $this->jobRepo->get(self::$job2->getId() );    
        $this->assertInstanceOf( JobInterface::class, $job);
        $this->assertTrue($job->isPersisted());
        $this->assertFalse($job->isLocked());

        $this->jobRepo->remove($job);
        
        $this->assertTrue($job->isPersisted());
        $this->assertTrue($job->isLocked());   // maže až při flush
       
        $this->jobRepo->flush();
        //  uz neni locked
        $this->assertFalse($job->isLocked());
       
        // pokus o čtení, institution uz  neni
        $job2 = $this->jobRepo->get( self::$job2->getId() );
        $this->assertNull($job2);
    }

}


