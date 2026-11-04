<?php

declare(strict_types=1);

namespace Test\Unit\Build\Migration;

use Build\Migration\BaselineStructureGate;
use Build\Migration\MigrationJournal;
use Build\Migration\MigrationRunner;
use Build\Migration\MigrationScanner;
use Build\Migration\MigrationTarget;
use Build\Migration\PdoSqlExecutor;
use Build\Migration\ReleaseBaseline;
use Build\Migration\SchemaStructureDiff;
use Build\Migration\SchemaStructureMismatchException;
use PDO;
use PHPUnit\Framework\TestCase;

final class BaselineStructureGateTest extends TestCase
{
    private string $tmpRoot;

    protected function setUp(): void
    {
        $this->tmpRoot = sys_get_temp_dir() . '/grafia-structure-gate-' . uniqid('', true);
        mkdir($this->tmpRoot . '/red', 0777, true);
    }

    protected function tearDown(): void
    {
        $this->removeTree($this->tmpRoot);
    }

    public function testJournalIsVirginUntilFirstRecord(): void
    {
        $pdo = new PDO('sqlite::memory:');
        $journal = new MigrationJournal($pdo);
        $this->assertTrue($journal->isVirgin());
        $this->assertFalse($journal->tableExists());
        $this->assertSame([], $journal->getAppliedVersions());
        $this->assertFalse($journal->tableExists(), 'status/read nesmí vytvořit žurnál');

        $journal->ensureTable();
        $this->assertTrue($journal->tableExists());
        $this->assertTrue($journal->isVirgin(), 'prázdná tabulka = stále virgin');

        $journal->record('2025');
        $this->assertFalse($journal->isVirgin());
    }

    public function testDisabledGateAllowsBaselineOnSqlite(): void
    {
        $pdo = new PDO('sqlite::memory:');
        $runner = new MigrationRunner(
            new MigrationScanner($this->tmpRoot),
            new MigrationJournal($pdo),
            new PdoSqlExecutor($pdo),
            BaselineStructureGate::disabled($pdo)
        );
        $recorded = $runner->baseline(MigrationTarget::Red, ReleaseBaseline::PRODUCTION);
        $this->assertSame(['2025'], $recorded);
    }

    public function testMismatchExceptionFormatsDiff(): void
    {
        $diff = new SchemaStructureDiff([
            'chybí tabulka `login` (je v dump, není v DB)',
            'navíc sloupec `x`',
        ]);
        $e = new SchemaStructureMismatchException(
            MigrationTarget::Auth,
            $diff,
            '/path/auth.sql'
        );
        $this->assertStringContainsString('auth', $e->getMessage());
        $this->assertStringContainsString('chybí tabulka `login`', $e->getMessage());
        $this->assertStringContainsString('db-rebuild.php', $e->getMessage());
    }

    public function testGateNoopsForNonMysqlDriverEvenWhenEnabled(): void
    {
        $pdo = new PDO('sqlite::memory:');
        $journal = new MigrationJournal($pdo);
        $gate = new BaselineStructureGate($pdo, $this->tmpRoot, true, 'unused');
        // Nesmí hodit — sqlite se přeskočí (dump check je MySQL-only).
        $gate->assertMatchesBaseline(MigrationTarget::Auth, $journal);
        $this->assertTrue($journal->isVirgin());
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
