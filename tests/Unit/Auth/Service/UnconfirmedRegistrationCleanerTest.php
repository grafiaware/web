<?php
declare(strict_types=1);

namespace Test\Unit\Auth\Service;

use Auth\Model\Dao\RegistrationDao;
use Auth\Service\UnconfirmedRegistrationCleaner;
use PHPUnit\Framework\TestCase;

/**
 * A3: náhled úklidu, čekající zástupci, počet dní do smazání (docs/vp/zadani/A3.md).
 */
final class UnconfirmedRegistrationCleanerTest extends TestCase
{
    public function testDaysLeft(): void
    {
        $now = new \DateTimeImmutable('2026-11-06 12:00:00');
        $this->assertSame(14, UnconfirmedRegistrationCleaner::daysLeft('2026-11-06 12:00:00', 14, $now));
        $this->assertSame(1, UnconfirmedRegistrationCleaner::daysLeft('2026-10-23 13:00:00', 14, $now));
        $this->assertSame(0, UnconfirmedRegistrationCleaner::daysLeft('2026-10-23 12:00:00', 14, $now));
        $this->assertSame(0, UnconfirmedRegistrationCleaner::daysLeft('2025-01-01 00:00:00', 14, $now));
    }

    public function testPreviewSplitsVisitorsAndRepresentatives(): void
    {
        $dao = $this->createStub(RegistrationDao::class);
        $dao->method('findUnconfirmed')->willReturn([
            ['login_name_fk' => 'a', 'email' => 'a@x.test', 'info' => null, 'created' => '2026-01-01 00:00:00'],
            ['login_name_fk' => 'b', 'email' => 'b@x.test', 'info' => '  ', 'created' => '2026-01-01 00:00:00'],
            ['login_name_fk' => 'c', 'email' => 'c@x.test', 'info' => 'Firma s.r.o.', 'created' => '2026-01-01 00:00:00'],
        ]);
        $cleaner = new UnconfirmedRegistrationCleaner($dao);
        $this->assertSame(['visitors' => 2, 'representatives' => 1, 'total' => 3], $cleaner->preview(14));

        $pending = $cleaner->pendingRepresentatives(14, new \DateTimeImmutable('2026-01-10 00:00:00'));
        $this->assertCount(1, $pending);
        $this->assertSame('c', $pending[0]['loginName']);
        $this->assertSame('Firma s.r.o.', $pending[0]['company']);
        $this->assertSame(5, $pending[0]['daysLeft']);
    }
}
