<?php

namespace Build\Migration;

use PDO;
use Pes\Database\Manipulator\Manipulator;
use RuntimeException;

/**
 * Rebuild: drop tabulek → import baseline dump 2025 → baseline žurnál → migrate pending.
 * Žádné auto-spouštění z HTTP; určeno pro CLI.
 */
final class DatabaseRebuilder
{
    public function __construct(
        private readonly MigrationConnectionFactory $connections,
        private readonly ?string $projectRoot = null,
        private readonly string $release = ReleaseBaseline::PRODUCTION,
    ) {
    }

    /**
     * @return array{
     *   target: string,
     *   database: string,
     *   dump: string,
     *   dropped_tables: list<string>,
     *   baseline: list<string>,
     *   migrated: list<string>,
     *   pending_after: list<string>
     * }
     */
    public function rebuild(MigrationTarget $target): array
    {
        $dumpPath = BaselineDumpPaths::assertReadable($target, $this->projectRoot);
        $dbName = $this->connections->databaseName($target);
        $manipulator = $this->connections->createManipulator($target);
        $pdo = HandlerPdoUnwrapper::unwrap($manipulator->getHandler());

        $dropped = $this->dropAllTables($pdo, $dbName);
        $this->importDump($manipulator, $dumpPath);

        $runner = $this->connections->createRunner($target);
        $baseline = $runner->baseline($target, $this->release);
        $migrated = $runner->migrate($target);
        $status = $runner->status($target);

        return [
            'target' => $target->value,
            'database' => $dbName,
            'dump' => $dumpPath,
            'dropped_tables' => $dropped,
            'baseline' => $baseline,
            'migrated' => $migrated,
            'pending_after' => $status['pending'],
        ];
    }

    /**
     * @return list<string>
     */
    private function dropAllTables(PDO $pdo, string $database): array
    {
        $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
        $stmt = $pdo->prepare(
            'SELECT TABLE_NAME AS table_name
             FROM information_schema.tables
             WHERE table_schema = :db AND table_type = \'BASE TABLE\'
             ORDER BY TABLE_NAME'
        );
        $stmt->execute(['db' => $database]);
        $tables = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $name = (string) $row['table_name'];
            $tables[] = $name;
            $pdo->exec('DROP TABLE IF EXISTS `' . str_replace('`', '``', $name) . '`');
        }
        // views
        $vstmt = $pdo->prepare(
            'SELECT TABLE_NAME AS table_name
             FROM information_schema.views
             WHERE table_schema = :db
             ORDER BY TABLE_NAME'
        );
        $vstmt->execute(['db' => $database]);
        while ($row = $vstmt->fetch(PDO::FETCH_ASSOC)) {
            $name = (string) $row['table_name'];
            $pdo->exec('DROP VIEW IF EXISTS `' . str_replace('`', '``', $name) . '`');
        }
        $pdo->exec('SET FOREIGN_KEY_CHECKS=1');

        return $tables;
    }

    private function importDump(Manipulator $manipulator, string $dumpPath): void
    {
        $sql = file_get_contents($dumpPath);
        if ($sql === false) {
            throw new RuntimeException("Nelze přečíst baseline dump: {$dumpPath}");
        }
        $sql = BaselineDumpSanitizer::sanitize($sql);
        if (trim($sql) === '') {
            throw new RuntimeException("Baseline dump je prázdný po sanitizaci: {$dumpPath}");
        }
        $manipulator->executeSequence($sql);
    }
}
