<?php

declare(strict_types=1);

namespace Build\Migration;

use PDO;

/**
 * Před prvním zápisem / vytvořením schema_migrations ověří, že live DB
 * strukturálně odpovídá baseline dumpu 2025 (bez dat).
 *
 * Rebuild po importu dumpu gate přeskočí ({@see self::disabled()}).
 */
final class BaselineStructureGate
{
    public function __construct(
        private readonly PDO $pdo,
        private readonly ?string $projectRoot = null,
        private readonly bool $enabled = true,
        private readonly ?string $databaseName = null,
    ) {
    }

    public static function disabled(PDO $pdo): self
    {
        return new self($pdo, enabled: false);
    }

    public function assertMatchesBaseline(MigrationTarget $target, MigrationJournal $journal): void
    {
        if (!$this->enabled) {
            return;
        }

        $driver = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($driver !== 'mysql') {
            return;
        }

        if (!$journal->isVirgin()) {
            return;
        }

        $dumpPath = BaselineDumpPaths::assertReadable($target, $this->projectRoot);
        $expected = BaselineDumpStructureParser::parseFile($dumpPath);
        $actual = LiveSchemaStructureReader::read($this->pdo, $this->databaseName);
        $diff = SchemaStructureComparer::compare($expected, $actual);
        if (!$diff->isEmpty()) {
            throw new SchemaStructureMismatchException($target, $diff, $dumpPath);
        }
    }
}
