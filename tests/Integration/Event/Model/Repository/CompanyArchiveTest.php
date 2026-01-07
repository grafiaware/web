<?php
declare(strict_types=1);
namespace Test\Integration\Event\Model\Repository;

use Test\AppRunner\AppRunner;
use Pes\Container\Container;
use Container\EventsModelContainerConfigurator;
use Test\Integration\Event\Container\TestDbEventsContainerConfigurator;

use Events\Model\Dao\CompanyDao;
use Events\Model\Repository\CompanyRepo;
use Events\Model\Repository\CompanyAddressRepo;
use Events\Model\Repository\CompanyContactRepo;
use Events\Model\Repository\CompanyVersionRepo;
use Events\Model\Entity\CompanyVersion;
use Model\Repository\Exception\OperationWithLockedEntityException;
use Model\RowData\RowData;


 /**
 * Description of CompanyRepositoryTest
 *
 * @author vlse2610
 */
class CompanyArchiveTest extends AppRunner {
    
    private $container;
    
    /**
     *
     * @var CompanyRepo
     */
    private $companyRepo;
    
    /**
     * 
     * @var CompanyVersionRepo
     */
    private $companyVersionRepo;

    /**
     *
     * @var CompanyAddressRepo
     */
    private $companyAddressRepo; 
    
    /**
     *
     * @var CompanyContactRepo
     */
    private $companyContactRepo;
    
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
//        $companyDao = $container->get(CompanyDao::class);
//        $rowData = new RowData();
//        $rowData->import([
//            'name' => self::$companyName    ]);
//        $companyDao->insert($rowData);
//        self::$companyId = $companyDao->getLastInsertedPrimaryKey()[$companyDao->getAutoincrementFieldName()];
    }

    private static function deleteRecords(Container $container) {
          /** @var CompanyDao $companyDao */
//        $companyDao = $container->get( CompanyDao::class);
//        $rows = $companyDao->find( " name LIKE '". "%" . self::$companyName . "%'", [] );
//        foreach($rows as $row) {
//            $ok = $companyDao->delete($row);
//        }
    }

    protected function setUp(): void {
        $this->container =
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(new Container())
            );
        $this->companyRepo = $this->container->get(CompanyRepo::class);
        $this->companyAddressRepo = $this->container->get(CompanyAddressRepo::class);
        $this->companyContactRepo = $this->container->get(CompanyContactRepo::class);
        $this->companyVersionRepo = $this->container->get(CompanyVersionRepo::class);
        
    }

    protected function tearDown(): void {
        $this->companyContactRepo->__destruct();
        $this->companyAddressRepo->__destruct();        
        $this->companyRepo->__destruct();
        $this->companyVersionRepo->__destruct();
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
        $this->assertInstanceOf( CompanyAddressRepo::class, $this->companyAddressRepo );
        $this->assertInstanceOf( CompanyContactRepo::class, $this->companyContactRepo );
        $this->assertInstanceOf( CompanyVersionRepo::class, $this->companyVersionRepo );
    }

    public function testVersionsExists() {
        $source = $this->companyVersionRepo->get('2025');
        $this->assertInstanceOf(CompanyVersion::class, $source);
        $archive = $this->companyVersionRepo->get('archive_2025');
        $this->assertInstanceOf(CompanyVersion::class, $archive);
        $target = $this->companyVersionRepo->get('2026');
        $this->assertInstanceOf(CompanyVersion::class, $target);
    }
    
    public function testFindByVersion() {
        // !! všechny hodnoty version musí být v databázo - v tabulve company_version (constraint violation)
        $sourceVersion = '2025';
        $archiveVersion = 'archive_2025';
        $targetVersion = '2026';
        $companies = $this->companyRepo->findByVersionAsc($sourceVersion);
        $this->assertIsArray($companies);
        foreach ($companies as $company) {
            $id = $company->getId();
            $address = $this->companyAddressRepo->get($id);
            $contacts = $this->companyContactRepo->findByCompanyId($id);
            
            // zdrojovou verzi naklonuje a uloží jako archivní, pak jí změní verzi na cílovou 
            // - id původní compady se nemění, všechny potomkovské entity zůstávají navázána (fk) na původní company
            $archiveCompany = clone $company;
            $archiveCompany->setId(null);  // clone nemaže id!!
            $archiveCompany->setVersionFk($archiveVersion);  // company_version má UNIQUE index name+version
            $this->companyRepo->add($archiveCompany);   // CompanyDao je DaoEditAutoincrementKeyInterface -> proběhne ihned INSERT do databáze a nastavení nového id do entity
            $newId = $archiveCompany->getId();
            $company->setVersionFk($targetVersion);
            
            if (isset($address)) {
                $archiveAddress = clone $address;
                $archiveAddress->setCompanyId($newId);  // připojí klon adresy k nové company
                $this->companyAddressRepo->add($archiveAddress);
            }
            foreach ($contacts as $contact) {
                $newContact = clone $contact;
                $newContact->setCompanyId($newId);
                $this->companyContactRepo->add($newContact);
            }
        }
        
//DELETE FROM company WHERE version_fk='archive_2025';
//
//UPDATE `company`
//SET `version_fk` = '2025'
//WHERE `version_fk` = '2026';        
        
    }

}
