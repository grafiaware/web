<?php
declare(strict_types=1);

namespace Test\Unit\Firewall;

use Auth\Model\Entity\Credentials;
use Auth\Model\Entity\LoginAggregateFull;
use Firewall\Middleware\Rule\IsLogged;
use Pes\Application\AppInterface;
use Pes\Container\Container;
use PHPUnit\Framework\TestCase;
use Status\Model\Entity\Security;
use Status\Model\Repository\StatusSecurityRepo;

final class IsLoggedTest extends TestCase
{
    public function testNotGrantedWithoutSecurityStatus(): void
    {
        $rule = new IsLogged($this->appWithSecurity(null));

        $this->assertFalse($rule->granted());
    }

    public function testNotGrantedWithoutLoginAggregate(): void
    {
        $security = new Security();
        $rule = new IsLogged($this->appWithSecurity($security));

        $this->assertFalse($rule->granted());
    }

    public function testNotGrantedWithoutCredentials(): void
    {
        $security = new Security();
        $login = new LoginAggregateFull();
        $login->setLoginName('user@test.cz');
        $security->newContext($login);

        $rule = new IsLogged($this->appWithSecurity($security));

        $this->assertFalse($rule->granted());
    }

    public function testGrantedWithCredentials(): void
    {
        $security = new Security();
        $login = new LoginAggregateFull();
        $login->setLoginName('user@test.cz');
        $credentials = new Credentials();
        $credentials->setLoginNameFk('user@test.cz');
        $credentials->setPasswordHash('hash');
        $login->setCredentials($credentials);
        $security->newContext($login);

        $rule = new IsLogged($this->appWithSecurity($security));

        $this->assertTrue($rule->granted());
    }

    public function testRestrictMessage(): void
    {
        $rule = new IsLogged($this->appWithSecurity(null));

        $this->assertSame('Přístup mají pouze přihlášení uživatelé.', $rule->restrictMessage());
    }

    private function appWithSecurity(?Security $security): AppInterface
    {
        $statusSecurityRepo = $this->createMock(StatusSecurityRepo::class);
        $statusSecurityRepo->method('get')->willReturn($security);

        $container = new Container();
        $container->set(StatusSecurityRepo::class, $statusSecurityRepo);

        $app = $this->createMock(AppInterface::class);
        $app->method('getAppContainer')->willReturn($container);

        return $app;
    }
}
