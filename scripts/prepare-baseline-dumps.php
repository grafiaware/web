<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$srcDir = $root . '/docker/migrations/dumps/dumps_produkce';
$dstDir = $root . '/docker/migrations/baseline/2025';
if (!is_dir($dstDir)) {
    mkdir($dstDir, 0777, true);
}

function stripSchemaMigrations(string $sql): string
{
    $sql = preg_replace('/DROP\s+TABLE\s+IF\s+EXISTS\s+`?schema_migrations`?\s*;/i', '', $sql) ?? $sql;
    $sql = preg_replace('/CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?`?schema_migrations`?[\s\S]*?;\s*/i', '', $sql) ?? $sql;
    $sql = preg_replace('/INSERT\s+INTO\s+`?schema_migrations`?[\s\S]*?;\s*/i', '', $sql) ?? $sql;

    return $sql;
}

function stripSchemaQualifiers(string $sql): string
{
    return preg_replace('/`[A-Za-z0-9_]+`\s*\.\s*(`[A-Za-z0-9_]+`)/', '$1', $sql) ?? $sql;
}

function extractStructureWithDrops(string $sql): string
{
    $out = ["SET FOREIGN_KEY_CHECKS=0;", ''];
    if (preg_match_all('/CREATE\s+TABLE\s+`([^`]+)`\s*\([\s\S]*?\)\s*ENGINE[\s\S]*?;/i', $sql, $m, PREG_SET_ORDER)) {
        foreach ($m as $match) {
            $table = $match[1];
            $create = $match[0];
            $out[] = "-- Table structure for {$table}";
            $out[] = "DROP TABLE IF EXISTS `{$table}`;";
            $out[] = $create;
            $out[] = '';
        }
    }
    $out[] = 'SET FOREIGN_KEY_CHECKS=1;';

    return implode("\n", $out) . "\n";
}

function normalizeDump(string $sql, bool $structureOnly = false): string
{
    $sql = stripSchemaMigrations($sql);
    $sql = stripSchemaQualifiers($sql);
    $sql = preg_replace('/^\s*USE\s+`?[^`;]+`?\s*;\s*$/mi', '', $sql) ?? $sql;
    $sql = preg_replace('/^\s*CREATE\s+DATABASE[\s\S]*?;\s*$/mi', '', $sql) ?? $sql;
    if ($structureOnly) {
        return extractStructureWithDrops($sql);
    }
    if (stripos($sql, 'FOREIGN_KEY_CHECKS') === false) {
        $sql = "SET FOREIGN_KEY_CHECKS=0;\n" . $sql . "\nSET FOREIGN_KEY_CHECKS=1;\n";
    }

    return $sql;
}

$header = static function (string $target, string $source, string $note): string {
    return "-- Baseline dump 2025 — target={$target}\n"
        . "-- Source: {$source}\n"
        . "-- {$note}\n"
        . "-- No schema_migrations (journal written by rebuild CLI after import).\n"
        . "-- No schema-qualified identifiers; import into the target DB connection.\n\n";
};

$auth = normalizeDump((string) file_get_contents("{$srcDir}/login.sql"), false);
file_put_contents(
    "{$dstDir}/auth.sql",
    $header('auth', 'dumps_produkce/login.sql', 'structure + seed data') . $auth
);

$events = normalizeDump((string) file_get_contents("{$srcDir}/events.sql"), false);
file_put_contents(
    "{$dstDir}/events.sql",
    $header('events', 'dumps_produkce/events.sql', 'structure + seed data') . $events
);

$red = normalizeDump((string) file_get_contents("{$srcDir}/red_najdisi.sql"), true);
file_put_contents(
    "{$dstDir}/red.sql",
    $header(
        'red',
        'dumps_produkce/red_najdisi.sql',
        'STRUCTURE ONLY — full data dump remains gitignored under docker/migrations/dumps/dumps_produkce/'
    ) . $red
);

foreach (['auth.sql', 'events.sql', 'red.sql'] as $f) {
    $p = "{$dstDir}/{$f}";
    $body = (string) file_get_contents($p);
    echo $f
        . ' bytes=' . filesize($p)
        . ' schema_mig=' . (preg_match('/schema_migrations/i', $body) ? 'YES' : 'no')
        . ' creates=' . preg_match_all('/CREATE\s+TABLE/i', $body)
        . PHP_EOL;
}
