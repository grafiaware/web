<?php

declare(strict_types=1);

namespace Test\Unit\Build\Migration;

use Build\Migration\MigrationScanner;
use Build\Migration\MigrationTarget;
use PHPUnit\Framework\TestCase;

final class MigrationScannerTest extends TestCase
{
    public function testScansRealRedMigrationsDirectory(): void
    {
        $root = dirname(__DIR__, 4) . '/migrations';
        $this->assertDirectoryExists($root . '/red');

        $scanner = new MigrationScanner($root);
        $defs = $scanner->scan(MigrationTarget::Red);

        $this->assertNotEmpty($defs);
        $versions = array_map(static fn ($d) => $d->version, $defs);
        $sorted = $versions;
        sort($sorted);
        $this->assertSame($sorted, $versions);
        $this->assertContains('20250101000001_create_static', $versions);
        $this->assertContains('20260101000015_alter_menu_item_pretty_uri', $versions);
    }

    public function testAuthContainsLoginTokenMigration(): void
    {
        $root = dirname(__DIR__, 4) . '/migrations';
        $scanner = new MigrationScanner($root);
        $versions = array_map(static fn ($d) => $d->version, $scanner->scan(MigrationTarget::Auth));
        $this->assertContains('20261102000001_login_token', $versions);  // A1 (#44)
    }
}
