<?php

declare(strict_types=1);

namespace Build\Migration;

use PDO;
use RuntimeException;

/**
 * Čte strukturu live MySQL DB z information_schema (MySQL 5.6+ / 8).
 */
final class LiveSchemaStructureReader
{
    public static function read(PDO $pdo, ?string $database = null): SchemaStructure
    {
        $database ??= self::currentDatabase($pdo);
        if ($database === null || $database === '') {
            throw new RuntimeException('Nelze určit jméno databáze pro strukturální kontrolu.');
        }

        $tables = [];
        $tableMeta = self::loadTableMeta($pdo, $database);
        foreach ($tableMeta as $tableName => $meta) {
            if (SchemaStructure::shouldIgnoreTable($tableName)) {
                continue;
            }
            $tables[$tableName] = [
                'name' => $tableName,
                'engine' => $meta['engine'],
                'charset' => $meta['charset'],
                'collation' => $meta['collation'],
                'columns' => self::loadColumns($pdo, $database, $tableName, $meta['charset'], $meta['collation']),
                'indexes' => self::loadIndexes($pdo, $database, $tableName),
                'foreign_keys' => self::loadForeignKeys($pdo, $database, $tableName),
            ];
        }

        $views = self::loadViews($pdo, $database);
        ksort($tables);
        ksort($views);

        return new SchemaStructure($tables, $views);
    }

    private static function currentDatabase(PDO $pdo): ?string
    {
        $name = $pdo->query('SELECT DATABASE()')->fetchColumn();

        return is_string($name) && $name !== '' ? $name : null;
    }

    /**
     * @return array<string, array{engine: ?string, charset: ?string, collation: ?string}>
     */
    private static function loadTableMeta(PDO $pdo, string $database): array
    {
        $stmt = $pdo->prepare(
            'SELECT TABLE_NAME AS name, ENGINE AS engine,
                    TABLE_COLLATION AS collation_name
             FROM information_schema.TABLES
             WHERE TABLE_SCHEMA = :db AND TABLE_TYPE = \'BASE TABLE\'
             ORDER BY TABLE_NAME'
        );
        $stmt->execute(['db' => $database]);
        $out = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $name = SchemaStructureNormalizer::ident((string) $row['name']);
            $collation = SchemaStructureNormalizer::collation(
                isset($row['collation_name']) ? (string) $row['collation_name'] : null
            );
            $charset = null;
            if ($collation !== null && str_contains($collation, '_')) {
                $charset = SchemaStructureNormalizer::charset(strstr($collation, '_', true) ?: null);
            }
            $out[$name] = [
                'engine' => isset($row['engine']) ? strtolower((string) $row['engine']) : null,
                'charset' => $charset,
                'collation' => $collation,
            ];
        }

        return $out;
    }

    /**
     * @return array<string, array{name: string, type: string, nullable: bool, default: ?string, extra: string, charset: ?string, collation: ?string}>
     */
    private static function loadColumns(
        PDO $pdo,
        string $database,
        string $table,
        ?string $tableCharset,
        ?string $tableCollation
    ): array {
        $stmt = $pdo->prepare(
            'SELECT COLUMN_NAME AS name, COLUMN_TYPE AS column_type,
                    IS_NULLABLE AS is_nullable, COLUMN_DEFAULT AS col_default,
                    EXTRA AS extra, CHARACTER_SET_NAME AS charset_name,
                    COLLATION_NAME AS collation_name
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = :db AND TABLE_NAME = :table
             ORDER BY ORDINAL_POSITION'
        );
        $stmt->execute(['db' => $database, 'table' => $table]);
        $columns = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $name = SchemaStructureNormalizer::ident((string) $row['name']);
            $charset = SchemaStructureNormalizer::charset(
                isset($row['charset_name']) && $row['charset_name'] !== null
                    ? (string) $row['charset_name']
                    : $tableCharset
            );
            $collation = SchemaStructureNormalizer::collation(
                isset($row['collation_name']) && $row['collation_name'] !== null
                    ? (string) $row['collation_name']
                    : $tableCollation
            );
            $nullable = strtoupper((string) $row['is_nullable']) === 'YES';
            $columns[$name] = [
                'name' => $name,
                'type' => SchemaStructureNormalizer::dataType((string) $row['column_type']),
                'nullable' => $nullable,
                'default' => SchemaStructureNormalizer::defaultValue(
                    $row['col_default'] === null ? null : (string) $row['col_default'],
                    $nullable
                ),
                'extra' => SchemaStructureNormalizer::extra((string) ($row['extra'] ?? '')),
                'charset' => $charset,
                'collation' => $collation,
            ];
        }
        ksort($columns);

        return $columns;
    }

    /**
     * @return array<string, array{name: string, type: string, columns: list<string>}>
     */
    private static function loadIndexes(PDO $pdo, string $database, string $table): array
    {
        $stmt = $pdo->prepare(
            'SELECT INDEX_NAME AS index_name, NON_UNIQUE AS non_unique,
                    INDEX_TYPE AS index_type, COLUMN_NAME AS column_name,
                    SEQ_IN_INDEX AS seq
             FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = :db AND TABLE_NAME = :table
             ORDER BY INDEX_NAME, SEQ_IN_INDEX'
        );
        $stmt->execute(['db' => $database, 'table' => $table]);
        /** @var array<string, array{name: string, type: string, columns: list<string>}> $indexes */
        $indexes = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $rawName = (string) $row['index_name'];
            $name = SchemaStructureNormalizer::ident($rawName);
            if (!isset($indexes[$name])) {
                $type = 'INDEX';
                if (strcasecmp($rawName, 'PRIMARY') === 0) {
                    $type = 'PRIMARY';
                } elseif ((int) $row['non_unique'] === 0) {
                    $type = 'UNIQUE';
                } elseif (strcasecmp((string) $row['index_type'], 'FULLTEXT') === 0) {
                    $type = 'FULLTEXT';
                } elseif (strcasecmp((string) $row['index_type'], 'SPATIAL') === 0) {
                    $type = 'SPATIAL';
                }
                $indexes[$name] = [
                    'name' => $name,
                    'type' => $type,
                    'columns' => [],
                ];
            }
            $indexes[$name]['columns'][] = SchemaStructureNormalizer::ident((string) $row['column_name']);
        }
        ksort($indexes);

        return $indexes;
    }

    /**
     * @return array<string, array{name: string, columns: list<string>, ref_table: string, ref_columns: list<string>, on_update: string, on_delete: string}>
     */
    private static function loadForeignKeys(PDO $pdo, string $database, string $table): array
    {
        $stmt = $pdo->prepare(
            'SELECT rc.CONSTRAINT_NAME AS constraint_name,
                    rc.UPDATE_RULE AS update_rule,
                    rc.DELETE_RULE AS delete_rule,
                    kcu.COLUMN_NAME AS column_name,
                    kcu.REFERENCED_TABLE_NAME AS ref_table,
                    kcu.REFERENCED_COLUMN_NAME AS ref_column,
                    kcu.ORDINAL_POSITION AS ordinal
             FROM information_schema.REFERENTIAL_CONSTRAINTS rc
             INNER JOIN information_schema.KEY_COLUMN_USAGE kcu
               ON rc.CONSTRAINT_SCHEMA = kcu.CONSTRAINT_SCHEMA
              AND rc.CONSTRAINT_NAME = kcu.CONSTRAINT_NAME
              AND rc.TABLE_NAME = kcu.TABLE_NAME
             WHERE rc.CONSTRAINT_SCHEMA = :db AND rc.TABLE_NAME = :table
             ORDER BY rc.CONSTRAINT_NAME, kcu.ORDINAL_POSITION'
        );
        $stmt->execute(['db' => $database, 'table' => $table]);

        /** @var array<string, array{name: string, columns: list<string>, ref_table: string, ref_columns: list<string>, on_update: string, on_delete: string}> $grouped */
        $grouped = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $name = SchemaStructureNormalizer::ident((string) $row['constraint_name']);
            if (!isset($grouped[$name])) {
                $grouped[$name] = [
                    'name' => $name,
                    'columns' => [],
                    'ref_table' => SchemaStructureNormalizer::ident((string) $row['ref_table']),
                    'ref_columns' => [],
                    'on_update' => SchemaStructureNormalizer::fkAction((string) $row['update_rule']),
                    'on_delete' => SchemaStructureNormalizer::fkAction((string) $row['delete_rule']),
                ];
            }
            $grouped[$name]['columns'][] = SchemaStructureNormalizer::ident((string) $row['column_name']);
            $grouped[$name]['ref_columns'][] = SchemaStructureNormalizer::ident((string) $row['ref_column']);
        }

        $bySig = [];
        foreach ($grouped as $fk) {
            $sig = SchemaStructureNormalizer::fkSignature(
                $fk['columns'],
                $fk['ref_table'],
                $fk['ref_columns'],
                $fk['on_update'],
                $fk['on_delete']
            );
            $bySig[$sig] = $fk;
        }
        ksort($bySig);

        return $bySig;
    }

    /**
     * @return array<string, string>
     */
    private static function loadViews(PDO $pdo, string $database): array
    {
        $stmt = $pdo->prepare(
            'SELECT TABLE_NAME AS name, VIEW_DEFINITION AS definition
             FROM information_schema.VIEWS
             WHERE TABLE_SCHEMA = :db
             ORDER BY TABLE_NAME'
        );
        $stmt->execute(['db' => $database]);
        $views = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $name = SchemaStructureNormalizer::ident((string) $row['name']);
            $body = preg_replace('/\s+/', ' ', trim((string) $row['definition'])) ?? '';
            $views[$name] = strtolower($body);
        }

        return $views;
    }
}
