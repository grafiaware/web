<?php
declare(strict_types=1);

namespace Test\Integration\Auth\Model\Dao;

use Auth\Model\Dao\CredentialsDao;
use Auth\Model\Dao\LoginDao;
use Auth\Model\Dao\RoleDao;
use Container\AuthContainerConfigurator;
use Container\AuthDbContainerConfigurator;
use Pes\Container\Container;
use Pes\Model\RowData\RowData;
use Pes\Model\RowData\RowDataInterface;
use Test\AppRunner\AppRunner;

final class CredentialsDaoTest extends AppRunner
{
    private const PREFIX = 'CredentialsDaoTest_';

    private Container $container;
    private CredentialsDao $dao;

    private static string $loginName;
    private static array $credentialsPk;

    public static function setUpBeforeClass(): void
    {
        self::bootstrapBeforeClass();
        self::cleanup();

        $container = self::authContainer();
        self::$loginName = self::PREFIX . uniqid();

        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        $loginDao->insert((function (): RowData {
            $row = new RowData();
            $row->offsetSet('login_name', self::$loginName);
            return $row;
        })());

        /** @var RoleDao $roleDao */
        $roleDao = $container->get(RoleDao::class);
        $roleRow = new RowData();
        $roleRow->offsetSet('role', self::PREFIX . 'role');
        $roleDao->insert($roleRow);
    }

    protected function setUp(): void
    {
        $this->container = self::authContainer();
        $this->dao = $this->container->get(CredentialsDao::class);
    }

    public static function tearDownAfterClass(): void
    {
        self::cleanup();
    }

    public function testInsertAndGet(): void
    {
        $row = new RowData();
        $row->import([
            'login_name_fk' => self::$loginName,
            'password_hash' => 'test-hash',
            'role_fk' => self::PREFIX . 'role',
        ]);
        $this->dao->insert($row);
        self::$credentialsPk = $this->dao->getLastInsertedPrimaryKey();

        $loaded = $this->dao->get(self::$credentialsPk);
        $this->assertInstanceOf(RowDataInterface::class, $loaded);
        $this->assertSame('test-hash', $loaded['password_hash']);
    }

    public function testUpdatePasswordHash(): void
    {
        $row = $this->dao->get(self::$credentialsPk);
        $row['password_hash'] = 'updated-hash';
        $this->assertTrue($this->dao->update($row));

        $this->setUp();
        $loaded = $this->dao->get(self::$credentialsPk);
        $this->assertSame('updated-hash', $loaded['password_hash']);
    }

    public function testDelete(): void
    {
        $row = $this->dao->get(self::$credentialsPk);
        $this->assertTrue($this->dao->delete($row));

        $this->setUp();
        $this->assertNull($this->dao->get(self::$credentialsPk));
    }

    private static function authContainer(): Container
    {
        return (new AuthContainerConfigurator())->configure(
            (new AuthDbContainerConfigurator())->configure(new Container())
        );
    }

    private static function cleanup(): void
    {
        $container = self::authContainer();

        /** @var CredentialsDao $credentialsDao */
        $credentialsDao = $container->get(CredentialsDao::class);
        foreach ($credentialsDao->find('login_name_fk LIKE :login_name_like', ['login_name_like' => self::PREFIX . '%']) as $row) {
            $credentialsDao->delete($row);
        }

        /** @var RoleDao $roleDao */
        $roleDao = $container->get(RoleDao::class);
        foreach ($roleDao->find('role LIKE :role_like', ['role_like' => self::PREFIX . '%']) as $row) {
            $roleDao->delete($row);
        }

        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        foreach ($loginDao->find('login_name LIKE :login_name_like', ['login_name_like' => self::PREFIX . '%']) as $row) {
            $loginDao->delete($row);
        }
    }
}
