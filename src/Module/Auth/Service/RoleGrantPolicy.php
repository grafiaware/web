<?php

namespace Auth\Service;

use Access\Enum\RoleEnum;
use Application\Feature\FeatureFlags;

/**
 * Kdo smí spravovat účty a přidělovat které role (A6, A9 – docs/vp/zadani/A9.md). Jediné místo s maticí rolí.
 *
 * Přepínač vp.sprava-roli vypnutý = matice z A6 (supervisor všechny role, events_administrator visitor a representative).
 * Zapnutý = role „grantor“: global_role_grantor (z konfigurace) a auth_role_grantor všechny role z tabulky role,
 * red_role_grantor editor, events_role_grantor visitor a representative.
 *
 * Pseudorole global_role_grantor se nepřiděluje nikdy (není v tabulce role).
 * Pravidla pro změnu role existujícího účtu (platí vždy, i s vypnutým přepínačem – oprava bezpečnostní chyby):
 * nikdo nemění vlastní roli; měnit smí jen ten, kdo smí přidělit současnou roli účtu (nebo účet roli nemá);
 * nová role musí být v jeho seznamu, nebo „žádná“.
 */
final class RoleGrantPolicy {

    public const FEATURE = 'vp.sprava-roli';

    /** null = všechny role z tabulky role */
    private const MATRIX_A6 = [
        RoleEnum::SUPERVISOR => null,
        RoleEnum::EVENTS_ADMINISTRATOR => [RoleEnum::VISITOR, RoleEnum::REPRESENTATIVE],
    ];

    private const MATRIX_A9 = [
        RoleEnum::GLOBAL_ROLE_GRANTOR => null,
        RoleEnum::AUTH_ROLE_GRANTOR => null,
        RoleEnum::RED_ROLE_GRANTOR => [RoleEnum::EDITOR],
        RoleEnum::EVENTS_ROLE_GRANTOR => [RoleEnum::VISITOR, RoleEnum::REPRESENTATIVE],
    ];

    /** Kdo smí měnit e-mail účtů (jen s přepínačem). */
    private const EMAIL_EDITORS = [RoleEnum::GLOBAL_ROLE_GRANTOR, RoleEnum::AUTH_ROLE_GRANTOR];

    /** Kdo smí upravovat tabulku role (stránka admin-role). */
    private const ROLE_TABLE_A6 = [RoleEnum::SUPERVISOR, RoleEnum::EVENTS_ADMINISTRATOR];
    private const ROLE_TABLE_A9 = [RoleEnum::GLOBAL_ROLE_GRANTOR, RoleEnum::AUTH_ROLE_GRANTOR];

    public function __construct(private bool $grantorsEnabled) {
    }

    public static function fromConfiguration(?FeatureFlags $flags = null): self {
        return new self(($flags ?? FeatureFlags::fromConfiguration())->isEnabled(self::FEATURE));
    }

    public function grantorsEnabled(): bool {
        return $this->grantorsEnabled;
    }

    /** Role, které spravují účty v aktuálním režimu. */
    public function managerRoles(): array {
        return array_keys($this->matrix());
    }

    public function canManageAccounts(?string $actorRole): bool {
        return $actorRole !== null && $actorRole !== '' && array_key_exists($actorRole, $this->matrix());
    }

    /**
     * Role, které smí $actorRole přidělit (podmnožina $allRoles, bez global_role_grantor).
     *
     * @param string[] $allRoles role z tabulky role
     * @return string[]
     */
    public function grantableRoles(?string $actorRole, array $allRoles): array {
        if (!$this->canManageAccounts($actorRole)) {
            return [];
        }
        $allRoles = array_values(array_filter($allRoles, fn($r) => $r !== RoleEnum::GLOBAL_ROLE_GRANTOR && $r !== ''));
        $limit = $this->matrix()[$actorRole];
        return $limit === null ? $allRoles : array_values(array_intersect($allRoles, $limit));
    }

    /**
     * Smí $actor měnit roli účtu $target se současnou rolí $targetRole?
     */
    public function canEditRoleOf(string $actorLogin, ?string $actorRole, string $targetLogin, ?string $targetRole, array $allRoles): bool {
        return $this->roleEditDenial($actorLogin, $actorRole, $targetLogin, $targetRole, $allRoles) === null;
    }

    /**
     * Důvod, proč změnu role nelze provést, nebo null = smí. $newRole null nebo '' = odebrat roli.
     */
    public function roleChangeDenial(string $actorLogin, ?string $actorRole, string $targetLogin, ?string $targetRole,
            ?string $newRole, array $allRoles): ?string {
        $denial = $this->roleEditDenial($actorLogin, $actorRole, $targetLogin, $targetRole, $allRoles);
        if ($denial !== null) {
            return $denial;
        }
        if ($newRole !== null && $newRole !== '' && !in_array($newRole, $this->grantableRoles($actorRole, $allRoles), true)) {
            return "Roli {$newRole} nemůžete přidělit.";
        }
        return null;
    }

    public function canEditEmail(?string $actorRole): bool {
        return $this->grantorsEnabled && in_array($actorRole, self::EMAIL_EDITORS, true);
    }

    public function canManageRoleTable(?string $actorRole): bool {
        return in_array($actorRole, $this->grantorsEnabled ? self::ROLE_TABLE_A9 : self::ROLE_TABLE_A6, true);
    }

    private function roleEditDenial(string $actorLogin, ?string $actorRole, string $targetLogin, ?string $targetRole, array $allRoles): ?string {
        if (!$this->canManageAccounts($actorRole)) {
            return 'Nemáte oprávnění měnit role účtů.';
        }
        if (mb_strtolower($actorLogin) === mb_strtolower($targetLogin)) {
            return 'Vlastní roli měnit nemůžete.';
        }
        if ($targetRole !== null && $targetRole !== '' && !in_array($targetRole, $this->grantableRoles($actorRole, $allRoles), true)) {
            return "Roli účtu s rolí {$targetRole} měnit nemůžete.";
        }
        return null;
    }

    private function matrix(): array {
        return $this->grantorsEnabled ? self::MATRIX_A9 : self::MATRIX_A6;
    }
}
