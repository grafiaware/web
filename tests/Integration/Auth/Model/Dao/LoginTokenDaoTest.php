<?php
declare(strict_types=1);

namespace Test\Integration\Auth\Model\Dao;

use Auth\Model\Dao\CredentialsDao;
use Auth\Model\Dao\LoginDao;
use Auth\Model\Dao\LoginTokenDao;
use Auth\Model\Dao\RegistrationDao;
use Auth\Service\LoginTokenService;
use Container\AuthContainerConfigurator;
use Container\AuthDbContainerConfigurator;
use Pes\Container\Container;
use Pes\Model\RowData\RowData;
use Test\AppRunner\AppRunner;

/**
 * A1+A2: tabulka login_token (migrace auth 20261102000001) a vyhledání registrací podle e-mailu.
 */
final class LoginTokenDaoTest extends AppRunner
{
    private const PREFIX = 'LoginTokenDaoTest_';

    private static string $loginA;
    private static string $loginB;
    private static string $email;

    public static function setUpBeforeClass(): void
    {
        self::bootstrapBeforeClass();
        self::cleanup();
        $c = self::authContainer();
        self::$loginA = self::PREFIX . 'a' . uniqid();
        self::$loginB = self::PREFIX . 'b' . uniqid();
        self::$email = strtolower(self::PREFIX) . uniqid() . '@example.test';

        foreach ([self::$loginA, self::$loginB] as $login) {
            $row = new RowData();
            $row->offsetSet('login_name', $login);
            $c->get(LoginDao::class)->insert($row);
            $reg = new RowData();
            $reg->import(['login_name_fk' => $login, 'password_hash' => '', 'email' => self::$email]);
            $c->get(RegistrationDao::class)->insert($reg);
        }
        // potvrzený je jen účet A
        $cred = new RowData();
        $cred->import(['login_name_fk' => self::$loginA, 'password_hash' => '', 'role_fk' => 'visitor']);
        $c->get(CredentialsDao::class)->insert($cred);
    }

    public static function tearDownAfterClass(): void
    {
        self::cleanup();
    }

    public function testFindLoginNamesByEmail(): void
    {
        /** @var RegistrationDao $dao */
        $dao = self::authContainer()->get(RegistrationDao::class);
        $all = $dao->findLoginNamesByEmail(' ' . strtoupper(self::$email) . ' ', false);
        sort($all);
        $expected = [self::$loginA, self::$loginB];
        sort($expected);
        $this->assertSame($expected, $all);
        $this->assertSame([self::$loginA], $dao->findLoginNamesByEmail(self::$email, true));
        $this->assertSame([], $dao->findLoginNamesByEmail('nikdo-' . uniqid() . '@example.test', false));
    }

    public function testIssueConsumeAndCount(): void
    {
        $c = self::authContainer();
        /** @var LoginTokenService $service */
        $service = $c->get(LoginTokenService::class);
        $token = $service->issue(self::$loginA, LoginTokenService::PURPOSE_LOGIN, 30);

        /** @var LoginTokenDao $dao */
        $dao = $c->get(LoginTokenDao::class);
        $row = $dao->get(['token_hash' => hash('sha256', $token)]);
        $this->assertNotNull($row);
        $this->assertSame(self::$loginA, $row['login_name_fk']);
        $this->assertNull($row['used']);

        $this->assertSame(1, $dao->countCreatedSince(self::$loginA, LoginTokenService::PURPOSE_LOGIN, new \DateTimeImmutable('-1 minute')));
        $this->assertSame(self::$loginA, $service->consume($token, LoginTokenService::PURPOSE_LOGIN));
        $this->assertNull($service->consume($token, LoginTokenService::PURPOSE_LOGIN));
    }

    public function testDeletingLoginCascadesTokens(): void
    {
        $c = self::authContainer();
        $c->get(LoginTokenService::class)->issue(self::$loginB, LoginTokenService::PURPOSE_LOGIN, 30);
        $dao = $c->get(LoginTokenDao::class);
        $this->assertCount(1, $dao->find('login_name_fk = :l', ['l' => self::$loginB]));

        $reg = $c->get(RegistrationDao::class)->get(['login_name_fk' => self::$loginB]);
        $c->get(RegistrationDao::class)->delete($reg);
        $login = $c->get(LoginDao::class)->get(['login_name' => self::$loginB]);
        $c->get(LoginDao::class)->delete($login);
        $this->assertCount(0, self::authContainer()->get(LoginTokenDao::class)->find('login_name_fk = :l', ['l' => self::$loginB]));
    }

    private static function authContainer(): Container
    {
        return (new AuthContainerConfigurator())->configure(
            (new AuthDbContainerConfigurator())->configure(new Container())
        );
    }

    private static function cleanup(): void
    {
        $c = self::authContainer();
        $like = ['l' => self::PREFIX . '%'];
        foreach ($c->get(LoginTokenDao::class)->find('login_name_fk LIKE :l', $like) as $row) {
            $c->get(LoginTokenDao::class)->delete($row);
        }
        foreach ($c->get(CredentialsDao::class)->find('login_name_fk LIKE :l', $like) as $row) {
            $c->get(CredentialsDao::class)->delete($row);
        }
        foreach ($c->get(RegistrationDao::class)->find('login_name_fk LIKE :l', $like) as $row) {
            $c->get(RegistrationDao::class)->delete($row);
        }
        foreach ($c->get(LoginDao::class)->find('login_name LIKE :l', $like) as $row) {
            $c->get(LoginDao::class)->delete($row);
        }
    }
}
