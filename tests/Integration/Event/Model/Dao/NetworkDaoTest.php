<?php
declare(strict_types=1);

namespace Test\Integration\Event\Model\Dao;

use Container\EventsModelContainerConfigurator;
use Events\Model\Dao\NetworkDao;
use Pes\Container\Container;
use Pes\Model\RowData\RowData;
use Pes\Model\RowData\RowDataInterface;
use Test\AppRunner\AppRunner;
use Test\Integration\Event\Container\TestDbEventsContainerConfigurator;

final class NetworkDaoTest extends AppRunner
{
    private NetworkDao $dao;
    private static int $networkId;

    public static function setUpBeforeClass(): void
    {
        self::bootstrapBeforeClass();
    }

    protected function setUp(): void
    {
        $container = (new EventsModelContainerConfigurator())->configure(
            (new TestDbEventsContainerConfigurator())->configure(new Container())
        );
        $this->dao = $container->get(NetworkDao::class);
    }

    public function testInsertGetUpdateDelete(): void
    {
        $row = new RowData();
        $row->offsetSet('title', 'NetworkDaoTest ' . uniqid());
        $row->offsetSet('icon', 'icon-test');
        $this->dao->insert($row);
        self::$networkId = (int) $this->dao->getLastInsertedPrimaryKey()['id'];

        $loaded = $this->dao->get(['id' => self::$networkId]);
        $this->assertInstanceOf(RowDataInterface::class, $loaded);
        $this->assertSame('icon-test', $loaded['icon']);

        $loaded['title'] = 'NetworkDaoTest updated';
        $this->assertTrue($this->dao->update($loaded));

        $this->setUp();
        $updated = $this->dao->get(['id' => self::$networkId]);
        $this->assertSame('NetworkDaoTest updated', $updated['title']);

        $this->assertTrue($this->dao->delete($updated));
        $this->setUp();
        $this->assertNull($this->dao->get(['id' => self::$networkId]));
    }
}
