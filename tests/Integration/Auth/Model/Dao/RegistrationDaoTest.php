<?php
declare(strict_types=1);

namespace Test\Integration\Auth\Model\Dao;

use Auth\Model\Dao\LoginDao;
use Auth\Model\Dao\RegistrationDao;
use Container\AuthContainerConfigurator;
use Container\AuthDbContainerConfigurator;
use Pes\Container\Container;
use Pes\Model\RowData\RowData;
use Pes\Model\RowData\RowDataInterface;
use Test\AppRunner\AppRunner;

final class RegistrationDaoTest extends AppRunner
{
    private const PREFIX = 'RegistrationDaoTest_';

    private RegistrationDao $dao;
    private static string $loginName;
    private static string $uid;

    public static function setUpBeforeClass(): void
    {
        self::bootstrapBeforeClass();
        self::cleanup();

        $container = self::authContainer();
        self::$loginName = self::PREFIX . uniqid();

        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        $loginRow = new RowData();
        $loginRow->offsetSet('login_name', self::$loginName);
        $loginDao->insert($loginRow);
    }

    protected function setUp(): void
    {
        $this->dao = self::authContainer()->get(RegistrationDao::class);
    }

    public static function tearDownAfterClass(): void
    {
        self::cleanup();
    }

    public function testInsertGeneratesUid(): void
    {
        $row = new RowData();
        $row->import([
            'login_name_fk' => self::$loginName,
            'password_hash' => 'plain-password',
            'email' => self::$loginName . '@example.cz',
            'email_time' => (new \DateTime())->format('Y-m-d H:i:s'),
        ]);
        $this->dao->insert($row);
        self::$uid = $row['uid'];

        $this->assertNotEmpty(self::$uid);
        $loaded = $this->dao->get(['login_name_fk' => self::$loginName]);
        $this->assertInstanceOf(RowDataInterface::class, $loaded);
        $this->assertSame(self::$uid, $loaded['uid']);
    }

    public function testFindByLoginNameFkViaFind(): void
    {
        $rows = $this->dao->find('login_name_fk = :login_name_fk', ['login_name_fk' => self::$loginName]);
        $this->assertCount(1, $rows);
        $this->assertSame(self::$uid, $rows[0]['uid']);
    }

    public function testDelete(): void
    {
        $row = $this->dao->get(['login_name_fk' => self::$loginName]);
        $this->assertTrue($this->dao->delete($row));

        $this->setUp();
        $this->assertNull($this->dao->get(['login_name_fk' => self::$loginName]));
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

        /** @var RegistrationDao $registrationDao */
        $registrationDao = $container->get(RegistrationDao::class);
        foreach ($registrationDao->find('login_name_fk LIKE :login_name_like', ['login_name_like' => self::PREFIX . '%']) as $row) {
            $registrationDao->delete($row);
        }

        /** @var LoginDao $loginDao */
        $loginDao = $container->get(LoginDao::class);
        foreach ($loginDao->find('login_name LIKE :login_name_like', ['login_name_like' => self::PREFIX . '%']) as $row) {
            $loginDao->delete($row);
        }
    }
}
