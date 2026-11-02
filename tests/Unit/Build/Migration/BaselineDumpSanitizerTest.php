<?php

declare(strict_types=1);

namespace Test\Unit\Build\Migration;

use Build\Migration\BaselineDumpSanitizer;
use PHPUnit\Framework\TestCase;

final class BaselineDumpSanitizerTest extends TestCase
{
    public function testStripsLineCommentsThatContainSemicolons(): void
    {
        $sql = <<<'SQL'
-- No schema-qualified identifiers; import into the target DB connection.

SET FOREIGN_KEY_CHECKS=0;
CREATE TABLE t (id INT);
SET FOREIGN_KEY_CHECKS=1;
SQL;

        $out = BaselineDumpSanitizer::sanitize($sql);

        $this->assertStringNotContainsString('import into the target', $out);
        $this->assertStringContainsString('SET FOREIGN_KEY_CHECKS=0;', $out);
        $this->assertStringContainsString('CREATE TABLE t (id INT);', $out);
    }

    public function testStripsDefinerFromVersionedViewComment(): void
    {
        $sql = <<<'SQL'
/*!50001 DROP VIEW IF EXISTS `pocet_pozic`*/;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`najdisicz004`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `pocet_pozic` AS select 1 AS `x` */;
SQL;

        $out = BaselineDumpSanitizer::sanitize($sql);

        $this->assertStringNotContainsString('DEFINER=', $out);
        $this->assertStringNotContainsString('najdisicz004', $out);
        $this->assertStringNotContainsString('SQL SECURITY DEFINER', $out);
        $this->assertStringContainsString('SQL SECURITY INVOKER', $out);
        $this->assertStringContainsString('VIEW `pocet_pozic`', $out);
    }

    public function testStripsGrantAndMysqlProcStyleStatements(): void
    {
        $sql = <<<'SQL'
SET FOREIGN_KEY_CHECKS=0;
GRANT SELECT ON *.* TO 'app'@'%';
SET DEFAULT ROLE NONE TO 'app'@'%';
INSERT INTO mysql.proc VALUES (1);
CREATE TABLE ok (id INT);
FLUSH PRIVILEGES;
SQL;

        $out = BaselineDumpSanitizer::sanitize($sql);

        $this->assertStringNotContainsString('GRANT ', $out);
        $this->assertStringNotContainsString('SET DEFAULT ROLE', $out);
        $this->assertStringNotContainsString('mysql.proc', $out);
        $this->assertStringNotContainsString('FLUSH PRIVILEGES', $out);
        $this->assertStringContainsString('CREATE TABLE ok (id INT);', $out);
    }

    public function testKeepsMysqlVersionedSetComments(): void
    {
        $sql = "/*!40101 SET character_set_client = utf8mb4 */;\nCREATE TABLE t (id INT);\n";
        $out = BaselineDumpSanitizer::sanitize($sql);
        $this->assertStringContainsString('/*!40101 SET character_set_client = utf8mb4 */;', $out);
    }

    public function testStripsOrphanOldVarRestores(): void
    {
        $sql = <<<'SQL'
SET FOREIGN_KEY_CHECKS=0;
CREATE TABLE t (id INT);
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
SQL;

        $out = BaselineDumpSanitizer::sanitize($sql);

        $this->assertStringNotContainsString('@OLD_FOREIGN_KEY_CHECKS', $out);
        $this->assertStringNotContainsString('@OLD_SQL_MODE', $out);
        $this->assertStringContainsString('CREATE TABLE t (id INT);', $out);
    }
}
