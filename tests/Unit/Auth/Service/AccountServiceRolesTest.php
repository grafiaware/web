<?php
declare(strict_types=1);

namespace Test\Unit\Auth\Service;

use Access\Enum\RoleEnum;
use Auth\Service\AccountService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * A6: kdo smí zakládat účty s jakou rolí, pravidlo přihlašovacího jména (docs/vp/zadani/A6.md).
 */
final class AccountServiceRolesTest extends TestCase
{
    private const ALL = ['editor', 'events_administrator', 'representative', 'supervisor', 'visitor'];

    public function testSupervisorMayCreateAnyRole(): void
    {
        $this->assertSame(self::ALL, AccountService::allowedRoles(RoleEnum::SUPERVISOR, self::ALL));
    }

    public function testEventsAdministratorOnlyVisitorAndRepresentative(): void
    {
        $this->assertSame(['representative', 'visitor'], AccountService::allowedRoles(RoleEnum::EVENTS_ADMINISTRATOR, self::ALL));
    }

    public static function notAllowed(): array
    {
        return [[null], [RoleEnum::VISITOR], [RoleEnum::REPRESENTATIVE], [RoleEnum::EDITOR], ['']];
    }

    #[DataProvider('notAllowed')]
    public function testOthersMayNotCreate(?string $role): void
    {
        $this->assertFalse(AccountService::canCreateAccounts($role));
        $this->assertSame([], AccountService::allowedRoles($role, self::ALL));
    }

    public static function loginNames(): array
    {
        return [
            'platné' => ['test_repre01', true],
            'krátké' => ['abcd', false],
            'diakritika' => ['jiří_novák', false],
            'zavináč' => ['jan@firma', false],
            'mezera' => ['jan novak', false],
            '50 znaků' => [str_repeat('a', 50), true],
            '51 znaků' => [str_repeat('a', 51), false],
        ];
    }

    #[DataProvider('loginNames')]
    public function testLoginNamePattern(string $name, bool $valid): void
    {
        $this->assertSame($valid, (bool) preg_match(AccountService::LOGIN_NAME_PATTERN, $name));
    }
}
