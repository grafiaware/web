<?php
declare(strict_types=1);

namespace Test\Unit\Events\Lottery;

use DateTimeImmutable;
use Events\Service\Lottery\PresenceCalculator;
use PHPUnit\Framework\TestCase;

/**
 * D1: platnost losu na místě – akceptační kritérium 6 (rozpis 11:00–14:00, 30 min, 2 kola, prodloužení 60 min).
 */
final class PresenceCalculatorTest extends TestCase {

    private PresenceCalculator $calc;
    private array $rounds;

    protected function setUp(): void {
        $this->calc = new PresenceCalculator(30, 2);
        $this->rounds = [$this->t('13:00'), $this->t('11:00'), $this->t('14:00'), $this->t('12:00')];  // záměrně neseřazené
    }

    private function t(string $hm): DateTimeImmutable {
        return new DateTimeImmutable("2027-01-27 {$hm}:00");
    }

    private function plays(array $presence): array {
        return array_map(fn($d) => $d->format('H:i'),
            $this->calc->eligibleRounds($presence['eligibleFrom'], $presence['validUntil'], $this->rounds));
    }

    public function testIssuedAt1020PlaysTwoRounds(): void {
        $p = $this->calc->issue($this->t('10:20'), $this->rounds);
        $this->assertSame(['11:00', '12:00'], $this->plays($p));
        $this->assertFalse($this->calc->isValidAt($p['validUntil'], $this->t('12:01')));
    }

    public function testThirtyMinuteBoundary(): void {
        $this->assertSame(['11:00', '12:00'], $this->plays($this->calc->issue($this->t('10:30'), $this->rounds)));
        $this->assertSame(['12:00', '13:00'], $this->plays($this->calc->issue($this->t('10:31'), $this->rounds)));
    }

    public function testRefreshExtendsOnlyForward(): void {
        $p = $this->calc->issue($this->t('10:20'), $this->rounds);
        $p['validUntil'] = $this->calc->refresh($p['validUntil'], $this->t('12:30'), 60);
        $this->assertSame(['11:00', '12:00', '13:00'], $this->plays($p));

        $q = $this->calc->issue($this->t('10:20'), $this->rounds);
        $q['validUntil'] = $this->calc->refresh($q['validUntil'], $this->t('10:45'), 60);
        $this->assertSame(['11:00', '12:00'], $this->plays($q), 'prodloužení v 10:45 platnost nezkrátí');
    }

    public function testReissueAfterExpiry(): void {
        $p = $this->calc->issue($this->t('13:10'), $this->rounds);
        $this->assertSame(['14:00'], $this->plays($p), 'méně kol v rozpisu – poslední kolo');
    }

    public function testNoRoundLeft(): void {
        $p = $this->calc->issue($this->t('13:45'), $this->rounds);
        $this->assertSame([], $this->plays($p));
    }

    public function testPlannedTimeDecides(): void {
        $p = $this->calc->issue($this->t('10:20'), $this->rounds);
        $this->assertTrue($this->calc->isEligible($p['eligibleFrom'], $p['validUntil'], $this->t('12:00')));
        $this->assertFalse($this->calc->isEligible($p['eligibleFrom'], $p['validUntil'], $this->t('12:00')->modify('+1 minute')));
    }
}
