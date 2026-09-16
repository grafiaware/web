<?php
declare(strict_types=1);

namespace Test\Integration\Event\Model\Dao;

use Container\EventsModelContainerConfigurator;
use Events\Model\Dao\CompanyVersionDao;
use Pes\Container\Container;
use Pes\Model\RowData\RowData;
use Pes\Model\RowData\RowDataInterface;
use Test\AppRunner\AppRunner;
use Test\Integration\Event\Container\TestDbEventsContainerConfigurator;

final class CompanyVersionDaoTest extends AppRunner
{
    private const VERSION = 'CompanyVersionDaoTest_v1';

    private CompanyVersionDao $dao;

    public static function setUpBeforeClass(): void
    {
        self::bootstrapBeforeClass();
        self::cleanup();
    }

    protected function setUp(): void
    {
        $container = (new EventsModelContainerConfigurator())->configure(
            (new TestDbEventsContainerConfigurator())->configure(new Container())
        );
        $this->dao = $container->get(CompanyVersionDao::class);
    }

    public static function tearDownAfterClass(): void
    {
        self::cleanup();
    }

    public function testInsertGetDelete(): void
    {
        $row = new RowData();
        $row->offsetSet('version', self::VERSION);
        $this->dao->insert($row);

        $loaded = $this->dao->get(['version' => self::VERSION]);
        $this->assertInstanceOf(RowDataInterface::class, $loaded);
        $this->assertSame(self::VERSION, $loaded['version']);

        $this->assertTrue($this->dao->delete($loaded));
        $this->setUp();
        $this->assertNull($this->dao->get(['version' => self::VERSION]));
    }

    private static function cleanup(): void
    {
        $container = (new EventsModelContainerConfigurator())->configure(
            (new TestDbEventsContainerConfigurator())->configure(new Container())
        );
        /** @var CompanyVersionDao $dao */
        $dao = $container->get(CompanyVersionDao::class);
        $row = $dao->get(['version' => self::VERSION]);
        if ($row !== null) {
            $dao->delete($row);
        }
    }
}
