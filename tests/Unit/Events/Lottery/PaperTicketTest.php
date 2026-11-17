<?php
declare(strict_types=1);

namespace Test\Unit\Events\Lottery;

use Events\Service\Lottery\LotteryConfig;
use Events\Service\Lottery\LotteryPaperService;
use Events\Service\Lottery\Participant;
use Events\Service\Lottery\VisitorCodeGenerator;
use Events\Service\Lottery\WeightedDraw;
use PHPUnit\Framework\TestCase;

/**
 * D1c (#108): rozpoznání kódu a čísla lístku ve skenu, klíče účastníků, losování se smíšenými účastníky, konfigurace tisku.
 */
final class PaperTicketTest extends TestCase {

    public function testPaperCodeFromScannedText(): void {
        $code = 'ABCDEFGHJK23';
        $this->assertSame($code, LotteryPaperService::codeFromScannedText('https://x.test/events/v1/scan/L' . $code));
        $this->assertSame($code, LotteryPaperService::codeFromScannedText(' l' . strtolower($code) . ' '));
        $this->assertNull(LotteryPaperService::codeFromScannedText($code), 'kód návštěvníka (bez L) není lístek');
        $this->assertNull(LotteryPaperService::codeFromScannedText('L' . substr($code, 1)), 'krátký kód');
        $this->assertNull(LotteryPaperService::codeFromScannedText('LABCDEFGHJK2O'), 'znak mimo abecedu');
        $this->assertNull(VisitorCodeGenerator::fromScannedText('https://x.test/events/v1/scan/L' . $code), 'lístek není kód návštěvníka');
        $this->assertSame('L' . $code, LotteryPaperService::scanCode($code));
    }

    public function testNumberFromText(): void {
        $this->assertSame(427, LotteryPaperService::numberFromText('0427'));
        $this->assertSame(5, LotteryPaperService::numberFromText(' 5 '));
        $this->assertNull(LotteryPaperService::numberFromText('0'));
        $this->assertNull(LotteryPaperService::numberFromText('12AB'));
        $this->assertNull(LotteryPaperService::numberFromText('ABCDEFGHJK23'));
        $this->assertSame('0427', LotteryPaperService::formatNumber(427));
        $this->assertSame('12345', LotteryPaperService::formatNumber(12345));
        $this->assertSame('Lístek č. 0007', LotteryPaperService::label(7));
    }

    public function testParticipantKeys(): void {
        $this->assertSame(['type' => 'v', 'login' => 'jana:novak', 'paperId' => null], Participant::parse(Participant::visitor('jana:novak')));
        $this->assertSame(['type' => 'p', 'login' => null, 'paperId' => 42], Participant::parse(Participant::paper(42)));
        $this->assertSame(Participant::paper(3), Participant::ofRow(['login_login_name_fk' => null, 'paper_ticket_id_fk' => '3']));
        $this->assertSame(Participant::visitor('x'), Participant::ofRow(['login_login_name_fk' => 'x', 'paper_ticket_id_fk' => null]));
        $this->assertNull(Participant::ofRow(['login_login_name_fk' => null]));
        $this->expectException(\InvalidArgumentException::class);
        Participant::parse('x:1');
    }

    public function testMixedDrawWeights(): void {
        // seřazeno: p:1 (1 los), p:2 (1 los), v:anna (2 losy) → hodnoty 1, 2, 3–4
        $weights = [Participant::visitor('anna') => 2, Participant::paper(2) => 1, Participant::paper(1) => 1];
        $draw = new WeightedDraw(new SequenceRandom([1, 2, 3, 4]));
        $this->assertSame(Participant::paper(1), $draw->pick($weights));
        $this->assertSame(Participant::paper(2), $draw->pick($weights));
        $this->assertSame(Participant::visitor('anna'), $draw->pick($weights));
        $this->assertSame(Participant::visitor('anna'), $draw->pick($weights));
    }

    public function testPaperPerPageConfig(): void {
        $this->assertSame(4, LotteryConfig::fromArray([])->paperPerPage);
        $this->assertSame(2, LotteryConfig::fromArray(['paper' => ['perPage' => 2]])->paperPerPage);
        $this->assertSame(4, LotteryConfig::fromArray(['paper' => ['perPage' => 3]])->paperPerPage);
    }
}
