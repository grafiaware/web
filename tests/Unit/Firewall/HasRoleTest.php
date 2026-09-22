<?php
declare(strict_types=1);

namespace Test\Unit\Firewall;

use Access\Enum\RoleEnum;
use Auth\Model\Entity\Credentials;
use Auth\Model\Entity\LoginAggregateFull;
use Firewall\Middleware\Rule\HasRole;
use Pes\Application\AppInterface;
use Pes\Container\Container;
use PHPUnit\Framework\TestCase;
use Status\Model\Entity\Security;
use Status\Model\Repository\StatusSecurityRepo;

final class HasRoleTest extends TestCase
{
    public function testGrantedForMatchingRole(): void
    {
        $rule = new HasRole($this->appWithRole(RoleEnum::EDITOR), RoleEnum::EDITOR);

        $this->assertTrue($rule->granted());
    }

    public function testNotGrantedForDifferentRole(): void
    {
        $rule = new HasRole($this->appWithRole(RoleEnum::VISITOR), RoleEnum::EDITOR);

        $this->assertFalse($rule->granted());
    }

    public function testNotGrantedWithoutSecurityContext(): void
    {
        $rule = new HasRole($this->appWithRole(null), RoleEnum::EDITOR);

        $this->assertFalse($rule->granted());
    }

    public function testRestrictMessage(): void
    {
        $rule = new HasRole($this->appWithRole(null), RoleEnum::EDITOR);

        $this->assertSame('Přístup mají uživatelé s určenou rolí.', $rule->restrictMessage());
    }

    private function appWithRole(?string $role): AppInterface
    {
        $security = null;
        if ($role !== null) {
            $security = new Security();
            $login = new LoginAggregateFull();
            $login->setLoginName('user@test.cz');
            $credentials = new Credentials();
            $credentials->setLoginNameFk('user@test.cz');
            $credentials->setPasswordHash('hash');
            $credentials->setRoleFk($role);
            $login->setCredentials($credentials);
            $security->newContext($login);
        }

        $statusSecurityRepo = $this->createMock(StatusSecurityRepo::class);
        $statusSecurityRepo->method('get')->willReturn($security);

        $container = new Container();
        $container->set(StatusSecurityRepo::class, $statusSecurityRepo);

        $app = $this->createMock(AppInterface::class);
        $app->method('getAppContainer')->willReturn($container);

        return $app;
    }
}
