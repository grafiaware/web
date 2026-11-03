<?php

/**
 * CLI rebuild DB na schema aktuální větve:
 * drop tables → import baseline dump 2025 → baseline žurnál → migrate pending.
 *
 * Usage:
 *   php scripts/db-rebuild.php --target=red|auth|events|all --yes
 *   php scripts/db-rebuild.php --target=all --yes --force-prod   # nebezpečné
 *   php scripts/db-rebuild.php --drift-check
 *
 * Guard: na produkčním hostu (PES_RUNNING_ON_PRODUCTION_HOST) odmítne rebuild
 * bez --force-prod.
 */

declare(strict_types=1);

use Build\Migration\DatabaseRebuilder;
use Build\Migration\MigrationConnectionFactory;
use Build\Migration\MigrationDriftDetector;
use Build\Migration\MigrationDriftGuard;
use Build\Migration\MigrationTarget;
use Build\Migration\ReleaseBaseline;
use Site\Common\ActiveSite;
use Site\ConfigurationCache;

$projectRoot = dirname(__DIR__);
chdir($projectRoot);

if (!isset($_SERVER['DOCUMENT_ROOT']) || $_SERVER['DOCUMENT_ROOT'] === '') {
    $_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', $projectRoot);
}
$_SERVER['SERVER_NAME'] = $_SERVER['SERVER_NAME'] ?? 'localhost';
$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'localhost';

$bootstrapEntryFile = $projectRoot . '/vendor/pes/pes-bootstrap/src/BootstrapEntry.php';
if (!is_readable($bootstrapEntryFile)) {
    fwrite(STDERR, "Missing bootstrap: {$bootstrapEntryFile}\n");
    exit(1);
}
require_once $bootstrapEntryFile;
Pes\Bootstrap\BootstrapEntry::load();

$opts = parseCliArgs($argv);
if ($opts['help']) {
    printUsage();
    exit(0);
}

if (isset($opts['site']) && $opts['site'] !== '') {
    ActiveSite::force($opts['site']);
    ConfigurationCache::resetCache();
}

$factory = new MigrationConnectionFactory($projectRoot);

if ($opts['drift_check']) {
    exit(runDriftCheck($factory, $opts['targets']));
}

if (!$opts['yes']) {
    fwrite(STDERR, "Chybí --yes (destruktivní: drop tabulek + import baseline).\n");
    printUsage();
    exit(2);
}

$onProd = defined('PES_RUNNING_ON_PRODUCTION_HOST') && PES_RUNNING_ON_PRODUCTION_HOST;
if ($onProd && !$opts['force_prod']) {
    fwrite(
        STDERR,
        "Odmítnuto: PES_RUNNING_ON_PRODUCTION_HOST=true. "
        . "Rebuild jen s explicitním --force-prod (nebezpečné).\n"
    );
    exit(3);
}
if ($onProd && $opts['force_prod']) {
    fwrite(STDERR, "VAROVÁNÍ: rebuild na produkčním hostu (--force-prod).\n");
}

$rebuilder = new DatabaseRebuilder($factory, $projectRoot, $opts['release']);
$exit = 0;
foreach ($opts['targets'] as $target) {
    fwrite(STDOUT, "== Rebuild {$target->value} ==\n");
    try {
        $result = $rebuilder->rebuild($target);
        fwrite(STDOUT, "DB: {$result['database']}\n");
        fwrite(STDOUT, "Dump: {$result['dump']}\n");
        fwrite(STDOUT, 'Dropped tables: ' . count($result['dropped_tables']) . "\n");
        fwrite(STDOUT, 'Baseline: ' . ($result['baseline'] === [] ? '(už bylo)' : implode(',', $result['baseline'])) . "\n");
        fwrite(STDOUT, 'Migrated: ' . ($result['migrated'] === [] ? '(nic)' : implode(',', $result['migrated'])) . "\n");
        fwrite(STDOUT, 'Pending after: ' . ($result['pending_after'] === [] ? '(ok)' : implode(',', $result['pending_after'])) . "\n");
    } catch (Throwable $e) {
        fwrite(STDERR, "FAIL {$target->value}: {$e->getMessage()}\n");
        $exit = 1;
    }
}

exit($exit);

/**
 * @return array{
 *   help: bool,
 *   yes: bool,
 *   force_prod: bool,
 *   drift_check: bool,
 *   release: string,
 *   site: string,
 *   targets: list<MigrationTarget>
 * }
 */
function parseCliArgs(array $argv): array
{
    $help = false;
    $yes = false;
    $forceProd = false;
    $driftCheck = false;
    $release = ReleaseBaseline::PRODUCTION;
    $site = '';
    $targetRaw = 'all';

    foreach (array_slice($argv, 1) as $arg) {
        if ($arg === '--help' || $arg === '-h') {
            $help = true;
            continue;
        }
        if ($arg === '--yes' || $arg === '-y') {
            $yes = true;
            continue;
        }
        if ($arg === '--force-prod') {
            $forceProd = true;
            continue;
        }
        if ($arg === '--drift-check') {
            $driftCheck = true;
            continue;
        }
        if (str_starts_with($arg, '--target=')) {
            $targetRaw = substr($arg, 9);
            continue;
        }
        if (str_starts_with($arg, '--release=')) {
            $release = substr($arg, 10);
            continue;
        }
        if (str_starts_with($arg, '--site=')) {
            $site = substr($arg, 7);
            continue;
        }
        fwrite(STDERR, "Neznámý argument: {$arg}\n");
        $help = true;
    }

    if (!ReleaseBaseline::isValid($release)) {
        fwrite(STDERR, "Neplatný --release={$release} (očekáváno YYYY).\n");
        $help = true;
    }

    $targets = resolveTargets($targetRaw);

    return [
        'help' => $help,
        'yes' => $yes,
        'force_prod' => $forceProd,
        'drift_check' => $driftCheck,
        'release' => $release,
        'site' => $site,
        'targets' => $targets,
    ];
}

/**
 * @return list<MigrationTarget>
 */
function resolveTargets(string $raw): array
{
    $raw = strtolower(trim($raw));
    if ($raw === '' || $raw === 'all') {
        return MigrationTarget::all();
    }
    $parts = preg_split('/[,\s]+/', $raw) ?: [];
    $targets = [];
    foreach ($parts as $part) {
        if ($part === '') {
            continue;
        }
        $t = MigrationTarget::tryFromString($part);
        if ($t === null) {
            throw new InvalidArgumentException("Neplatný target: {$part}");
        }
        $targets[] = $t;
    }
    if ($targets === []) {
        throw new InvalidArgumentException('Žádný target.');
    }

    return $targets;
}

/**
 * @param list<MigrationTarget> $targets
 */
function runDriftCheck(MigrationConnectionFactory $factory, array $targets): int
{
    MigrationDriftGuard::reset();
    $reports = [];
    foreach ($targets as $target) {
        $runner = $factory->createRunner($target);
        $reports[] = (new MigrationDriftDetector($runner))->detect($target);
    }
    $onProd = defined('PES_RUNNING_ON_PRODUCTION_HOST') && PES_RUNNING_ON_PRODUCTION_HOST;
    foreach ($reports as $report) {
        fwrite(STDOUT, $report->summary() . "\n");
    }
    try {
        MigrationDriftGuard::evaluate($reports, (bool) $onProd);
    } catch (Throwable $e) {
        fwrite(STDERR, $e->getMessage() . "\n");

        return 1;
    }
    $bad = array_filter($reports, static fn ($r) => !$r->isOk());

    return $bad === [] ? 0 : 1;
}

function printUsage(): void
{
    fwrite(STDOUT, <<<TXT
db-rebuild.php — baseline dump 2025 + migrate (dev CLI)

  php scripts/db-rebuild.php --target=red|auth|events|all --yes
  php scripts/db-rebuild.php --drift-check [--target=red]
  php scripts/db-rebuild.php --target=all --yes --force-prod

Options:
  --target=…     red | auth | events | all (default all)
  --yes          potvrzení destruktivního rebuildu
  --force-prod   povolí rebuild i při PES_RUNNING_ON_PRODUCTION_HOST
  --release=YYYY výchozí 2025
  --site=Name    přepne ActiveSite (jinak app/Site/active-site.php)
  --drift-check  jen klasifikace ahead/behind/ok (exit 1 při driftu)
  --help

Baseline dumpy: docker/migrations/baseline/2025/{red,auth,events}.sql

TXT);
}
