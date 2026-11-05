<?php

declare(strict_types=1);

/**
 * Regeneruje docker/migrations/baseline/2025/{auth,events,red}.sql
 * z lokálních dumps_produkce/ (gitignore).
 *
 * Odstraní schema_migrations, schema kvalifikátory (jen známá DB jména —
 * ne table.column ve VIEW), USE/CREATE DATABASE, a přes BaselineDumpSanitizer
 * i DEFINER / SQL SECURITY DEFINER→INVOKER / utf8mb4_uca1400_ai_ci→unicode_ci /
 * privilege statements / nebezpečné komentáře.
 */

$root = dirname(__DIR__);
require $root . '/vendor/autoload.php';

use Build\Migration\BaselineDumpSanitizer;

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

/**
 * Strip only known database schema prefixes (`najdisi`.`table`),
 * never table.column / alias.column inside VIEW DDL.
 */
function stripSchemaQualifiers(string $sql): string
{
    $schemas = 'najdisi|events|login|single_login|veletrhprace|auth|red|grafia';

    return preg_replace('/`(?:' . $schemas . ')`\s*\.\s*(`[A-Za-z0-9_]+`)/i', '$1', $sql) ?? $sql;
}

/**
 * Navicat-style dumps often omit DROP before CREATE TABLE.
 * Skip when DROP TABLE IF EXISTS for the same table already appears just above
 * (mysqldump typically has DROP, then session SET, then CREATE).
 */
function ensureDropTableBeforeCreate(string $sql): string
{
    if (!preg_match_all(
        '/^CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?`([^`]+)`/im',
        $sql,
        $matches,
        PREG_OFFSET_CAPTURE | PREG_SET_ORDER
    )) {
        return $sql;
    }

    for ($i = count($matches) - 1; $i >= 0; $i--) {
        $table = $matches[$i][1][0];
        $pos = (int) $matches[$i][0][1];
        $before = substr($sql, max(0, $pos - 600), min(600, $pos));
        if (preg_match('/DROP\s+TABLE\s+IF\s+EXISTS\s+`' . preg_quote($table, '/') . '`\s*;/i', $before) === 1) {
            continue;
        }
        $sql = substr($sql, 0, $pos) . "DROP TABLE IF EXISTS `{$table}`;\n" . substr($sql, $pos);
    }

    return $sql;
}

/**
 * Ensure DROP VIEW before plain CREATE VIEW (rebuild may leave views after DROP TABLE).
 * Skip mysqldump versioned stubs (/*!50001 CREATE VIEW …) — those already have DROP VIEW.
 */
function ensureDropViewBeforeCreate(string $sql): string
{
    return preg_replace_callback(
        '/(?<!\/\*!\d{5} )\bCREATE\s+(?:ALGORITHM\s*=\s*\w+\s+)?(?:DEFINER\s*=\s*(?:`[^`]+`|\'[^\']+\'|"[^"]+"|[^\s@\'"`]+)\s*@\s*(?:`[^`]+`|\'[^\']+\'|"[^"]+"|[^\s\'"`]+)\s+)?(?:SQL\s+SECURITY\s+\w+\s+)?VIEW\s+`([^`]+)`/i',
        static function (array $m): string {
            return "DROP VIEW IF EXISTS `{$m[1]}`;\n{$m[0]}";
        },
        $sql
    ) ?? $sql;
}

/**
 * @deprecated Kept for ad-hoc structure extracts; red/auth/events baselines are full dumps.
 */
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
        $sql = extractStructureWithDrops($sql);
    } else {
        $sql = ensureDropTableBeforeCreate($sql);
        $sql = ensureDropViewBeforeCreate($sql);
        if (stripos($sql, 'FOREIGN_KEY_CHECKS') === false) {
            $sql = "SET FOREIGN_KEY_CHECKS=0;\n" . $sql . "\nSET FOREIGN_KEY_CHECKS=1;\n";
        }
    }

    // Pozn.: sanitize stripuje i `--` komentáře včetně headeru — header přidáváme až potom.
    return BaselineDumpSanitizer::sanitize($sql);
}

$header = static function (string $target, string $source, string $note): string {
    // Bez středníku v komentáři — Manipulator::sqlExplode dělí podle `;` i uvnitř `--` řádků.
    return "-- Baseline dump 2025 — target={$target}\n"
        . "-- Source: {$source}\n"
        . "-- {$note}\n"
        . "-- No schema_migrations (journal written by rebuild CLI after import).\n"
        . "-- No schema-qualified identifiers. Import into the target DB connection.\n\n";
};

foreach (['login.sql', 'events.sql', 'red_najdisi.sql'] as $src) {
    if (!is_readable("{$srcDir}/{$src}")) {
        fwrite(STDERR, "Chybí zdrojový dump: {$srcDir}/{$src}\n");
        exit(1);
    }
}

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

$red = normalizeDump((string) file_get_contents("{$srcDir}/red_najdisi.sql"), false);
file_put_contents(
    "{$dstDir}/red.sql",
    $header(
        'red',
        'dumps_produkce/red_najdisi.sql',
        'structure + full CMS seed data (project-start dump)'
    ) . $red
);

foreach (['auth.sql', 'events.sql', 'red.sql'] as $f) {
    $p = "{$dstDir}/{$f}";
    $body = (string) file_get_contents($p);
    echo $f
        . ' bytes=' . filesize($p)
        . ' schema_mig=' . (preg_match('/schema_migrations/i', $body) ? 'YES' : 'no')
        . ' definer=' . (preg_match('/DEFINER\s*=/i', $body) ? 'YES' : 'no')
        . ' security_definer=' . (preg_match('/SQL\s+SECURITY\s+DEFINER/i', $body) ? 'YES' : 'no')
        . ' inserts=' . preg_match_all('/INSERT\s+INTO/i', $body)
        . ' creates=' . preg_match_all('/CREATE\s+TABLE/i', $body)
        . PHP_EOL;
}
