<?php
declare(strict_types=1);
namespace Test\Integration\Event\Model\Repository;

use Test\AppRunner\AppRunner;
use Pes\Container\Container;
use Container\EventsModelContainerConfigurator;
use Test\Integration\Event\Container\TestDbEventsContainerConfigurator;

use Events\Model\Dao\CompanyDao;
use Events\Model\Dao\CompanyParameterDao;
use Events\Model\Repository\CompanyParameterRepo;
use Events\Model\Entity\CompanyParameter;
use Events\Model\Entity\CompanyParameterInterface;

use Model\Repository\Exception\OperationWithLockedEntityException;
use Model\RowData\RowData;


 /**
 * Description of CompanyAddressRepositoryTest
 *
 * @author vlse2610
 */
class CompanyParameterRepositoryTest extends AppRunner {
    private $container;
    /**
     *
     * @var CompanyParameterRepo
     */
    private $companyParameterRepo;

    private static $jobLimit = "10";
//    //private static $companyAddressId;

    private static $companyName = "testovka"; 
    private static $companyId;  


    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(new Container())
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
            'name' => self::$companyName
        ]);
        $companyDao->insert($rowData);
        self::$companyId = $companyDao->getLastInsertedPrimaryKey()[$companyDao->getAutoincrementFieldName()];

        /** @var CompanyParameterDao $companyParameterDao */
        $companyParameterDao = $container->get(CompanyParameterDao::class);
        $rowData = new RowData();
        $rowData->import([
            'job_limit' => self::$jobLimit,
            'company_id'  =>   self::$companyId,
           
        ]);
        $companyParameterDao->insert($rowData);
    }


    private static function deleteRecords(Container $container) {
        /** @var CompanyDao $companyDao */
        $companyDao = $container->get( CompanyDao::class);
        $rows = $companyDao->find( " name LIKE '". "%" . self::$companyName . "%'", [] );
        foreach($rows as $row) {
            $ok = $companyDao->delete($row);
        }

        /** @var CompanyParameterDao $companyParameterDao */
        $companyParameterDao = $container->get( CompanyParameterDao::class);
        $rows = $companyParameterDao->find( " job_limit = " . self::$jobLimit , []);
        foreach($rows as $row) {
            $ok = $companyParameterDao->delete($row);
        }
    }



    protected function setUp(): void {
        $this->container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(new Container())
            );
        $this->companyParameterRepo = $this->container->get(CompanyParameterRepo::class);
    }



    protected function tearDown(): void {
        //$this->e>flush();
        $this->companyParameterRepo->__destruct();
    }


    public static function tearDownAfterClass(): void {
        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(new Container())
            );
        self::deleteRecords($container);
    }


    public function testSetUp() {
        $this->assertInstanceOf( CompanyParameterRepo::class, $this->companyParameterRepo );
    }


    public function testGetNonExisted() {
        $companyParameter = $this->companyParameterRepo->get(0);
        $this->assertNull($companyParameter);
    }


    public function testGetAfterSetup() {
        $companyParameter = $this->companyParameterRepo->get(self::$companyId);
        $this->assertInstanceOf(CompanyParameterInterface::class, $companyParameter);
    }


    public function testGetAndRemoveAfterSetup() {
        $companyParameter = $this->companyParameterRepo->get(self::$companyId);
        $this->assertInstanceOf(CompanyParameterInterface::class, $companyParameter);

        $this->companyParameterRepo->remove($companyParameter);
        $this->assertTrue($companyParameter->isPersisted());
        $this->assertTrue($companyParameter->isLocked());
    }


    public function testGetAfterRemove() {
        $companyParameter = $this->companyParameterRepo->get(self::$companyId);
        $this->assertNull($companyParameter);
    }



    public function testAdd() {
        $companyParameter = new CompanyParameter();
        $companyParameter->setJobLimit( self::$jobLimit  );
        $companyParameter->setCompanyId(  self::$companyId );
       
        $this->companyParameterRepo->add($companyParameter);  //nezapise hned!!! --nenigenerovany ani overovany

        $this->assertFalse($companyParameter->isPersisted());
        $this->assertTrue($companyParameter->isLocked());
    }


    public function testReread() {
        $companyParameterRereaded = $this->companyParameterRepo->get( self::$companyId );

        $this->assertInstanceOf(CompanyParameterInterface::class, $companyParameterRereaded);
        $this->assertTrue($companyParameterRereaded->isPersisted());
        $this->assertFalse($companyParameterRereaded->isLocked());
    }

//    /**
//     * Pokus o zapsani věty do company_address se stejným primárním klíčem.
//     * Na úrovni DB nastane chyba (tj. OK) a nic se neprovede. Nijak se ale neda poznat,že nastala?
//     */
//    public function testAdd2() {
//        $companyAddress = new CompanyAddress();
//        $companyAddress->setName( self::$companyAddressName  . "trr222" );
//        $companyAddress->setCompanyId(  self::$companyId );
//        $companyAddress->setLokace( self::$companyAddressName );
//        $this->companyAddressRepo->add($companyAddress);  //nezapise hned!!! --nenigenerovany ani overovany
//
//        $this->assertFalse($companyAddress->isPersisted());
//        $this->assertTrue($companyAddress->isLocked());
//    }


    public function testfindAll() {
        $companyParameterArray = $this->companyParameterRepo->findAll();
        $this->assertTrue(is_array($companyParameterArray));
    }


    public function testFind() {
       $companyParameterArray = $this->companyParameterRepo->find( " job_limit = " . self::$jobLimit , []);
       $this->assertTrue(is_array($companyParameterArray));
       $this->assertGreaterThan(0,count($companyParameterArray)); //jsou tam minimalne 1
       $this->assertInstanceOf( CompanyParameterInterface::class, $companyParameterArray [0] );
    }


    public function testRemove_OperationWithLockedEntity() {
        $companyParameter = $this->companyParameterRepo->get( self::$companyId );
        $this->assertInstanceOf(CompanyParameterInterface::class,  $companyParameter);
        $this->assertTrue( $companyParameter->isPersisted());
        $this->assertFalse( $companyParameter->isLocked());

        $companyParameter->lock();
        $this->expectException( OperationWithLockedEntityException::class);
        $this->companyParameterRepo->remove( $companyParameter);
    }


    public function testRemove() {
        $companyParameter = $this->companyParameterRepo->get(self::$companyId );
        $this->assertInstanceOf( CompanyParameterInterface::class,  $companyParameter);
        $this->assertTrue( $companyParameter->isPersisted());
        $this->assertFalse( $companyParameter->isLocked());

        $this->companyParameterRepo->remove($companyParameter);

        $this->assertTrue($companyParameter->isPersisted());
        $this->assertTrue($companyParameter->isLocked());   // maže až při flush

        $this->companyParameterRepo->flush();
        //  uz neni locked
        $this->assertFalse($companyParameter->isLocked());

        // pokus o čtení, institution uz  neni
        $companyParameter2 = $this->companyParameterRepo->get(self::$companyId );
        $this->assertNull($companyParameter2);
    }

}



