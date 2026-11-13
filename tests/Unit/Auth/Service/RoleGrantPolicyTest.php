<?php
declare(strict_types=1);

namespace Test\Unit\Auth\Service;

use Access\Enum\RoleEnum;
use Auth\Service\RoleGrantPolicy;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * A6/A9: kdo smí přidělit kterou roli, pravidla pro změnu role existujícího účtu (docs/vp/zadani/A9.md).
 */
final class RoleGrantPolicyTest extends TestCase
{
    private const A6_ROLES = ['editor', 'events_administrator', 'representative', 'supervisor', 'visitor'];
    private const A9_ROLES = ['auth_role_grantor', 'editor', 'events_administrator', 'events_role_grantor', 'red_administrator',
        'red_role_grantor', 'representative', 'supervisor', 'visitor'];

    // --- vypnutý přepínač: matice A6 ---

    public function testA6SupervisorAllEventsAdminVisitorRepresentative(): void
    {
        $p = new RoleGrantPolicy(false);
        $this->assertSame(self::A6_ROLES, $p->grantableRoles(RoleEnum::SUPERVISOR, self::A6_ROLES));
        $this->assertSame(['representative', 'visitor'], $p->grantableRoles(RoleEnum::EVENTS_ADMINISTRATOR, self::A6_ROLES));
        $this->assertFalse($p->canManageAccounts(RoleEnum::AUTH_ROLE_GRANTOR), 'grantor role bez přepínače nic nesmí');
        $this->assertFalse($p->canEditEmail(RoleEnum::SUPERVISOR));
    }

    /** Oprava bezpečnostní chyby: events_administrator si (ani jinému) nenastaví supervisor. */
    public function testA6EventsAdminCannotEscalate(): void
    {
        $p = new RoleGrantPolicy(false);
        $this->assertNotNull($p->roleChangeDenial('eadmin', RoleEnum::EVENTS_ADMINISTRATOR, 'jiny', RoleEnum::VISITOR, RoleEnum::SUPERVISOR, self::A6_ROLES));
        $this->assertNotNull($p->roleChangeDenial('eadmin', RoleEnum::EVENTS_ADMINISTRATOR, 'eadmin', RoleEnum::EVENTS_ADMINISTRATOR, RoleEnum::SUPERVISOR, self::A6_ROLES));
        $this->assertNotNull($p->roleChangeDenial('eadmin', RoleEnum::EVENTS_ADMINISTRATOR, 'red', RoleEnum::EDITOR, null, self::A6_ROLES),
            'roli editora neodebere');
        $this->assertNull($p->roleChangeDenial('eadmin', RoleEnum::EVENTS_ADMINISTRATOR, 'jiny', RoleEnum::VISITOR, RoleEnum::REPRESENTATIVE, self::A6_ROLES));
        $this->assertNull($p->roleChangeDenial('eadmin', RoleEnum::EVENTS_ADMINISTRATOR, 'jiny', null, null, self::A6_ROLES));
    }

    // --- zapnutý přepínač: role grantor ---

    public static function a9Matrix(): array
    {
        $all = array_values(array_diff(self::A9_ROLES, []));
        return [
            'global' => [RoleEnum::GLOBAL_ROLE_GRANTOR, $all],
            'auth' => [RoleEnum::AUTH_ROLE_GRANTOR, $all],
            'red' => [RoleEnum::RED_ROLE_GRANTOR, ['editor']],
            'events' => [RoleEnum::EVENTS_ROLE_GRANTOR, ['representative', 'visitor']],
            'supervisor nespravuje účty' => [RoleEnum::SUPERVISOR, []],
            'events_administrator nespravuje účty' => [RoleEnum::EVENTS_ADMINISTRATOR, []],
            'red_administrator' => [RoleEnum::RED_ADMINISTRATOR, []],
            'nepřihlášený' => [null, []],
        ];
    }

    #[DataProvider('a9Matrix')]
    public function testA9GrantableRoles(?string $actor, array $expected): void
    {
        $this->assertSame($expected, (new RoleGrantPolicy(true))->grantableRoles($actor, self::A9_ROLES));
    }

    public function testGlobalRoleGrantorIsNeverGrantable(): void
    {
        $p = new RoleGrantPolicy(true);
        $withGlobal = array_merge(self::A9_ROLES, [RoleEnum::GLOBAL_ROLE_GRANTOR]);
        $this->assertNotContains(RoleEnum::GLOBAL_ROLE_GRANTOR, $p->grantableRoles(RoleEnum::GLOBAL_ROLE_GRANTOR, $withGlobal));
        $this->assertNotNull($p->roleChangeDenial('g', RoleEnum::GLOBAL_ROLE_GRANTOR, 'x', null, RoleEnum::GLOBAL_ROLE_GRANTOR, $withGlobal));
    }

    public function testNobodyChangesOwnRoleCaseInsensitive(): void
    {
        $p = new RoleGrantPolicy(true);
        $this->assertSame('Vlastní roli měnit nemůžete.',
            $p->roleChangeDenial('Spravce', RoleEnum::AUTH_ROLE_GRANTOR, 'spravce', RoleEnum::AUTH_ROLE_GRANTOR, null, self::A9_ROLES));
    }

    /** Varianta B: auth_role_grantor jmenuje dalšího a ten smí odebrat roli původnímu. */
    public function testAuthGrantorAppointsAndRemovesAnother(): void
    {
        $p = new RoleGrantPolicy(true);
        $this->assertNull($p->roleChangeDenial('prvni', RoleEnum::AUTH_ROLE_GRANTOR, 'druhy', null, RoleEnum::AUTH_ROLE_GRANTOR, self::A9_ROLES));
        $this->assertNull($p->roleChangeDenial('druhy', RoleEnum::AUTH_ROLE_GRANTOR, 'prvni', RoleEnum::AUTH_ROLE_GRANTOR, null, self::A9_ROLES));
        $this->assertNull($p->roleChangeDenial('prvni', RoleEnum::AUTH_ROLE_GRANTOR, 'red', null, RoleEnum::SUPERVISOR, self::A9_ROLES));
    }

    /** Příklad ze zadání: events_role_grantor neupraví editora; účtu bez role / visitor / representative ano. */
    public function testEventsGrantorExample(): void
    {
        $p = new RoleGrantPolicy(true);
        $this->assertFalse($p->canEditRoleOf('eg', RoleEnum::EVENTS_ROLE_GRANTOR, 'ed', RoleEnum::EDITOR, self::A9_ROLES));
        foreach ([null, RoleEnum::VISITOR, RoleEnum::REPRESENTATIVE] as $current) {
            $this->assertTrue($p->canEditRoleOf('eg', RoleEnum::EVENTS_ROLE_GRANTOR, 'u', $current, self::A9_ROLES));
        }
        $this->assertNotNull($p->roleChangeDenial('eg', RoleEnum::EVENTS_ROLE_GRANTOR, 'u', RoleEnum::VISITOR, RoleEnum::EDITOR, self::A9_ROLES));
        $this->assertNull($p->roleChangeDenial('eg', RoleEnum::EVENTS_ROLE_GRANTOR, 'u', RoleEnum::VISITOR, null, self::A9_ROLES));
    }

    public function testEmailAndRoleTableEditors(): void
    {
        $p = new RoleGrantPolicy(true);
        $this->assertTrue($p->canEditEmail(RoleEnum::AUTH_ROLE_GRANTOR));
        $this->assertTrue($p->canEditEmail(RoleEnum::GLOBAL_ROLE_GRANTOR));
        $this->assertFalse($p->canEditEmail(RoleEnum::EVENTS_ROLE_GRANTOR));
        $this->assertTrue($p->canManageRoleTable(RoleEnum::AUTH_ROLE_GRANTOR));
        $this->assertFalse($p->canManageRoleTable(RoleEnum::EVENTS_ADMINISTRATOR));
        $this->assertTrue((new RoleGrantPolicy(false))->canManageRoleTable(RoleEnum::EVENTS_ADMINISTRATOR));
    }

    /** A3: výpis nepotvrzených registrací a úklid – bez A9 supervisor (úklid) a events_administrator (výpis). */
    public function testUnconfirmedA6(): void
    {
        $p = new RoleGrantPolicy(false);
        $this->assertTrue($p->canViewUnconfirmed(RoleEnum::SUPERVISOR));
        $this->assertTrue($p->canViewUnconfirmed(RoleEnum::EVENTS_ADMINISTRATOR));
        $this->assertFalse($p->canViewUnconfirmed(RoleEnum::AUTH_ROLE_GRANTOR));
        $this->assertTrue($p->canCleanUnconfirmed(RoleEnum::SUPERVISOR));
        $this->assertFalse($p->canCleanUnconfirmed(RoleEnum::EVENTS_ADMINISTRATOR));
        $this->assertFalse($p->canCleanUnconfirmed(null));
    }

    /** A3 + A9: výpis global, auth a events_role_grantor; úklid jen global a auth_role_grantor. */
    public function testUnconfirmedA9(): void
    {
        $p = new RoleGrantPolicy(true);
        foreach ([RoleEnum::GLOBAL_ROLE_GRANTOR, RoleEnum::AUTH_ROLE_GRANTOR, RoleEnum::EVENTS_ROLE_GRANTOR] as $r) {
            $this->assertTrue($p->canViewUnconfirmed($r), $r);
        }
        foreach ([RoleEnum::SUPERVISOR, RoleEnum::EVENTS_ADMINISTRATOR, RoleEnum::RED_ROLE_GRANTOR, null] as $r) {
            $this->assertFalse($p->canViewUnconfirmed($r), (string) $r);
            $this->assertFalse($p->canCleanUnconfirmed($r), (string) $r);
        }
        $this->assertTrue($p->canCleanUnconfirmed(RoleEnum::AUTH_ROLE_GRANTOR));
        $this->assertTrue($p->canCleanUnconfirmed(RoleEnum::GLOBAL_ROLE_GRANTOR));
        $this->assertFalse($p->canCleanUnconfirmed(RoleEnum::EVENTS_ROLE_GRANTOR));
    }
}
