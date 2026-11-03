<?php

declare(strict_types=1);

namespace Test\Unit\Build\Migration;

use Build\Migration\MigrationDriftDetector;
use Build\Migration\MigrationDriftGuard;
use Build\Migration\MigrationDriftStatus;
use Build\Migration\MigrationJournal;
use Build\Migration\MigrationRunner;
use Build\Migration\MigrationScanner;
use Build\Migration\MigrationTarget;
use Build\Migration\PdoSqlExecutor;
use Build\Migration\ReleaseBaseline;
use PDO;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class MigrationDriftDetectorTest extends TestCase
{
    private string $tmpRoot;

    protected function setUp(): void
    {
        $this->tmpRoot = sys_get_temp_dir() . '/grafia-drift-' . uniqid('', true);
        mkdir($this->tmpRoot . '/red', 0777, true);
        MigrationDriftGuard::reset();
    }

    protected function tearDown(): void
    {
        $this->removeTree($this->tmpRoot);
        MigrationDriftGuard::reset();
    }

    public function testOkWhenJournalMatchesFiles(): void
    {
        file_put_contents(
            $this->tmpRoot . '/red/20260101000001_next.sql',
            'CREATE TABLE t_next (id INTEGER PRIMARY KEY);'
        );
        $pdo = new PDO('sqlite::memory:');
        $runner = $this->runner($pdo);
        $runner->baseline(MigrationTarget::Red, ReleaseBaseline::PRODUCTION);
        $runner->migrate(MigrationTarget::Red);

        $report = (new MigrationDriftDetector($runner))->detect(MigrationTarget::Red);
        $this->assertSame(MigrationDriftStatus::Ok, $report->status);
        $this->assertSame([], $report->pending);
        $this->assertSame([], $report->unknown);
    }

    public function testBehindWhenPendingFiles(): void
    {
        file_put_contents(
            $this->tmpRoot . '/red/20260101000001_next.sql',
            'CREATE TABLE t_next (id INTEGER PRIMARY KEY);'
        );
        $pdo = new PDO('sqlite::memory:');
        $runner = $this->runner($pdo);
        $runner->baseline(MigrationTarget::Red, ReleaseBaseline::PRODUCTION);

        $report = (new MigrationDriftDetector($runner))->detect(MigrationTarget::Red);
        $this->assertSame(MigrationDriftStatus::Behind, $report->status);
        $this->assertSame(['20260101000001_next'], $report->pending);
    }

    public function testAheadWhenUnknownJournalVersions(): void
    {
        $pdo = new PDO('sqlite::memory:');
        $runner = $this->runner($pdo);
        $journal = new MigrationJournal($pdo);
        $journal->ensureTable();
        $journal->record('20260315000001_from_other_branch');

        $report = (new MigrationDriftDetector($runner))->detect(MigrationTarget::Red);
        $this->assertSame(MigrationDriftStatus::Ahead, $report->status);
        $this->assertSame(['20260315000001_from_other_branch'], $report->unknown);
    }

    public function testGuardFailsOnProductionAhead(): void
    {
        $pdo = new PDO('sqlite::memory:');
        $runner = $this->runner($pdo);
        $journal = new MigrationJournal($pdo);
        $journal->record('20260315000001_orphan');
        $report = (new MigrationDriftDetector($runner))->detect(MigrationTarget::Red);

        $this->expectException(RuntimeException::class);
        MigrationDriftGuard::evaluate([$report], true);
    }

    public function testGuardWarnsOnlyOnDev(): void
    {
        $pdo = new PDO('sqlite::memory:');
        $runner = $this->runner($pdo);
        $journal = new MigrationJournal($pdo);
        $journal->record('20260315000001_orphan');
        $report = (new MigrationDriftDetector($runner))->detect(MigrationTarget::Red);

        $messages = MigrationDriftGuard::evaluate([$report], false);
        $this->assertCount(1, $messages);
        $this->assertStringContainsString('ahead', $messages[0]);
    }

    private function runner(PDO $pdo): MigrationRunner
    {
        return new MigrationRunner(
            new MigrationScanner($this->tmpRoot),
            new MigrationJournal($pdo),
            new PdoSqlExecutor($pdo)
        );
    }

    private function removeTree(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($it as $file) {
            $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
        }
        rmdir($dir);
    }
}
