<?php
declare(strict_types=1);

namespace Test\Integration\Red\Model\Dao;

use Pes\Container\Container;
use Pes\Model\RowData\RowData;
use Pes\Model\RowData\RowDataInterface;
use Red\Model\Dao\AssetDao;
use Test\AppRunner\AppRunner;
use Test\Integration\Red\Container\TestDbUpgradeContainerConfigurator;
use Test\Integration\Red\Container\TestHierarchyContainerConfigurator;

final class AssetDaoTest extends AppRunner
{
    private AssetDao $dao;
    private static int $assetId;

    public static function setUpBeforeClass(): void
    {
        self::bootstrapBeforeClass();
    }

    protected function setUp(): void
    {
        $container = (new TestHierarchyContainerConfigurator())->configure(
            (new TestDbUpgradeContainerConfigurator())->configure(new Container())
        );
        $this->dao = $container->get(AssetDao::class);
    }

    public function testInsertGetByFilepathAndDelete(): void
    {
        $filepath = '/tests/asset-' . uniqid() . '.png';
        $now = (new \DateTime())->format('Y-m-d H:i:s');
        $row = new RowData();
        $row->import([
            'filepath' => $filepath,
            'mime_type' => 'image/png',
            'editor_login_name' => 'test_editor',
            'created' => $now,
            'updated' => $now,
        ]);
        $this->dao->insert($row);
        self::$assetId = (int) $this->dao->getLastInsertedPrimaryKey()['id'];

        $loaded = $this->dao->getByFilepath($filepath);
        $this->assertInstanceOf(RowDataInterface::class, $loaded);
        $this->assertSame('image/png', $loaded['mime_type']);

        $this->assertTrue($this->dao->delete($loaded));
    }
}
