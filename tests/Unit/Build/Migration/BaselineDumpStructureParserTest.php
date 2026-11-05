<?php

declare(strict_types=1);

namespace Test\Unit\Build\Migration;

use Build\Migration\BaselineDumpPaths;
use Build\Migration\BaselineDumpStructureParser;
use Build\Migration\MigrationTarget;
use Build\Migration\SchemaStructure;
use Build\Migration\SchemaStructureComparer;
use Build\Migration\SchemaStructureNormalizer;
use PHPUnit\Framework\TestCase;

final class BaselineDumpStructureParserTest extends TestCase
{
    public function testParsesAuthBaselineTablesColumnsIndexesAndFks(): void
    {
        $path = BaselineDumpPaths::dumpFile(MigrationTarget::Auth);
        $this->assertFileIsReadable($path);

        $structure = BaselineDumpStructureParser::parseFile($path);
        $tables = $structure->tables();

        $this->assertSame(
            ['credentials', 'login', 'registration', 'role'],
            array_keys($tables)
        );

        $credentials = $tables['credentials'];
        $this->assertArrayHasKey('login_name_fk', $credentials['columns']);
        $this->assertArrayHasKey('password_hash', $credentials['columns']);
        $this->assertFalse($credentials['columns']['login_name_fk']['nullable']);
        $this->assertTrue($credentials['columns']['role_fk']['nullable']);
        $this->assertSame('primary', $credentials['indexes']['primary']['name']);
        $this->assertSame(['login_name_fk'], $credentials['indexes']['primary']['columns']);
        $this->assertSame('INDEX', $credentials['indexes']['c_idx']['type']);
        $this->assertNotEmpty($credentials['foreign_keys']);
        $this->assertSame('innodb', $credentials['engine']);
        $this->assertSame('utf8mb3', $credentials['charset']);
    }

    public function testParsesEventsCompanyWithCompositeUniqueAndFk(): void
    {
        $path = BaselineDumpPaths::dumpFile(MigrationTarget::Events);
        $structure = BaselineDumpStructureParser::parseFile($path);
        $company = $structure->table('company');
        $this->assertNotNull($company);
        $this->assertSame('int unsigned', $company['columns']['id']['type']);
        $this->assertStringContainsString('auto_increment', $company['columns']['id']['extra']);
        $this->assertSame('UNIQUE', $company['indexes']['name_and_version_unique']['type']);
        $this->assertSame(['name', 'version_fk'], $company['indexes']['name_and_version_unique']['columns']);
        $this->assertNotEmpty($company['foreign_keys']);
    }

    public function testParsesRedPaperFulltextAndFk(): void
    {
        $path = BaselineDumpPaths::dumpFile(MigrationTarget::Red);
        $structure = BaselineDumpStructureParser::parseFile($path);
        $paper = $structure->table('paper');
        $this->assertNotNull($paper);
        $this->assertSame('FULLTEXT', $paper['indexes']['searchpaper']['type']);
        $this->assertSame(['headline', 'perex'], $paper['indexes']['searchpaper']['columns']);
        $this->assertSame('utf8mb3', $paper['charset']); // dump CHARSET=utf8 → utf8mb3
        $this->assertCount(17, $structure->tables());
    }

    public function testComparerReportsMissingAndExtra(): void
    {
        $sql = <<<'SQL'
CREATE TABLE `login` (
  `login_name` varchar(50) NOT NULL,
  PRIMARY KEY (`login_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
        $expected = BaselineDumpStructureParser::parse($sql);
        $actual = new SchemaStructure([
            'login' => [
                'name' => 'login',
                'engine' => 'innodb',
                'charset' => 'utf8mb3',
                'collation' => null,
                'columns' => [
                    'login_name' => [
                        'name' => 'login_name',
                        'type' => 'varchar(50)',
                        'nullable' => false,
                        'default' => null,
                        'extra' => '',
                        'charset' => 'utf8mb3',
                        'collation' => null,
                    ],
                    'extra_col' => [
                        'name' => 'extra_col',
                        'type' => 'int(11)',
                        'nullable' => true,
                        'default' => null,
                        'extra' => '',
                        'charset' => null,
                        'collation' => null,
                    ],
                ],
                'indexes' => [
                    'primary' => [
                        'name' => 'primary',
                        'type' => 'PRIMARY',
                        'columns' => ['login_name'],
                    ],
                ],
                'foreign_keys' => [],
            ],
            'orphan' => [
                'name' => 'orphan',
                'engine' => 'innodb',
                'charset' => 'utf8mb3',
                'collation' => null,
                'columns' => [],
                'indexes' => [],
                'foreign_keys' => [],
            ],
        ]);

        $diff = SchemaStructureComparer::compare($expected, $actual);
        $this->assertFalse($diff->isEmpty());
        $text = implode("\n", $diff->lines());
        $this->assertStringContainsString('navíc tabulka `orphan`', $text);
        $this->assertStringContainsString('navíc sloupec `extra_col`', $text);
    }

    public function testComparerMatchIsEmpty(): void
    {
        $sql = <<<'SQL'
CREATE TABLE `role` (
  `role` varchar(50) NOT NULL,
  PRIMARY KEY (`role`),
  UNIQUE KEY `role_UNIQUE` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL;
        $expected = BaselineDumpStructureParser::parse($sql);
        $actual = BaselineDumpStructureParser::parse($sql);
        $diff = SchemaStructureComparer::compare($expected, $actual);
        $this->assertTrue($diff->isEmpty());
    }

    /**
     * False positive z PR #81: nullable DEFAULT '' v dumpu vs information_schema COLUMN_DEFAULT=''
     * (PDO string bez uvozovek) — dříve normalizer převáděl live '' → null → „default NULL ≠ dump ''“.
     */
    public function testEmptyStringDefaultMatchesInformationSchemaEmpty(): void
    {
        $sql = <<<'SQL'
CREATE TABLE `company_to_network` (
  `company_id` int(10) unsigned NOT NULL,
  `network_id` int(10) unsigned NOT NULL,
  `link` varchar(200) DEFAULT '',
  `published` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`company_id`,`network_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
SQL;
        $expected = BaselineDumpStructureParser::parse($sql);
        $this->assertSame('', $expected->table('company_to_network')['columns']['link']['default']);
        $this->assertSame('0', $expected->table('company_to_network')['columns']['published']['default']);

        // Simulace LiveSchemaStructureReader po defaultValue(COLUMN_DEFAULT, nullable):
        // IS vrací '' / '0' bez dumpových uvozovek.
        $fromLive = BaselineDumpStructureParser::parse($sql);
        $tables = $fromLive->tables();
        $tables['company_to_network']['columns']['link']['default'] =
            SchemaStructureNormalizer::defaultValue('', true);
        $tables['company_to_network']['columns']['published']['default'] =
            SchemaStructureNormalizer::defaultValue('0', true);
        $actual = new SchemaStructure($tables, $fromLive->views());

        $diff = SchemaStructureComparer::compare($expected, $actual);
        $this->assertTrue(
            $diff->isEmpty(),
            'DEFAULT \'\' nesmí hlásit NULL ≠ dump \'\': ' . implode('; ', $diff->lines())
        );
    }

    public function testNotNullImplicitEmptyDefaultMatchesMissingDumpDefault(): void
    {
        $sql = <<<'SQL'
CREATE TABLE `t` (
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
SQL;
        $expected = BaselineDumpStructureParser::parse($sql);
        $this->assertNull($expected->table('t')['columns']['name']['default']);

        // MySQL 5.6: NOT NULL bez DEFAULT → COLUMN_DEFAULT často ''
        $normalized = SchemaStructureNormalizer::defaultValue('', false);
        $this->assertNull($normalized);

        $tables = $expected->tables();
        $tables['t']['columns']['name']['default'] = $normalized;
        $actual = new SchemaStructure($tables, $expected->views());
        $diff = SchemaStructureComparer::compare($expected, $actual);
        $this->assertTrue($diff->isEmpty());
    }

    public function testIgnoresSchemaMigrationsTableInDump(): void
    {
        $sql = <<<'SQL'
CREATE TABLE `schema_migrations` (
  `version` varchar(64) NOT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB;
CREATE TABLE `login` (
  `login_name` varchar(50) NOT NULL,
  PRIMARY KEY (`login_name`)
) ENGINE=InnoDB;
SQL;
        $structure = BaselineDumpStructureParser::parse($sql);
        $this->assertFalse($structure->hasTable('schema_migrations'));
        $this->assertTrue($structure->hasTable('login'));
    }
}
