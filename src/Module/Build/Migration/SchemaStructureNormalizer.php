<?php

declare(strict_types=1);

namespace Build\Migration;

/**
 * Sjednocuje MySQL 5.6 / 8 / MariaDB odchylky pro strukturální porovnání.
 */
final class SchemaStructureNormalizer
{
    public static function ident(string $name): string
    {
        return strtolower(trim($name, " \t\n\r\0\x0B`\"'"));
    }

    public static function charset(?string $charset): ?string
    {
        if ($charset === null || $charset === '') {
            return null;
        }
        $c = strtolower(trim($charset));
        // MySQL 8: utf8 je alias utf8mb3
        if ($c === 'utf8') {
            return 'utf8mb3';
        }

        return $c;
    }

    public static function collation(?string $collation): ?string
    {
        if ($collation === null || $collation === '') {
            return null;
        }
        $c = strtolower(trim($collation));
        if (str_starts_with($c, 'utf8_')) {
            return 'utf8mb3_' . substr($c, strlen('utf8_'));
        }

        return $c;
    }

    public static function dataType(string $type): string
    {
        $t = strtolower(trim($type));
        $t = preg_replace('/\s+/', ' ', $t) ?? $t;
        // MySQL 8.0.17+ bere display width u integerů jako deprecated / často bez něj v information_schema.
        // Dump z 5.6/MariaDB má int(11); sjednotíme na int (kromě ZEROFILL, kde šířka zůstává).
        if (!str_contains($t, 'zerofill')) {
            $t = preg_replace(
                '/\b(tinyint|smallint|mediumint|int|integer|bigint)\(\d+\)/',
                '$1',
                $t
            ) ?? $t;
        }

        return $t;
    }

    public static function defaultValue(?string $default): ?string
    {
        if ($default === null) {
            return null;
        }
        $d = trim($default);
        if ($d === '' || strcasecmp($d, 'NULL') === 0) {
            return null;
        }
        $d = trim($d, "'\"");
        $upper = strtoupper($d);
        if ($upper === 'CURRENT_TIMESTAMP' || $upper === 'CURRENT_TIMESTAMP()') {
            return 'current_timestamp()';
        }
        if (preg_match('/^current_timestamp(?:\(\d*\))?$/i', $d) === 1) {
            return 'current_timestamp()';
        }

        return strtolower($d);
    }

    public static function extra(string $extra): string
    {
        $e = strtolower(trim($extra));
        $e = str_replace('current_timestamp()', 'current_timestamp', $e);
        // auto_increment pořadí / default_generated — bereme jen známé tokeny
        $parts = preg_split('/\s+/', $e) ?: [];
        $keep = [];
        foreach ($parts as $part) {
            if ($part === 'auto_increment') {
                $keep[] = 'auto_increment';
            }
            if (str_starts_with($part, 'on') && str_contains($e, 'on update')) {
                // handled below
            }
        }
        if (str_contains($e, 'on update current_timestamp')) {
            $keep[] = 'on update current_timestamp';
        }
        if (in_array('auto_increment', $keep, true) === false && str_contains($e, 'auto_increment')) {
            array_unshift($keep, 'auto_increment');
        }

        return implode(' ', array_unique($keep));
    }

    public static function fkAction(string $action): string
    {
        $a = strtoupper(trim($action));
        return match ($a) {
            'NO ACTION', 'RESTRICT', '' => 'RESTRICT',
            default => $a,
        };
    }

    public static function indexType(string $type): string
    {
        $t = strtoupper(trim($type));
        return match ($t) {
            'PRIMARY' => 'PRIMARY',
            'UNIQUE' => 'UNIQUE',
            'FULLTEXT' => 'FULLTEXT',
            'SPATIAL' => 'SPATIAL',
            default => 'INDEX',
        };
    }

    /**
     * Signature indexu bez jména (pro párování při rozdílných názvech).
     *
     * @param list<string> $columns
     */
    public static function indexSignature(string $type, array $columns): string
    {
        $cols = array_map([self::class, 'ident'], $columns);

        return self::indexType($type) . ':' . implode(',', $cols);
    }

    /**
     * @param list<string> $columns
     * @param list<string> $refColumns
     */
    public static function fkSignature(
        array $columns,
        string $refTable,
        array $refColumns,
        string $onUpdate,
        string $onDelete
    ): string {
        return implode(',', array_map([self::class, 'ident'], $columns))
            . '>' . self::ident($refTable)
            . '(' . implode(',', array_map([self::class, 'ident'], $refColumns)) . ')'
            . '|u=' . self::fkAction($onUpdate)
            . '|d=' . self::fkAction($onDelete);
    }
}
