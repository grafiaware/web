<?php
declare(strict_types=1);

namespace Test\Integration\Event\Model\Repository;

use Container\EventsModelContainerConfigurator;
use Events\Model\Dao\CompanyDao;
use Events\Model\Entity\CompanyVersion;
use Events\Model\Repository\CompanyAddressRepo;
use Events\Model\Repository\CompanyContactRepo;
use Events\Model\Repository\CompanyRepo;
use Events\Model\Repository\CompanyVersionRepo;
use Pes\Container\Container;
use Pes\Model\RowData\RowData;
use Test\AppRunner\AppRunner;
use Test\Integration\Event\Container\TestDbEventsContainerConfigurator;

final class CompanyArchiveTest extends AppRunner
{
    private const COMPANY_NAME = 'CompanyArchiveTest_isolated';
    private const SOURCE_VERSION = '2025';
    private const ARCHIVE_VERSION = 'archive_2025';
    private const TARGET_VERSION = '2026';

    private CompanyRepo $companyRepo;
    private CompanyAddressRepo $companyAddressRepo;
    private CompanyContactRepo $companyContactRepo;
    private CompanyVersionRepo $companyVersionRepo;

    private static int $companyId;

    public static function setUpBeforeClass(): void
    {
        self::bootstrapBeforeClass();
        self::deleteRecords(
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(new Container())
            )
        );
        self::insertRecords(
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(new Container())
            )
        );
    }

    private static function insertRecords(Container $container): void
    {
        /** @var CompanyDao $companyDao */
        $companyDao = $container->get(CompanyDao::class);
        $rowData = new RowData();
        $rowData->offsetSet('name', self::COMPANY_NAME);
        $rowData->offsetSet('version_fk', self::SOURCE_VERSION);
        $companyDao->insert($rowData);
        self::$companyId = (int) $companyDao->getLastInsertedPrimaryKey()['id'];
    }

    private static function deleteRecords(Container $container): void
    {
        /** @var CompanyDao $companyDao */
        $companyDao = $container->get(CompanyDao::class);
        foreach ([self::SOURCE_VERSION, self::ARCHIVE_VERSION, self::TARGET_VERSION] as $version) {
            $rows = $companyDao->find('name = :name AND version_fk = :version', [
                'name' => self::COMPANY_NAME,
                'version' => $version,
            ]);
            foreach ($rows as $row) {
                $companyDao->delete($row);
            }
        }
    }

    protected function setUp(): void
    {
        $this->container = (new EventsModelContainerConfigurator())->configure(
            (new TestDbEventsContainerConfigurator())->configure(new Container())
        );
        $this->companyRepo = $this->container->get(CompanyRepo::class);
        $this->companyAddressRepo = $this->container->get(CompanyAddressRepo::class);
        $this->companyContactRepo = $this->container->get(CompanyContactRepo::class);
        $this->companyVersionRepo = $this->container->get(CompanyVersionRepo::class);
    }

    private Container $container;

    protected function tearDown(): void
    {
        $this->companyContactRepo->__destruct();
        $this->companyAddressRepo->__destruct();
        $this->companyRepo->__destruct();
        $this->companyVersionRepo->__destruct();
    }

    public static function tearDownAfterClass(): void
    {
        self::deleteRecords(
            (new EventsModelContainerConfigurator())->configure(
                (new TestDbEventsContainerConfigurator())->configure(new Container())
            )
        );
    }

    public function testVersionsExistInDatabase(): void
    {
        $this->assertInstanceOf(CompanyVersion::class, $this->companyVersionRepo->get(self::SOURCE_VERSION));
        $this->assertInstanceOf(CompanyVersion::class, $this->companyVersionRepo->get(self::ARCHIVE_VERSION));
        $this->assertInstanceOf(CompanyVersion::class, $this->companyVersionRepo->get(self::TARGET_VERSION));
    }

    public function testArchiveWorkflowForIsolatedCompany(): void
    {
        $company = $this->companyRepo->get(self::$companyId);
        $this->assertNotNull($company);
        $this->assertSame(self::SOURCE_VERSION, $company->getVersionFk());

        $archiveCompany = clone $company;
        $archiveCompany->setId(null);
        $archiveCompany->setVersionFk(self::ARCHIVE_VERSION);
        $this->companyRepo->add($archiveCompany);
        $archiveId = $archiveCompany->getId();
        $this->assertNotNull($archiveId);

        $company->setVersionFk(self::TARGET_VERSION);
        $this->companyRepo->flush();

        $this->companyRepo->__destruct();
        $this->companyRepo = $this->container->get(CompanyRepo::class);

        $archived = $this->companyRepo->get($archiveId);
        $this->assertNotNull($archived);
        $this->assertSame(self::ARCHIVE_VERSION, $archived->getVersionFk());

        $moved = $this->companyRepo->get(self::$companyId);
        $this->assertNotNull($moved);
        $this->assertSame(self::TARGET_VERSION, $moved->getVersionFk());
    }
}
