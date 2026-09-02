<?php
declare(strict_types=1);

namespace Test\Integration\Event\Model\Dao;

use Container\EventsModelContainerConfigurator;
use Events\Model\Dao\CompanyDao;
use Events\Model\Dao\CompanyToNetworkDao;
use Events\Model\Dao\NetworkDao;
use Pes\Container\Container;
use Pes\Model\RowData\RowData;
use Test\AppRunner\AppRunner;
use Test\Integration\Event\Container\TestDbEventsContainerConfigurator;

final class CompanyToNetworkDaoTest extends AppRunner
{
    private CompanyToNetworkDao $dao;
    private static int $companyId;
    private static int $networkId;
    private static string $companyName;

    public static function setUpBeforeClass(): void
    {
        self::bootstrapBeforeClass();
        self::$companyName = 'CompanyToNetworkDaoTest_' . uniqid();
        $container = (new EventsModelContainerConfigurator())->configure(
            (new TestDbEventsContainerConfigurator())->configure(new Container())
        );

        /** @var CompanyDao $companyDao */
        $companyDao = $container->get(CompanyDao::class);
        foreach ($companyDao->find('name LIKE :name', ['name' => 'CompanyToNetworkDaoTest_%']) as $row) {
            $companyDao->delete($row);
        }

        $companyRow = new RowData();
        $companyRow->offsetSet('name', self::$companyName);
        $companyRow->offsetSet('version_fk', 'test');
        $companyDao->insert($companyRow);
        self::$companyId = (int) $companyDao->getLastInsertedPrimaryKey()['id'];

        /** @var NetworkDao $networkDao */
        $networkDao = $container->get(NetworkDao::class);
        $networkRow = new RowData();
        $networkRow->offsetSet('title', 'CompanyToNetworkDaoTest network');
        $networkRow->offsetSet('icon', '');
        $networkRow->offsetSet('embed_code_template', '');
        $networkDao->insert($networkRow);
        self::$networkId = (int) $networkDao->getLastInsertedPrimaryKey()['id'];
    }

    protected function setUp(): void
    {
        $container = (new EventsModelContainerConfigurator())->configure(
            (new TestDbEventsContainerConfigurator())->configure(new Container())
        );
        $this->dao = $container->get(CompanyToNetworkDao::class);
    }

    public static function tearDownAfterClass(): void
    {
        $container = (new EventsModelContainerConfigurator())->configure(
            (new TestDbEventsContainerConfigurator())->configure(new Container())
        );

        /** @var CompanyToNetworkDao $linkDao */
        $linkDao = $container->get(CompanyToNetworkDao::class);
        foreach ($linkDao->findByCompanyIdFk(['company_id' => self::$companyId]) as $row) {
            $linkDao->delete($row);
        }

        /** @var NetworkDao $networkDao */
        $networkDao = $container->get(NetworkDao::class);
        $networkRow = $networkDao->get(['id' => self::$networkId]);
        if ($networkRow !== null) {
            $networkDao->delete($networkRow);
        }

        /** @var CompanyDao $companyDao */
        $companyDao = $container->get(CompanyDao::class);
        $companyRow = $companyDao->get(['id' => self::$companyId]);
        if ($companyRow !== null) {
            $companyDao->delete($companyRow);
        }
    }

    public function testInsertFindByCompanyAndDelete(): void
    {
        $row = new RowData();
        $row->offsetSet('company_id', self::$companyId);
        $row->offsetSet('network_id', self::$networkId);
        $row->offsetSet('link', 'https://example.cz');
        $row->offsetSet('published', 1);
        $this->dao->insert($row);

        $links = $this->dao->findByCompanyIdFk(['company_id' => self::$companyId]);
        $this->assertCount(1, $links);
        $this->assertSame('https://example.cz', $links[0]['link']);

        $this->assertTrue($this->dao->delete($links[0]));
    }
}
