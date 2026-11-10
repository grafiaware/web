<?php

namespace Auth\Model\Dao;

use Pes\Model\Dao\DaoEditAbstract;
use Pes\Model\Dao\DaoEditKeyDbVerifiedInterface;

/**
 * Osobní údaje účtu, které nejsou klíčem ani přihlašovacím údajem – tabulka login_profile (A8, docs/vp/zadani/A8.md).
 */
class LoginProfileDao extends DaoEditAbstract implements DaoEditKeyDbVerifiedInterface {

    public function getPrimaryKeyAttributes(): array {
        return ['login_name_fk'];
    }

    public function getAttributes(): array {
        return [
            'login_name_fk',
            'display_name',
            'updated',
        ];
    }

    public function getTableName(): string {
        return 'login_profile';
    }

    public function getDisplayName(string $loginName): ?string {
        $stmt = $this->getPreparedStatement("SELECT display_name FROM login_profile WHERE login_name_fk = :login_name_fk");
        $stmt->bindValue(':login_name_fk', $loginName);
        $stmt->execute();
        $value = $stmt->fetchColumn();
        return ($value === false || $value === null || $value === '') ? null : (string) $value;
    }

    /**
     * Uloží (založí nebo přepíše) osobní jméno; null smaže.
     */
    public function setDisplayName(string $loginName, ?string $displayName): void {
        $stmt = $this->getPreparedStatement(
            "INSERT INTO login_profile (login_name_fk, display_name) VALUES (:login_name_fk, :display_name)
             ON DUPLICATE KEY UPDATE display_name = :display_name2");
        $stmt->bindValue(':login_name_fk', $loginName);
        $stmt->bindValue(':display_name', $displayName);
        $stmt->bindValue(':display_name2', $displayName);
        $stmt->execute();
    }

    // ---- A9 (docs/vp/zadani/A9.md): typ účtu, poslední přihlášení, zpomalené přihlašování ----
    // Vlastní SQL – sloupce nejsou v getAttributes(), aby DAO fungovalo i před migrací A9.
    // Zápisy zachovávají sloupec updated (ten patří osobnímu jménu).

    public function getOrigin(string $loginName): ?string {
        $stmt = $this->getPreparedStatement("SELECT origin FROM login_profile WHERE login_name_fk = :login_name_fk");
        $stmt->bindValue(':login_name_fk', $loginName);
        $stmt->execute();
        $value = $stmt->fetchColumn();
        return ($value === false || $value === null || $value === '') ? null : (string) $value;
    }

    public function setOrigin(string $loginName, string $origin): void {
        $stmt = $this->getPreparedStatement(
            "INSERT INTO login_profile (login_name_fk, origin) VALUES (:login_name_fk, :origin)
             ON DUPLICATE KEY UPDATE origin = :origin2, updated = updated");
        $stmt->bindValue(':login_name_fk', $loginName);
        $stmt->bindValue(':origin', $origin);
        $stmt->bindValue(':origin2', $origin);
        $stmt->execute();
    }

    /**
     * Typy účtů a poslední přihlášení pro výpis v administraci.
     *
     * @return array<string, array{origin: ?string, lastLogin: ?string, displayName: ?string}>
     */
    public function findProfiles(): array {
        $stmt = $this->getPreparedStatement("SELECT login_name_fk, origin, last_login, display_name FROM login_profile");
        $stmt->execute();
        $result = [];
        while (($row = $stmt->fetch(\PDO::FETCH_ASSOC)) !== false) {
            $result[(string) $row['login_name_fk']] = [
                'origin' => $row['origin'] ?: null,
                'lastLogin' => $row['last_login'] ?: null,
                'displayName' => ($row['display_name'] ?? '') === '' ? null : (string) $row['display_name'],
            ];
        }
        return $result;
    }

    /**
     * Stav zpomaleného přihlašování.
     *
     * @return array{failedCount: int, lockedUntil: ?string}
     */
    public function getThrottle(string $loginName): array {
        $stmt = $this->getPreparedStatement("SELECT failed_count, locked_until FROM login_profile WHERE login_name_fk = :login_name_fk");
        $stmt->bindValue(':login_name_fk', $loginName);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return ['failedCount' => (int) ($row['failed_count'] ?? 0), 'lockedUntil' => ($row['locked_until'] ?? null) ?: null];
    }

    /** Neúspěšný pokus: zvýší počítadlo (atomicky v DB) a vrátí nový počet. */
    public function incrementFailed(string $loginName, string $now): int {
        $stmt = $this->getPreparedStatement(
            "INSERT INTO login_profile (login_name_fk, failed_count, failed_last) VALUES (:login_name_fk, 1, :now)
             ON DUPLICATE KEY UPDATE failed_count = failed_count + 1, failed_last = :now2, updated = updated");
        $stmt->bindValue(':login_name_fk', $loginName);
        $stmt->bindValue(':now', $now);
        $stmt->bindValue(':now2', $now);
        $stmt->execute();
        return $this->getThrottle($loginName)['failedCount'];
    }

    public function setLockedUntil(string $loginName, ?string $lockedUntil): void {
        $stmt = $this->getPreparedStatement(
            "UPDATE login_profile SET locked_until = :locked_until, updated = updated WHERE login_name_fk = :login_name_fk");
        $stmt->bindValue(':login_name_fk', $loginName);
        $stmt->bindValue(':locked_until', $lockedUntil);
        $stmt->execute();
    }

    /** Úspěšné přihlášení: čas posledního přihlášení, vynulování počítadla. */
    public function recordSuccess(string $loginName, string $now): void {
        $stmt = $this->getPreparedStatement(
            "INSERT INTO login_profile (login_name_fk, last_login) VALUES (:login_name_fk, :now)
             ON DUPLICATE KEY UPDATE last_login = :now2, failed_count = 0, failed_last = NULL, locked_until = NULL, updated = updated");
        $stmt->bindValue(':login_name_fk', $loginName);
        $stmt->bindValue(':now', $now);
        $stmt->bindValue(':now2', $now);
        $stmt->execute();
    }
}
