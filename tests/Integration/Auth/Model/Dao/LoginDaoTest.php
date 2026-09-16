<?php
declare(strict_types=1);

namespace Test\Integration\Auth\Model\Dao;

use Access\Enum\RoleEnum;
use Auth\Model\Dao\LoginDao;
use Container\AuthContainerConfigurator;
use Container\AuthDbContainerConfigurator;
use Pes\Container\Container;
use Pes\Model\RowData\RowData;
use Pes\Model\RowData\RowDataInterface;
use Test\AppRunner\AppRunner;

final class LoginDaoTest extends AppRunner
{
    private const PREFIX = 'AuthLoginDaoTest_';

    private LoginDao $dao;
    private static string $loginName;

    public static function setUpBeforeClass(): void
    {
        self::bootstrapBeforeClass();
        self::cleanup();
    }

    protected function setUp(): void
    {
        $container = (new AuthContainerConfigurator())->configure(
            (new AuthDbContainerConfigurator())->configure(new Container())
        );
        $this->dao = $container->get(LoginDao::class);
    }

    public static function tearDownAfterClass(): void
    {
        self::cleanup();
    }

    public function testInsertGetDelete(): void
    {
        self::$loginName = self::PREFIX . uniqid();
        $row = new RowData();
        $row->offsetSet('login_name', self::$loginName);
        $this->dao->insert($row);

        $loaded = $this->dao->get(['login_name' => self::$loginName]);
        $this->assertInstanceOf(RowDataInterface::class, $loaded);
        $this->assertSame(self::$loginName, $loaded['login_name']);

        $this->assertTrue($this->dao->delete($loaded));
        $this->setUp();
        $this->assertNull($this->dao->get(['login_name' => self::$loginName]));
    }

    private static function cleanup(): void
    {
        $container = (new AuthContainerConfigurator())->configure(
            (new AuthDbContainerConfigurator())->configure(new Container())
        );
        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        foreach ($loginDao->find('login_name LIKE :login_name_like', ['login_name_like' => self::PREFIX . '%']) as $row) {
            $loginDao->delete($row);
        }
    }
}
