<?php

declare(strict_types=1);

namespace Test\Unit\Build\Migration;

use Build\Migration\SchemaStructureNormalizer;
use PHPUnit\Framework\TestCase;

final class SchemaStructureNormalizerTest extends TestCase
{
    public function testNullableEmptyStringDefaultIsPreserved(): void
    {
        // information_schema / PDO: COLUMN_DEFAULT = ''
        $this->assertSame('', SchemaStructureNormalizer::defaultValue('', true));
        // dump token DEFAULT ''
        $this->assertSame('', SchemaStructureNormalizer::defaultValue("''", true));
        $this->assertSame('', SchemaStructureNormalizer::defaultValue('""', true));
    }

    public function testSqlNullDefaultIsNull(): void
    {
        $this->assertNull(SchemaStructureNormalizer::defaultValue(null, true));
        $this->assertNull(SchemaStructureNormalizer::defaultValue('NULL', true));
        $this->assertNull(SchemaStructureNormalizer::defaultValue('null', false));
    }

    public function testNotNullBareEmptyBecomesNull(): void
    {
        // MySQL 5.6 quirk: NOT NULL bez DEFAULT → IS často ''
        $this->assertNull(SchemaStructureNormalizer::defaultValue('', false));
        // Explicitní DEFAULT '' u NOT NULL (dump) — po strip uvozovek stejná cesta
        $this->assertNull(SchemaStructureNormalizer::defaultValue("''", false));
    }

    public function testNonEmptyDefaults(): void
    {
        $this->assertSame('0', SchemaStructureNormalizer::defaultValue('0', true));
        $this->assertSame('abc', SchemaStructureNormalizer::defaultValue("'ABC'", true));
        $this->assertSame('current_timestamp()', SchemaStructureNormalizer::defaultValue('CURRENT_TIMESTAMP', true));
    }
}
