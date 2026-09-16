<?php
declare(strict_types=1);

namespace Test\Integration\Event\Model\Dao;

use Container\EventsModelContainerConfigurator;
use Events\Model\Dao\CompanyDao;
use Events\Model\Dao\CompanyParameterDao;
use Pes\Container\Container;
use Pes\Model\RowData\RowData;
use Pes\Model\RowData\RowDataInterface;
use Test\AppRunner\AppRunner;
use Test\Integration\Event\Container\TestDbEventsContainerConfigurator;

final class CompanyParameterDaoTest extends AppRunner
{
    private CompanyParameterDao $dao;
    private static int $companyId;

    public static function setUpBeforeClass(): void
    {
        self::bootstrapBeforeClass();
        $container = (new EventsModelContainerConfigurator())->configure(
            (new TestDbEventsContainerConfigurator())->configure(new Container())
        );

        /** @var CompanyDao $companyDao */
        $companyDao = $container->get(CompanyDao::class);
        $companyRow = new RowData();
        $companyRow->offsetSet('name', 'CompanyParameterDaoTest');
        $companyRow->offsetSet('version_fk', 'test');
        $companyDao->insert($companyRow);
        self::$companyId = (int) $companyDao->getLastInsertedPrimaryKey()['id'];
    }

    protected function setUp(): void
    {
        $container = (new EventsModelContainerConfigurator())->configure(
            (new TestDbEventsContainerConfigurator())->configure(new Container())
        );
        $this->dao = $container->get(CompanyParameterDao::class);
    }

    public static function tearDownAfterClass(): void
    {
        $container = (new EventsModelContainerConfigurator())->configure(
            (new TestDbEventsContainerConfigurator())->configure(new Container())
        );

        /** @var CompanyParameterDao $parameterDao */
        $parameterDao = $container->get(CompanyParameterDao::class);
        $row = $parameterDao->get(['company_id' => self::$companyId]);
        if ($row !== null) {
            $parameterDao->delete($row);
        }

        /** @var CompanyDao $companyDao */
        $companyDao = $container->get(CompanyDao::class);
        $companyRow = $companyDao->get(['id' => self::$companyId]);
        if ($companyRow !== null) {
            $companyDao->delete($companyRow);
        }
    }

    public function testInsertGetUpdateDelete(): void
    {
        $row = new RowData();
        $row->offsetSet('company_id', self::$companyId);
        $row->offsetSet('job_limit', 5);
        $this->dao->insert($row);

        $loaded = $this->dao->get(['company_id' => self::$companyId]);
        $this->assertInstanceOf(RowDataInterface::class, $loaded);
        $this->assertSame(5, (int) $loaded['job_limit']);

        $loaded['job_limit'] = 12;
        $this->assertTrue($this->dao->update($loaded));

        $this->setUp();
        $updated = $this->dao->get(['company_id' => self::$companyId]);
        $this->assertSame(12, (int) $updated['job_limit']);

        $this->assertTrue($this->dao->delete($updated));
    }
}
