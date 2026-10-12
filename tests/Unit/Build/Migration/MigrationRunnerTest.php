<?php

declare(strict_types=1);

namespace Test\Unit\Build\Migration;

use Build\Migration\MigrationJournal;
use Build\Migration\MigrationRunner;
use Build\Migration\MigrationScanner;
use Build\Migration\MigrationTarget;
use Build\Migration\PdoSqlExecutor;
use Build\Migration\ReleaseBaseline;
use PDO;
use PHPUnit\Framework\TestCase;

final class MigrationRunnerTest extends TestCase
{
    private string $tmpRoot;

    protected function setUp(): void
    {
        $this->tmpRoot = sys_get_temp_dir() . '/grafia-migrations-' . uniqid('', true);
        mkdir($this->tmpRoot . '/red', 0777, true);
    }

    protected function tearDown(): void
    {
        $this->removeTree($this->tmpRoot);
    }

    public function testMigrateAppliesPendingInOrderAndIsIdempotent(): void
    {
        file_put_contents(
            $this->tmpRoot . '/red/20250101000002_second.sql',
            'CREATE TABLE t2 (id INTEGER PRIMARY KEY);'
        );
        file_put_contents(
            $this->tmpRoot . '/red/20250101000001_first.sql',
            'CREATE TABLE t1 (id INTEGER PRIMARY KEY);'
        );
        file_put_contents($this->tmpRoot . '/red/notes.sql', 'CREATE TABLE ignored (id INTEGER);');

        $pdo = new PDO('sqlite::memory:');
        $runner = $this->runner($pdo);

        $status = $runner->status(MigrationTarget::Red);
        $this->assertSame(
            ['20250101000001_first', '20250101000002_second'],
            $status['pending']
        );

        $applied = $runner->migrate(MigrationTarget::Red);
        $this->assertSame(
            ['20250101000001_first', '20250101000002_second'],
            $applied
        );
        $tables = $pdo->query(
            "SELECT name FROM sqlite_master WHERE type='table' AND name IN ('t1','t2') ORDER BY name"
        )->fetchAll(PDO::FETCH_COLUMN);
        $this->assertSame(['t1', 't2'], $tables);

        $again = $runner->migrate(MigrationTarget::Red);
        $this->assertSame([], $again);

        $statusAfter = $runner->status(MigrationTarget::Red);
        $this->assertSame([], $statusAfter['pending']);
        $this->assertSame(
            ['20250101000001_first', '20250101000002_second'],
            $statusAfter['applied']
        );
    }

    public function testBaseline2025CoversOlderFilesLeaves2026Pending(): void
    {
        file_put_contents(
            $this->tmpRoot . '/red/20240101000001_old.sql',
            'CREATE TABLE t_old (id INTEGER PRIMARY KEY);'
        );
        file_put_contents(
            $this->tmpRoot . '/red/20250101000001_year.sql',
            'CREATE TABLE t_2025 (id INTEGER PRIMARY KEY);'
        );
        file_put_contents(
            $this->tmpRoot . '/red/20260101000001_next.sql',
            'CREATE TABLE t_2026 (id INTEGER PRIMARY KEY);'
        );

        $pdo = new PDO('sqlite::memory:');
        $runner = $this->runner($pdo);

        $recorded = $runner->baseline(MigrationTarget::Red, ReleaseBaseline::PRODUCTION);
        $this->assertSame(['2025'], $recorded);

        $tables = $pdo->query(
            "SELECT name FROM sqlite_master WHERE type='table' AND name LIKE 't_%'"
        )->fetchAll(PDO::FETCH_COLUMN);
        $this->assertSame([], $tables);

        $status = $runner->status(MigrationTarget::Red);
        $this->assertSame(['2025'], $status['release_baselines']);
        $this->assertSame(['20260101000001_next'], $status['pending']);

        $applied = $runner->migrate(MigrationTarget::Red);
        $this->assertSame(['20260101000001_next'], $applied);
        $this->assertSame(
            ['t_2026'],
            $pdo->query(
                "SELECT name FROM sqlite_master WHERE type='table' AND name LIKE 't_%' ORDER BY name"
            )->fetchAll(PDO::FETCH_COLUMN)
        );
    }

    public function testBaselineIsIdempotentPerDbJournal(): void
    {
        $pdo = new PDO('sqlite::memory:');
        $runner = $this->runner($pdo);
        $this->assertSame(['2025'], $runner->baseline(MigrationTarget::Red));
        $this->assertSame([], $runner->baseline(MigrationTarget::Red));
    }

    private function runner(PDO $pdo): MigrationRunner
    {
        return new MigrationRunner(
            new MigrationScanner($this->tmpRoot),
            new MigrationJournal($pdo),
            new PdoSqlExecutor($pdo)
        );
    }

    private function removeTree(string $path): void
    {
        if (!is_dir($path)) {
            return;
        }
        $items = scandir($path) ?: [];
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $full = $path . DIRECTORY_SEPARATOR . $item;
            if (is_dir($full)) {
                $this->removeTree($full);
            } else {
                unlink($full);
            }
        }
        rmdir($path);
    }
}
