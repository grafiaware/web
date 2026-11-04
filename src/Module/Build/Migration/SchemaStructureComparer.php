<?php

declare(strict_types=1);

namespace Build\Migration;

/**
 * Porovná dva SchemaStructure snapshoty (expected=dump, actual=live).
 */
final class SchemaStructureComparer
{
    public static function compare(SchemaStructure $expected, SchemaStructure $actual): SchemaStructureDiff
    {
        $lines = [];

        $expectedTables = $expected->tables();
        $actualTables = $actual->tables();

        foreach (array_diff(array_keys($expectedTables), array_keys($actualTables)) as $missing) {
            $lines[] = "chybí tabulka `{$missing}` (je v dump, není v DB)";
        }
        foreach (array_diff(array_keys($actualTables), array_keys($expectedTables)) as $extra) {
            $lines[] = "navíc tabulka `{$extra}` (je v DB, není v dump)";
        }

        foreach (array_intersect(array_keys($expectedTables), array_keys($actualTables)) as $table) {
            $lines = array_merge(
                $lines,
                self::diffTable($table, $expectedTables[$table], $actualTables[$table])
            );
        }

        $expectedViews = $expected->views();
        $actualViews = $actual->views();
        foreach (array_diff(array_keys($expectedViews), array_keys($actualViews)) as $missing) {
            $lines[] = "chybí view `{$missing}`";
        }
        foreach (array_diff(array_keys($actualViews), array_keys($expectedViews)) as $extra) {
            $lines[] = "navíc view `{$extra}`";
        }
        foreach (array_intersect(array_keys($expectedViews), array_keys($actualViews)) as $view) {
            if ($expectedViews[$view] !== $actualViews[$view]) {
                $lines[] = "view `{$view}`: definice se liší";
            }
        }

        return new SchemaStructureDiff($lines);
    }

    /**
     * @param array{
     *   name: string,
     *   engine: ?string,
     *   charset: ?string,
     *   collation: ?string,
     *   columns: array<string, array{name: string, type: string, nullable: bool, default: ?string, extra: string, charset: ?string, collation: ?string}>,
     *   indexes: array<string, array{name: string, type: string, columns: list<string>}>,
     *   foreign_keys: array<string, array{name: string, columns: list<string>, ref_table: string, ref_columns: list<string>, on_update: string, on_delete: string}>
     * } $expected
     * @param array{
     *   name: string,
     *   engine: ?string,
     *   charset: ?string,
     *   collation: ?string,
     *   columns: array<string, array{name: string, type: string, nullable: bool, default: ?string, extra: string, charset: ?string, collation: ?string}>,
     *   indexes: array<string, array{name: string, type: string, columns: list<string>}>,
     *   foreign_keys: array<string, array{name: string, columns: list<string>, ref_table: string, ref_columns: list<string>, on_update: string, on_delete: string}>
     * } $actual
     * @return list<string>
     */
    private static function diffTable(string $table, array $expected, array $actual): array
    {
        $lines = [];
        if ($expected['engine'] !== null && $actual['engine'] !== null
            && $expected['engine'] !== $actual['engine']
        ) {
            $lines[] = "tabulka `{$table}`: engine {$actual['engine']} ≠ dump {$expected['engine']}";
        }
        if ($expected['charset'] !== null && $actual['charset'] !== null
            && $expected['charset'] !== $actual['charset']
        ) {
            $lines[] = "tabulka `{$table}`: charset {$actual['charset']} ≠ dump {$expected['charset']}";
        }
        if ($expected['collation'] !== null && $actual['collation'] !== null
            && $expected['collation'] !== $actual['collation']
        ) {
            $lines[] = "tabulka `{$table}`: collation {$actual['collation']} ≠ dump {$expected['collation']}";
        }

        foreach (array_diff(array_keys($expected['columns']), array_keys($actual['columns'])) as $col) {
            $lines[] = "tabulka `{$table}`: chybí sloupec `{$col}`";
        }
        foreach (array_diff(array_keys($actual['columns']), array_keys($expected['columns'])) as $col) {
            $lines[] = "tabulka `{$table}`: navíc sloupec `{$col}`";
        }
        foreach (array_intersect(array_keys($expected['columns']), array_keys($actual['columns'])) as $col) {
            $e = $expected['columns'][$col];
            $a = $actual['columns'][$col];
            $parts = [];
            if ($e['type'] !== $a['type']) {
                $parts[] = "type {$a['type']} ≠ dump {$e['type']}";
            }
            if ($e['nullable'] !== $a['nullable']) {
                $parts[] = 'nullable ' . ($a['nullable'] ? 'YES' : 'NO')
                    . ' ≠ dump ' . ($e['nullable'] ? 'YES' : 'NO');
            }
            if ($e['default'] !== $a['default']) {
                $parts[] = 'default '
                    . self::fmtDefault($a['default'])
                    . ' ≠ dump '
                    . self::fmtDefault($e['default']);
            }
            if ($e['extra'] !== $a['extra']) {
                $parts[] = "extra '{$a['extra']}' ≠ dump '{$e['extra']}'";
            }
            // charset/collation jen když dump explicitně nastavil na sloupci jinak než tabulka
            // — prakticky porovnáme, pokud obě strany mají hodnotu
            if ($e['charset'] !== null && $a['charset'] !== null && $e['charset'] !== $a['charset']) {
                $parts[] = "charset {$a['charset']} ≠ dump {$e['charset']}";
            }
            if ($parts !== []) {
                $lines[] = "tabulka `{$table}`.`{$col}`: " . implode('; ', $parts);
            }
        }

        $lines = array_merge($lines, self::diffIndexes($table, $expected['indexes'], $actual['indexes']));
        $lines = array_merge(
            $lines,
            self::diffForeignKeys($table, $expected['foreign_keys'], $actual['foreign_keys'])
        );

        return $lines;
    }

    /**
     * @param array<string, array{name: string, type: string, columns: list<string>}> $expected
     * @param array<string, array{name: string, type: string, columns: list<string>}> $actual
     * @return list<string>
     */
    private static function diffIndexes(string $table, array $expected, array $actual): array
    {
        $lines = [];
        $expBySig = [];
        foreach ($expected as $idx) {
            $sig = SchemaStructureNormalizer::indexSignature($idx['type'], $idx['columns']);
            $expBySig[$sig] = $idx;
        }
        $actBySig = [];
        foreach ($actual as $idx) {
            $sig = SchemaStructureNormalizer::indexSignature($idx['type'], $idx['columns']);
            $actBySig[$sig] = $idx;
        }

        foreach (array_diff(array_keys($expBySig), array_keys($actBySig)) as $sig) {
            $idx = $expBySig[$sig];
            $lines[] = "tabulka `{$table}`: chybí index {$idx['type']} ({$sig})"
                . ($idx['name'] !== '' ? " [dump: {$idx['name']}]" : '');
        }
        foreach (array_diff(array_keys($actBySig), array_keys($expBySig)) as $sig) {
            $idx = $actBySig[$sig];
            $lines[] = "tabulka `{$table}`: navíc index {$idx['type']} ({$sig})"
                . ($idx['name'] !== '' ? " [db: {$idx['name']}]" : '');
        }

        return $lines;
    }

    /**
     * @param array<string, array{name: string, columns: list<string>, ref_table: string, ref_columns: list<string>, on_update: string, on_delete: string}> $expected
     * @param array<string, array{name: string, columns: list<string>, ref_table: string, ref_columns: list<string>, on_update: string, on_delete: string}> $actual
     * @return list<string>
     */
    private static function diffForeignKeys(string $table, array $expected, array $actual): array
    {
        $lines = [];
        foreach (array_diff(array_keys($expected), array_keys($actual)) as $sig) {
            $fk = $expected[$sig];
            $lines[] = "tabulka `{$table}`: chybí FK {$sig} [dump: {$fk['name']}]";
        }
        foreach (array_diff(array_keys($actual), array_keys($expected)) as $sig) {
            $fk = $actual[$sig];
            $lines[] = "tabulka `{$table}`: navíc FK {$sig} [db: {$fk['name']}]";
        }

        return $lines;
    }

    private static function fmtDefault(?string $value): string
    {
        return $value === null ? 'NULL' : "'{$value}'";
    }
}
