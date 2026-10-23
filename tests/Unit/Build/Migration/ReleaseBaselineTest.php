<?php

declare(strict_types=1);

namespace Test\Unit\Build\Migration;

use Build\Migration\ReleaseBaseline;
use PHPUnit\Framework\TestCase;

final class ReleaseBaselineTest extends TestCase
{
    public function testProductionIsYear2025NotSemver(): void
    {
        $this->assertSame('2025', ReleaseBaseline::PRODUCTION);
        $this->assertTrue(ReleaseBaseline::isValid('2025'));
        $this->assertFalse(ReleaseBaseline::isValid('2025.1'));
        $this->assertFalse(ReleaseBaseline::isValid('20250101000001_create_static'));
    }

    public function testCoverageByReleaseMarker(): void
    {
        $applied = ['2025' => true];
        $this->assertTrue(ReleaseBaseline::isCoveredByReleaseMarkers('20240101000001_x', $applied));
        $this->assertTrue(ReleaseBaseline::isCoveredByReleaseMarkers('20250101000001_x', $applied));
        $this->assertFalse(ReleaseBaseline::isCoveredByReleaseMarkers('20260101000001_x', $applied));
    }
}
