<?php

namespace Auth\Model\Dao;

use Pes\Model\Dao\DaoEditAbstract;
use Pes\Model\Dao\DaoEditKeyDbVerifiedInterface;

/**
 * F1 (#47, docs/vp/zadani/F1.md): zdroj registrace – tabulka registration_source.
 * Dao s vlastními dotazy (deklarovaná odchylka ADR 001 jako D1 a R1).
 */
class RegistrationSourceDao extends DaoEditAbstract implements DaoEditKeyDbVerifiedInterface {

    public function getPrimaryKeyAttributes(): array {
        return ['login_name_fk'];
    }

    public function getAttributes(): array {
        return ['login_name_fk', 'link_code', 'channel', 'utm_source', 'utm_medium', 'utm_campaign', 'landing_at', 'created'];
    }

    public function getTableName(): string {
        return 'registration_source';
    }

    /** Zapíše zdroj, jen pokud účet zdroj ještě nemá (první registrace platí). */
    public function insertIgnore(string $loginName, array $v): void {
        $stmt = $this->getPreparedStatement(
            "INSERT IGNORE INTO registration_source (login_name_fk, link_code, channel, utm_source, utm_medium, utm_campaign, landing_at)
             VALUES (:login_name_fk, :link_code, :channel, :utm_source, :utm_medium, :utm_campaign, :landing_at)");
        $stmt->bindValue(':login_name_fk', $loginName);
        foreach (['link_code', 'channel', 'utm_source', 'utm_medium', 'utm_campaign', 'landing_at'] as $k) {
            $stmt->bindValue(':' . $k, $v[$k] ?? null, ($v[$k] ?? null) === null ? \PDO::PARAM_NULL : \PDO::PARAM_STR);
        }
        $stmt->execute();
    }

    /**
     * Počty registrací a potvrzených registrací (účet má credentials) podle zdroje za období (čas vzniku záznamu zdroje).
     *
     * @return array<int, array{link_code: ?string, channel: ?string, utm_source: ?string, utm_campaign: ?string, registered: int, confirmed: int}>
     */
    public function countsBySource(?string $from, ?string $to): array {
        $sql = "SELECT s.link_code, s.channel, s.utm_source, s.utm_campaign,
                       COUNT(*) AS registered, SUM(CASE WHEN c.login_name_fk IS NOT NULL THEN 1 ELSE 0 END) AS confirmed
                FROM registration_source s
                LEFT JOIN credentials c ON c.login_name_fk = s.login_name_fk
                WHERE (:date_from IS NULL OR s.created >= :date_from2) AND (:date_to IS NULL OR s.created < :date_to2)
                GROUP BY s.link_code, s.channel, s.utm_source, s.utm_campaign";
        $stmt = $this->getPreparedStatement($sql);
        foreach (['date_from' => $from, 'date_from2' => $from, 'date_to' => $to, 'date_to2' => $to] as $k => $v) {
            $stmt->bindValue(':' . $k, $v, $v === null ? \PDO::PARAM_NULL : \PDO::PARAM_STR);
        }
        $stmt->execute();
        $ret = [];
        foreach ($stmt->fetchAll() as $row) {
            $row = $row instanceof \ArrayObject ? $row->getArrayCopy() : (array) $row;
            $row['registered'] = (int) $row['registered'];
            $row['confirmed'] = (int) $row['confirmed'];
            $ret[] = $row;
        }
        return $ret;
    }
}
