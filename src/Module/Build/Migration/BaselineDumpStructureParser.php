<?php

declare(strict_types=1);

namespace Build\Migration;

use RuntimeException;

/**
 * Extrahuje strukturu (CREATE TABLE / CREATE VIEW) z baseline SQL dumpu — bez dat.
 */
final class BaselineDumpStructureParser
{
    public static function parseFile(string $path): SchemaStructure
    {
        $sql = file_get_contents($path);
        if ($sql === false) {
            throw new RuntimeException("Nelze přečíst baseline dump: {$path}");
        }

        return self::parse($sql);
    }

    public static function parse(string $sql): SchemaStructure
    {
        $sql = BaselineDumpSanitizer::sanitize($sql);
        $tables = [];
        $views = [];

        foreach (self::extractCreateTables($sql) as $extracted) {
            $name = SchemaStructureNormalizer::ident($extracted['name']);
            if (SchemaStructure::shouldIgnoreTable($name)) {
                continue;
            }
            $tables[$name] = self::parseTable($name, $extracted['body'], $extracted['suffix']);
        }

        if (preg_match_all(
            '/CREATE\s+(?:OR\s+REPLACE\s+)?(?:ALGORITHM=\w+\s+)?(?:SQL\s+SECURITY\s+\w+\s+)?VIEW\s+`?([a-zA-Z0-9_]+)`?\s+AS\s+(.*?);/is',
            $sql,
            $viewMatches,
            PREG_SET_ORDER
        )) {
            foreach ($viewMatches as $vm) {
                $vname = SchemaStructureNormalizer::ident($vm[1]);
                $body = preg_replace('/\s+/', ' ', trim($vm[2])) ?? trim($vm[2]);
                $views[$vname] = strtolower($body);
            }
        }

        ksort($tables);
        ksort($views);

        return new SchemaStructure($tables, $views);
    }

    /**
     * Najde CREATE TABLE a vytáhne tělo vyváženými závorkami (ne greedy regex přes INSERT).
     *
     * @return list<array{name: string, body: string, suffix: string}>
     */
    private static function extractCreateTables(string $sql): array
    {
        $out = [];
        $offset = 0;
        $len = strlen($sql);
        while ($offset < $len) {
            if (preg_match(
                '/CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?`?([a-zA-Z0-9_]+)`?\s*\(/is',
                $sql,
                $m,
                PREG_OFFSET_CAPTURE,
                $offset
            ) !== 1) {
                break;
            }
            $name = $m[1][0];
            $openParen = (int) $m[0][1] + strlen($m[0][0]) - 1;
            $closeParen = self::findMatchingParen($sql, $openParen);
            if ($closeParen === null) {
                throw new RuntimeException(
                    "Neuzavřené CREATE TABLE `{$name}` v baseline dumpu."
                );
            }
            $body = substr($sql, $openParen + 1, $closeParen - $openParen - 1);
            $semi = strpos($sql, ';', $closeParen);
            if ($semi === false) {
                throw new RuntimeException(
                    "Chybí ';' po CREATE TABLE `{$name}` v baseline dumpu."
                );
            }
            $suffix = substr($sql, $closeParen + 1, $semi - $closeParen - 1);
            $out[] = [
                'name' => $name,
                'body' => $body,
                'suffix' => $suffix,
            ];
            $offset = $semi + 1;
        }

        return $out;
    }

    private static function findMatchingParen(string $sql, int $openPos): ?int
    {
        $len = strlen($sql);
        $depth = 0;
        $quote = null;
        for ($i = $openPos; $i < $len; $i++) {
            $ch = $sql[$i];
            if ($quote !== null) {
                if ($ch === '\\' && $i + 1 < $len) {
                    $i++;
                    continue;
                }
                if ($ch === $quote) {
                    $quote = null;
                }
                continue;
            }
            if ($ch === "'" || $ch === '"' || $ch === '`') {
                $quote = $ch;
                continue;
            }
            if ($ch === '(') {
                $depth++;
                continue;
            }
            if ($ch === ')') {
                $depth--;
                if ($depth === 0) {
                    return $i;
                }
            }
        }

        return null;
    }

    /**
     * @return array{
     *   name: string,
     *   engine: ?string,
     *   charset: ?string,
     *   collation: ?string,
     *   columns: array<string, array{name: string, type: string, nullable: bool, default: ?string, extra: string, charset: ?string, collation: ?string}>,
     *   indexes: array<string, array{name: string, type: string, columns: list<string>}>,
     *   foreign_keys: array<string, array{name: string, columns: list<string>, ref_table: string, ref_columns: list<string>, on_update: string, on_delete: string}>
     * }
     */
    private static function parseTable(string $name, string $body, string $suffix): array
    {
        $engine = null;
        $charset = null;
        $collation = null;
        if (preg_match('/ENGINE\s*=\s*(\w+)/i', $suffix, $m) === 1) {
            $engine = strtolower($m[1]);
        }
        if (preg_match('/(?:DEFAULT\s+)?CHARSET\s*=\s*(\w+)/i', $suffix, $m) === 1) {
            $charset = SchemaStructureNormalizer::charset($m[1]);
        }
        if (preg_match('/COLLATE\s*=\s*(\w+)/i', $suffix, $m) === 1) {
            $collation = SchemaStructureNormalizer::collation($m[1]);
        }

        $columns = [];
        $indexes = [];
        $foreignKeys = [];

        foreach (self::splitTableDefinitions($body) as $def) {
            $def = trim($def);
            if ($def === '') {
                continue;
            }

            if (preg_match('/^PRIMARY\s+KEY\s*\((.+)\)\s*$/i', $def, $m) === 1) {
                $cols = self::parseIdentList($m[1]);
                $indexes['primary'] = [
                    'name' => 'primary',
                    'type' => 'PRIMARY',
                    'columns' => $cols,
                ];
                continue;
            }

            if (preg_match(
                '/^(?:CONSTRAINT\s+`?([a-zA-Z0-9_]+)`?\s+)?UNIQUE\s+(?:KEY|INDEX)\s+(?:`?([a-zA-Z0-9_]+)`?\s+)?\((.+)\)\s*$/i',
                $def,
                $m
            ) === 1) {
                $idxName = SchemaStructureNormalizer::ident($m[2] !== '' ? $m[2] : ($m[1] !== '' ? $m[1] : 'unique_' . count($indexes)));
                $indexes[$idxName] = [
                    'name' => $idxName,
                    'type' => 'UNIQUE',
                    'columns' => self::parseIdentList($m[3]),
                ];
                continue;
            }

            if (preg_match(
                '/^(?:CONSTRAINT\s+`?([a-zA-Z0-9_]+)`?\s+)?FULLTEXT\s+(?:KEY|INDEX)\s+`?([a-zA-Z0-9_]+)`?\s*\((.+)\)\s*$/i',
                $def,
                $m
            ) === 1) {
                $idxName = SchemaStructureNormalizer::ident($m[2]);
                $indexes[$idxName] = [
                    'name' => $idxName,
                    'type' => 'FULLTEXT',
                    'columns' => self::parseIdentList($m[3]),
                ];
                continue;
            }

            if (preg_match(
                '/^(?:KEY|INDEX)\s+`?([a-zA-Z0-9_]+)`?\s*\((.+)\)\s*$/i',
                $def,
                $m
            ) === 1) {
                $idxName = SchemaStructureNormalizer::ident($m[1]);
                $indexes[$idxName] = [
                    'name' => $idxName,
                    'type' => 'INDEX',
                    'columns' => self::parseIdentList($m[2]),
                ];
                continue;
            }

            if (preg_match(
                '/^(?:CONSTRAINT\s+`?([a-zA-Z0-9_]+)`?\s+)?FOREIGN\s+KEY\s*(?:`?[a-zA-Z0-9_]+`?\s*)?\((.+)\)\s*REFERENCES\s+`?([a-zA-Z0-9_]+)`?\s*\((.+)\)(.*)$/i',
                $def,
                $m
            ) === 1) {
                $fkName = SchemaStructureNormalizer::ident($m[1] !== '' ? $m[1] : 'fk_' . count($foreignKeys));
                $onUpdate = 'RESTRICT';
                $onDelete = 'RESTRICT';
                if (preg_match('/ON\s+UPDATE\s+(CASCADE|SET\s+NULL|RESTRICT|NO\s+ACTION|SET\s+DEFAULT)/i', $m[5], $um) === 1) {
                    $onUpdate = strtoupper(preg_replace('/\s+/', ' ', $um[1]) ?? $um[1]);
                }
                if (preg_match('/ON\s+DELETE\s+(CASCADE|SET\s+NULL|RESTRICT|NO\s+ACTION|SET\s+DEFAULT)/i', $m[5], $dm) === 1) {
                    $onDelete = strtoupper(preg_replace('/\s+/', ' ', $dm[1]) ?? $dm[1]);
                }
                $sig = SchemaStructureNormalizer::fkSignature(
                    self::parseIdentList($m[2]),
                    $m[3],
                    self::parseIdentList($m[4]),
                    $onUpdate,
                    $onDelete
                );
                $foreignKeys[$sig] = [
                    'name' => $fkName,
                    'columns' => self::parseIdentList($m[2]),
                    'ref_table' => SchemaStructureNormalizer::ident($m[3]),
                    'ref_columns' => self::parseIdentList($m[4]),
                    'on_update' => SchemaStructureNormalizer::fkAction($onUpdate),
                    'on_delete' => SchemaStructureNormalizer::fkAction($onDelete),
                ];
                continue;
            }

            if (preg_match('/^`?([a-zA-Z0-9_]+)`?\s+(.+)$/s', $def, $m) === 1) {
                $colName = SchemaStructureNormalizer::ident($m[1]);
                $columns[$colName] = self::parseColumn($colName, $m[2], $charset, $collation);
            }
        }

        ksort($columns);
        ksort($indexes);
        ksort($foreignKeys);

        return [
            'name' => $name,
            'engine' => $engine,
            'charset' => $charset,
            'collation' => $collation,
            'columns' => $columns,
            'indexes' => $indexes,
            'foreign_keys' => $foreignKeys,
        ];
    }

    /**
     * @return array{name: string, type: string, nullable: bool, default: ?string, extra: string, charset: ?string, collation: ?string}
     */
    private static function parseColumn(
        string $name,
        string $rest,
        ?string $tableCharset,
        ?string $tableCollation
    ): array {
        $rest = trim($rest);
        $nullable = !preg_match('/\bNOT\s+NULL\b/i', $rest);
        $extraParts = [];
        if (preg_match('/\bAUTO_INCREMENT\b/i', $rest) === 1) {
            $extraParts[] = 'auto_increment';
        }
        if (preg_match('/\bON\s+UPDATE\s+CURRENT_TIMESTAMP(?:\(\d*\))?/i', $rest) === 1) {
            $extraParts[] = 'on update current_timestamp';
        }

        $default = null;
        if (preg_match(
            '/\bDEFAULT\s+(NULL|CURRENT_TIMESTAMP(?:\(\d*\))?|\'(?:\\\\\'|[^\'])*\'|\"(?:\\\\\"|[^\"])*\"|[^\s,]+)/i',
            $rest,
            $dm
        ) === 1) {
            $default = SchemaStructureNormalizer::defaultValue($dm[1]);
        }

        $colCharset = $tableCharset;
        $colCollation = $tableCollation;
        if (preg_match('/\bCHARACTER\s+SET\s+(\w+)/i', $rest, $cm) === 1) {
            $colCharset = SchemaStructureNormalizer::charset($cm[1]);
        }
        if (preg_match('/\bCOLLATE\s+(\w+)/i', $rest, $cm) === 1) {
            $colCollation = SchemaStructureNormalizer::collation($cm[1]);
        }

        // typ = začátek až před NULL / NOT NULL / DEFAULT / AUTO_INCREMENT / CHARACTER / COLLATE / COMMENT / ON UPDATE
        $type = $rest;
        if (preg_match(
            '/^(.+?)(?=\s+(?:NOT\s+NULL|NULL|DEFAULT|AUTO_INCREMENT|CHARACTER\s+SET|COLLATE|COMMENT|ON\s+UPDATE|GENERATED)\b|$)/is',
            $rest,
            $tm
        ) === 1) {
            $type = trim($tm[1]);
        }
        $type = SchemaStructureNormalizer::dataType($type);

        return [
            'name' => $name,
            'type' => $type,
            'nullable' => $nullable,
            'default' => $default,
            'extra' => implode(' ', $extraParts),
            'charset' => $colCharset,
            'collation' => $colCollation,
        ];
    }

    /**
     * @return list<string>
     */
    private static function parseIdentList(string $list): array
    {
        $parts = preg_split('/\s*,\s*/', trim($list)) ?: [];
        $out = [];
        foreach ($parts as $part) {
            // `col`(10) prefix length — ber jen jméno
            if (preg_match('/`?([a-zA-Z0-9_]+)`?/', $part, $m) === 1) {
                $out[] = SchemaStructureNormalizer::ident($m[1]);
            }
        }

        return $out;
    }

    /**
     * Rozdělí tělo CREATE TABLE podle čárek mimo závorky a uvozovky.
     *
     * @return list<string>
     */
    private static function splitTableDefinitions(string $body): array
    {
        $parts = [];
        $buf = '';
        $depth = 0;
        $quote = null;
        $len = strlen($body);
        for ($i = 0; $i < $len; $i++) {
            $ch = $body[$i];
            if ($quote !== null) {
                $buf .= $ch;
                if ($ch === '\\' && $i + 1 < $len) {
                    $buf .= $body[++$i];
                    continue;
                }
                if ($ch === $quote) {
                    $quote = null;
                }
                continue;
            }
            if ($ch === "'" || $ch === '"' || $ch === '`') {
                $quote = $ch;
                $buf .= $ch;
                continue;
            }
            if ($ch === '(') {
                $depth++;
                $buf .= $ch;
                continue;
            }
            if ($ch === ')') {
                $depth = max(0, $depth - 1);
                $buf .= $ch;
                continue;
            }
            if ($ch === ',' && $depth === 0) {
                $parts[] = $buf;
                $buf = '';
                continue;
            }
            $buf .= $ch;
        }
        if (trim($buf) !== '') {
            $parts[] = $buf;
        }

        return $parts;
    }
}
