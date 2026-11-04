<?php

declare(strict_types=1);

namespace Build\Migration;

/**
 * Normalizovaný snapshot struktury DB (bez dat) pro porovnání live vs baseline dump.
 *
 * @phpstan-type ColumnShape array{
 *   name: string,
 *   type: string,
 *   nullable: bool,
 *   default: ?string,
 *   extra: string,
 *   charset: ?string,
 *   collation: ?string
 * }
 * @phpstan-type IndexShape array{
 *   name: string,
 *   type: string,
 *   columns: list<string>
 * }
 * @phpstan-type ForeignKeyShape array{
 *   name: string,
 *   columns: list<string>,
 *   ref_table: string,
 *   ref_columns: list<string>,
 *   on_update: string,
 *   on_delete: string
 * }
 * @phpstan-type TableShape array{
 *   name: string,
 *   engine: ?string,
 *   charset: ?string,
 *   collation: ?string,
 *   columns: array<string, ColumnShape>,
 *   indexes: array<string, IndexShape>,
 *   foreign_keys: array<string, ForeignKeyShape>
 * }
 */
final class SchemaStructure
{
    /** Tabulka žurnálu — není v baseline dumpu; při porovnání se ignoruje. */
    public const IGNORE_TABLES = [
        MigrationJournal::TABLE => true,
    ];

    /**
     * @param array<string, TableShape> $tables
     * @param array<string, string> $views view_name => normalized select/body fingerprint
     */
    public function __construct(
        private readonly array $tables = [],
        private readonly array $views = [],
    ) {
    }

    /**
     * @return array<string, TableShape>
     */
    public function tables(): array
    {
        return $this->tables;
    }

    /**
     * @return array<string, string>
     */
    public function views(): array
    {
        return $this->views;
    }

    public function hasTable(string $name): bool
    {
        return isset($this->tables[strtolower($name)]);
    }

    /**
     * @return TableShape|null
     */
    public function table(string $name): ?array
    {
        return $this->tables[strtolower($name)] ?? null;
    }

    public static function shouldIgnoreTable(string $name): bool
    {
        return isset(self::IGNORE_TABLES[strtolower($name)]);
    }
}
