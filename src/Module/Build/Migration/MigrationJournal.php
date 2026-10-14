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
     * @return list<string>
     */
    public function getAppliedVersions(): array
    {
        $this->ensureTable();
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
