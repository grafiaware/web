<?php

namespace Build\Migration;

use PDO;

/**
 * Žurnál aplikovaných migrací (tabulka schema_migrations).
 */
final class MigrationJournal
{
    public const TABLE = 'schema_migrations';

    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function ensureTable(): void
    {
        $driver = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($driver === 'sqlite') {
            $this->pdo->exec(
                'CREATE TABLE IF NOT EXISTS ' . self::TABLE . ' (
                    version TEXT NOT NULL PRIMARY KEY,
                    applied_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
                )'
            );
            return;
        }

        $this->pdo->exec(
            'CREATE TABLE IF NOT EXISTS `' . self::TABLE . '` (
                `version` VARCHAR(64) NOT NULL,
                `applied_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`version`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8'
        );
    }

    /**
     * Žurnál ještě neexistuje nebo nemá žádný řádek — první init/baseline/migrate.
     * Nevolá ensureTable (nevytváří tabulku).
     */
    public function isVirgin(): bool
    {
        if (!$this->tableExists()) {
            return true;
        }
        $stmt = $this->pdo->query('SELECT 1 FROM ' . self::TABLE . ' LIMIT 1');
        if ($stmt === false) {
            return true;
        }

        return $stmt->fetchColumn() === false;
    }

    public function tableExists(): bool
    {
        $driver = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($driver === 'sqlite') {
            $stmt = $this->pdo->prepare(
                "SELECT 1 FROM sqlite_master WHERE type = 'table' AND name = :name LIMIT 1"
            );
            $stmt->execute(['name' => self::TABLE]);

            return (bool) $stmt->fetchColumn();
        }

        // MySQL 5.6 / 8 — information_schema (bez závislosti na DATABASE())
        $stmt = $this->pdo->prepare(
            'SELECT 1 FROM information_schema.TABLES
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :name AND TABLE_TYPE = \'BASE TABLE\'
             LIMIT 1'
        );
        $stmt->execute(['name' => self::TABLE]);

        return (bool) $stmt->fetchColumn();
    }

    /**
     * @return list<string>
     */
    public function getAppliedVersions(): array
    {
        if ($this->isVirgin()) {
            // Tabulka ještě není / je prázdná — nevynucujeme CREATE zde;
            // ensureTable volá runner až po strukturální kontrole (BaselineStructureGate).
            if (!$this->tableExists()) {
                return [];
            }
        } else {
            $this->ensureTable();
        }

        if (!$this->tableExists()) {
            return [];
        }

        $stmt = $this->pdo->query('SELECT version FROM ' . self::TABLE . ' ORDER BY version ASC');
        if ($stmt === false) {
            return [];
        }
        $versions = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $versions[] = (string) $row['version'];
        }

        return $versions;
    }

    public function record(string $version): void
    {
        $this->ensureTable();
        $stmt = $this->pdo->prepare(
            'INSERT INTO ' . self::TABLE . ' (version) VALUES (:version)'
        );
        $stmt->execute(['version' => $version]);
    }

    public function isApplied(string $version): bool
    {
        $this->ensureTable();
        $stmt = $this->pdo->prepare(
            'SELECT 1 FROM ' . self::TABLE . ' WHERE version = :version LIMIT 1'
        );
        $stmt->execute(['version' => $version]);

        return (bool) $stmt->fetchColumn();
    }
}
