<?php
declare(strict_types=1);

namespace Test\Unit\Auth\Service;

use Auth\Service\AccountService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * A6: pravidlo přihlašovacího jména (docs/vp/zadani/A6.md). Kdo smí zakládat účty s jakou rolí: RoleGrantPolicyTest (A9).
 */
final class AccountServiceRolesTest extends TestCase
{
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
