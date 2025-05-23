<?php
declare(strict_types=1);
namespace Test\Integration\Event\Model\Repository;

use Test\AppRunner\AppRunner;
use Pes\Container\Container;
use Container\EventsModelContainerConfigurator;
use Test\Integration\Event\Container\TestDbEventsContainerConfigurator;

use Events\Model\Dao\CompanyDao;
use Events\Model\Repository\CompanyRepo;
use Events\Model\Entity\Company;
use Events\Model\Entity\CompanyInterface;

use Model\Repository\Exception\OperationWithLockedEntityException;
use Model\RowData\RowData;


 /**
 * Description of CompanyRepositoryTest
 *
 * @author vlse2610
 */
class CompanyRepositoryTest extends AppRunner {
    private $container;
    /**
     *
     * @var CompanyRepo
     */
    private $companyRepo;

    private static $companyName = "proCompanyRepoTest";
    private static $companyName2 = "proCompanyRepoTest" ."trrwqz.zu?aa";
    private static $companyId;

    /**
     *
     * @var Company
     */
    private static $company2;


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
            'name' => self::$companyName    ]);
        $companyDao->insert($rowData);
        self::$companyId = $companyDao->getLastInsertedPrimaryKey()[$companyDao->getAutoincrementFieldName()];
    }


    private static function deleteRecords(Container $container) {
          /** @var CompanyDao $companyDao */
        $companyDao = $container->get( CompanyDao::class);
        $rows = $companyDao->find( " name LIKE '". "%" . self::$companyName . "%'", [] );
        foreach($rows as $row) {
            $ok = $companyDao->delete($row);
        }
    }



    protected function setUp(): void {
        $this->container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(new Container())
            );
        $this->companyRepo = $this->container->get(CompanyRepo::class);
    }



    protected function tearDown(): void {
        //$this->eventLinkRepo->flush();
        $this->companyRepo->__destruct();

    }


    public static function tearDownAfterClass(): void {
        $container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(new Container())
            );
        self::deleteRecords($container);
    }


    public function testSetUp() {
        $this->assertInstanceOf( CompanyRepo::class, $this->companyRepo );
    }


    public function testGetNonExisted() {
        $company = $this->companyRepo->get(0);
        $this->assertNull($company);
    }


    public function testGetAfterSetup() {
        $company = $this->companyRepo->get(self::$companyId);
        $this->assertInstanceOf(CompanyInterface::class, $company);
    }


    public function testGetAndRemoveAfterSetup() {
        $company = $this->companyRepo->get(self::$companyId);
        $this->assertInstanceOf(CompanyInterface::class, $company);

        $this->companyRepo->remove($company);
        $this->assertTrue($company->isPersisted());
        $this->assertTrue($company->isLocked());
    }


    public function testGetAfterRemove() {
        $company = $this->companyRepo->get(self::$companyId);
        $this->assertNull($company);
    }



    public function testAdd() {
        $company = new Company();
        $company->setName( self::$companyName2 );
        $this->companyRepo->add($company);  //zapise hned

        $this->assertFalse($company->isLocked());
        $this->assertTrue($company->isPersisted());

        self::$company2 = $this->companyRepo->get($company->getId());
    }


    public function testReread() {
        $companyRereaded = $this->companyRepo->get( self::$company2->getId() );

        $this->assertInstanceOf(CompanyInterface::class, $companyRereaded);
        $this->assertTrue($companyRereaded->isPersisted());
        $this->assertFalse($companyRereaded->isLocked());
    }


    public function testfindAll() {
        $companyArray = $this->companyRepo->findAll();
        $this->assertTrue(is_array($companyArray));
    }


    public function testFind() {
       $companyArray = $this->companyRepo->find( " name LIKE '%" . self::$companyName . "%'", []);
       $this->assertTrue(is_array($companyArray));
       $this->assertGreaterThan(0,count($companyArray)); //jsou tam 
    }
    
    public function  testGetByName() {
        $company = $this->companyRepo->getByName(self::$companyName2);   
        $this->assertInstanceOf(CompanyInterface::class, $company);
        
        $company = $this->companyRepo->getByName('neočekávaná');
        $this->assertNull($company);               
    }


    public function testRemove_OperationWithLockedEntity() {
        $company = $this->companyRepo->get(self::$company2->getId() );
        $this->assertInstanceOf(CompanyInterface::class, $company);
        $this->assertTrue($company->isPersisted());
        $this->assertFalse($company->isLocked());

        $company->lock();
        $this->expectException( OperationWithLockedEntityException::class);
        $this->companyRepo->remove($company);
    }


    public function testRemove() {
        $company = $this->companyRepo->get(self::$company2->getId() );
        $this->assertInstanceOf( CompanyInterface::class, $company);
        $this->assertTrue($company->isPersisted());
        $this->assertFalse($company->isLocked());

        $this->companyRepo->remove($company);

        $this->assertTrue($company->isPersisted());
        $this->assertTrue($company->isLocked());   // maže až při flush

        $this->companyRepo->flush();
        //  uz neni locked
        $this->assertFalse($company->isLocked());

        // pokus o čtení, institution uz  neni
        $company = $this->companyRepo->get( self::$company2->getId() );
        $this->assertNull($company);
    }

}
