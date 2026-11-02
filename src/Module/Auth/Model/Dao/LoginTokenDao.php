<?php

namespace Auth\Model\Dao;

use Pes\Model\Dao\DaoEditAbstract;
use Pes\Model\Dao\DaoEditKeyDbVerifiedInterface;

/**
 * Jednorázové tokeny (přihlašovací odkaz, nastavení hesla) – tabulka login_token.
 *
 * V DB je jen SHA-256 hash tokenu (sloupec token_hash), token samotný je jen v e-mailu.
 * Viz docs/vp/zadani/A1.md.
 */
class LoginTokenDao extends DaoEditAbstract implements DaoEditKeyDbVerifiedInterface {

    public function getPrimaryKeyAttributes(): array {
        return ['token_hash'];
    }

    public function getAttributes(): array {
        return [
            'token_hash',
            'login_name_fk',
            'purpose',
            'created',
            'expires',
            'used',
        ];
    }

    public function getTableName(): string {
        return 'login_token';
    }

    /**
     * Počet tokenů daného účelu vytvořených pro login od zadaného času (omezení počtu odeslaných odkazů).
     */
    public function countCreatedSince(string $loginName, string $purpose, \DateTimeInterface $since): int {
        $stmt = $this->getPreparedStatement(
            "SELECT COUNT(*) AS cnt FROM login_token
             WHERE login_name_fk = :login_name_fk AND purpose = :purpose AND created >= :since");
        $stmt->bindValue(':login_name_fk', $loginName);
        $stmt->bindValue(':purpose', $purpose);
        $stmt->bindValue(':since', $since->format('Y-m-d H:i:s'));
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    /**
     * Atomicky označí token jako použitý. Vrací true jen tomu, kdo token použil první
     * (a jen pokud je platný a nepoužitý).
     */
    public function markUsed(string $tokenHash, string $purpose, \DateTimeInterface $now): bool {
        $stmt = $this->getPreparedStatement(
            "UPDATE login_token SET used = :now
             WHERE token_hash = :token_hash AND purpose = :purpose AND used IS NULL AND expires > :now2");
        $stmt->bindValue(':now', $now->format('Y-m-d H:i:s'));
        $stmt->bindValue(':now2', $now->format('Y-m-d H:i:s'));
        $stmt->bindValue(':token_hash', $tokenHash);
        $stmt->bindValue(':purpose', $purpose);
        $stmt->execute();
        return $stmt->rowCount() === 1;
    }
}
