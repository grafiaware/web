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
}
