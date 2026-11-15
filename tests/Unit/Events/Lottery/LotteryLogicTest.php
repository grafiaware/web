<?php
declare(strict_types=1);

namespace Test\Unit\Events\Lottery;

use DateTimeImmutable;
use Events\Service\Lottery\CryptoRandomSource;
use Events\Service\Lottery\LotteryConfig;
use Events\Service\Lottery\ScanTime;
use Events\Service\Lottery\SurveyValidator;
use Events\Service\Lottery\VisitorCodeGenerator;
use Events\Service\Lottery\WeightedDraw;
use PHPUnit\Framework\TestCase;

/**
 * D1: vážený výběr, kód návštěvníka, anketa, čas skenu z fronty offline, konfigurace.
 */
final class LotteryLogicTest extends TestCase {

    public function testWeightedDrawIsDeterministicWithSequence(): void {
        $draw = new WeightedDraw(new SequenceRandom([1, 2, 3, 4]));
        $w = ['b' => 2, 'a' => 1, 'c' => 1];   // seřazeno: a(1) b(2) c(1)
        $this->assertSame('a', $draw->pick($w));
        $this->assertSame('b', $draw->pick($w));
        $this->assertSame('b', $draw->pick($w));
        $this->assertSame('c', $draw->pick($w));
        $this->assertNull($draw->pick([]));
        $this->assertNull($draw->pick(['x' => 0]));
    }

    public function testWeightedDrawChanceMatchesTickets(): void {
        $draw = new WeightedDraw(new CryptoRandomSource());
        $counts = ['a' => 0, 'b' => 0];
        for ($i = 0; $i < 10000; $i++) {
            $counts[$draw->pick(['a' => 1, 'b' => 2])]++;
        }
        // očekávané 1/3 : 2/3, tolerance ±3 procentní body
        $this->assertEqualsWithDelta(3333, $counts['a'], 300);
        $this->assertEqualsWithDelta(6667, $counts['b'], 300);
    }

    public function testVisitorCode(): void {
        $gen = new VisitorCodeGenerator(new CryptoRandomSource());
        $codes = [];
        for ($i = 0; $i < 200; $i++) {
            $c = $gen->generate();
            $this->assertTrue(VisitorCodeGenerator::isWellFormed($c), $c);
            $this->assertDoesNotMatchRegularExpression('/[01OI]/', $c);
            $codes[$c] = true;
        }
        $this->assertCount(200, $codes);
        $this->assertSame('ABCDEFGHJKLM', VisitorCodeGenerator::fromScannedText('https://example.cz/events/v1/scan/ABCDEFGHJKLM'));
        $this->assertSame('ABCDEFGHJKLM', VisitorCodeGenerator::fromScannedText(' abcdefghjklm '));
        $this->assertNull(VisitorCodeGenerator::fromScannedText('https://example.cz/jine'));
        $this->assertNull(VisitorCodeGenerator::fromScannedText('ABCDEFGHJKL0'));
    }

    public function testSurvey(): void {
        $q = [
            ['id' => 1, 'text' => 'Odkud jste', 'type' => 'single', 'options' => "Brno\nPraha\n", 'required' => 1],
            ['id' => 2, 'text' => 'Zájmy', 'type' => 'multi', 'options' => "IT\nSklad\nObchod", 'required' => 1],
            ['id' => 3, 'text' => 'Poznámka', 'type' => 'text', 'options' => null, 'required' => 0],
        ];
        $v = new SurveyValidator();
        $ok = $v->validate($q, [1 => 'Praha', 2 => ['Obchod', 'IT', 'Hack'], 3 => '  ahoj ']);
        $this->assertSame([], $ok['errors']);
        $this->assertSame([1 => 'Praha', 2 => "IT\nObchod", 3 => 'ahoj'], $ok['answers']);

        $bad = $v->validate($q, [1 => 'Ostrava', 2 => []]);
        $this->assertArrayHasKey(1, $bad['errors']);
        $this->assertArrayHasKey(2, $bad['errors']);
        $this->assertArrayNotHasKey(3, $bad['errors']);
    }

    public function testScanTime(): void {
        $now = new DateTimeImmutable('2027-01-27 12:00:00');
        $this->assertEquals($now, ScanTime::resolve(null, $now, 240));
        $this->assertEquals(new DateTimeImmutable('2027-01-27 11:50:00'), ScanTime::resolve('2027-01-27T11:50:00' . $now->format('P'), $now, 240));
        $ms = (string) ((new DateTimeImmutable('2027-01-27 11:40:00'))->getTimestamp() * 1000);
        $this->assertEquals(new DateTimeImmutable('2027-01-27 11:40:00'), ScanTime::resolve($ms, $now, 240));
        $this->assertNull(ScanTime::resolve('2027-01-27T07:00:00' . $now->format('P'), $now, 240), 'starší než 4 h');
        $this->assertEquals($now, ScanTime::resolve('2027-01-27T13:00:00' . $now->format('P'), $now, 240), 'z budoucnosti = teď');
    }

    public function testConfigDefaultsAndOverrides(): void {
        $d = LotteryConfig::fromArray([]);
        $this->assertSame(30, $d->issueMinMinutes);
        $this->assertSame(60, $d->refreshMinutes('infostanek'));
        $this->assertNull($d->refreshMinutes('stanek'));
        $this->assertTrue($d->station('vchod')['issue']);
        $c = LotteryConfig::fromArray(['presence' => ['issueMinMinutes' => 15, 'refresh' => ['stanek' => 30]]]);
        $this->assertSame(15, $c->issueMinMinutes);
        $this->assertSame(30, $c->refreshMinutes('stanek'));
        $this->assertNull($c->refreshMinutes('vchod'));
        $this->assertNull($c->station('neznama'));
    }
}
