<?php
declare(strict_types=1);

namespace Test\Integration\Event\Model\Dao;

use Container\EventsModelContainerConfigurator;
use Events\Model\Dao\CompanyDao;
use Events\Model\Dao\CompanyInfoDao;
use Pes\Container\Container;
use Pes\Model\RowData\RowData;
use Pes\Model\RowData\RowDataInterface;
use Test\AppRunner\AppRunner;
use Test\Integration\Event\Container\TestDbEventsContainerConfigurator;

final class CompanyInfoDaoTest extends AppRunner
{
    private CompanyInfoDao $dao;
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
        $companyRow->offsetSet('name', 'CompanyInfoDaoTest');
        $companyRow->offsetSet('version_fk', 'test');
        $companyDao->insert($companyRow);
        self::$companyId = (int) $companyDao->getLastInsertedPrimaryKey()['id'];
    }

    protected function setUp(): void
    {
        $container = (new EventsModelContainerConfigurator())->configure(
            (new TestDbEventsContainerConfigurator())->configure(new Container())
        );
        $this->dao = $container->get(CompanyInfoDao::class);
    }

    public static function tearDownAfterClass(): void
    {
        $container = (new EventsModelContainerConfigurator())->configure(
            (new TestDbEventsContainerConfigurator())->configure(new Container())
        );

        /** @var CompanyInfoDao $infoDao */
        $infoDao = $container->get(CompanyInfoDao::class);
        $row = $infoDao->get(['company_id' => self::$companyId]);
        if ($row !== null) {
            $infoDao->delete($row);
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
        $row->offsetSet('introduction', 'Intro text');
        $row->offsetSet('video_link', 'https://video.example');
        $this->dao->insert($row);

        $loaded = $this->dao->get(['company_id' => self::$companyId]);
        $this->assertInstanceOf(RowDataInterface::class, $loaded);
        $this->assertSame('Intro text', $loaded['introduction']);

        $loaded['introduction'] = 'Updated intro';
        $this->assertTrue($this->dao->update($loaded));

        $this->setUp();
        $updated = $this->dao->get(['company_id' => self::$companyId]);
        $this->assertSame('Updated intro', $updated['introduction']);

        $this->assertTrue($this->dao->delete($updated));
    }
}
