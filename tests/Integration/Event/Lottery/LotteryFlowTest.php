<?php
declare(strict_types=1);

namespace Test\Integration\Event\Lottery;

use Container\EventsModelContainerConfigurator;
use DateTimeImmutable;
use Events\Model\Dao\Lottery\LotteryAnswerDao;
use Events\Model\Dao\Lottery\LotteryDao;
use Events\Model\Dao\Lottery\LotteryPresenceDao;
use Events\Model\Dao\Lottery\LotteryPresenceEventDao;
use Events\Model\Dao\Lottery\LotteryPrizeDao;
use Events\Model\Dao\Lottery\LotteryQuestionDao;
use Events\Model\Dao\Lottery\LotteryRoundDao;
use Events\Model\Dao\Lottery\LotteryTicketDao;
use Events\Model\Dao\Lottery\LotteryWinnerDao;
use Events\Model\Dao\Lottery\LotteryPaperTicketDao;
use Events\Model\Dao\Lottery\VisitorCodeDao;
use Events\Service\Lottery\CryptoRandomSource;
use Events\Service\Lottery\LotteryConfig;
use Events\Service\Lottery\LotteryDrawService;
use Events\Service\Lottery\LotteryScanService;
use Events\Service\Lottery\LotteryPaperService;
use Events\Service\Lottery\Participant;
use Events\Service\Lottery\LotteryService;
use Pes\Container\Container;
use Test\AppRunner\AppRunner;
use Test\Integration\Event\Container\TestDbEventsContainerConfigurator;

/**
 * D1 (#46): zapojení s anketou, kód, sken u vchodu a na infostánku, losování kola, náhradník, převzetí – proti DB events.
 * D1c (#108): papírové lístky – sada, sken kódu a čísla, lístky a registrovaní v témž slosování.
 * Data testu mají prefix a po testu se smažou (losování kaskádou, loginy zvlášť).
 */
final class LotteryFlowTest extends AppRunner {

    private const PREFIX = 'LotteryFlowTest_';

    private static Container $c;
    private static DateTimeImmutable $now;
    private static array $logins = [];
    private static int $lotteryId;

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        self::$c = (new EventsModelContainerConfigurator())->configure(
            (new TestDbEventsContainerConfigurator())->configure(new Container()));
        self::cleanup();
        foreach (['a', 'b', 'c', 'd'] as $k) {
            $login = self::PREFIX . $k . uniqid();
            self::$logins[$k] = $login;
            self::$c->get(LotteryDao::class)->exec("INSERT INTO login (login_name) VALUES (:n)", ['n' => $login]);
        }
        self::$now = new DateTimeImmutable('2027-01-27 09:00:00', new \DateTimeZone('Europe/Prague'));
    }

    public static function tearDownAfterClass(): void {
        self::cleanup();
    }

    private static function cleanup(): void {
        $dao = self::$c->get(LotteryDao::class);
        $dao->exec("DELETE FROM lottery WHERE name LIKE :p", ['p' => self::PREFIX . '%']);
        $dao->exec("DELETE FROM login WHERE login_name LIKE :p", ['p' => self::PREFIX . '%']);   // kaskáda: kódy, odpovědi, losy
    }

    private function service(): LotteryService {
        $c = self::$c;
        return new LotteryService($c->get(LotteryDao::class), $c->get(LotteryRoundDao::class), $c->get(LotteryPrizeDao::class),
            $c->get(LotteryQuestionDao::class), $c->get(LotteryAnswerDao::class), $c->get(LotteryTicketDao::class),
            $c->get(VisitorCodeDao::class), $c->get(LotteryPresenceDao::class), $c->get(LotteryPresenceEventDao::class),
            $c->get(LotteryWinnerDao::class), LotteryConfig::fromArray([]), new CryptoRandomSource(), fn() => self::$now);
    }

    private function draw(LotteryService $s): LotteryDrawService {
        return new LotteryDrawService($s, self::$c->get(LotteryWinnerDao::class), new CryptoRandomSource());
    }

    private function at(string $hm): void {
        self::$now = new DateTimeImmutable("2027-01-27 {$hm}:00", new \DateTimeZone('Europe/Prague'));
    }

    private function papers(LotteryService $s): LotteryPaperService {
        return new LotteryPaperService($s, self::$c->get(LotteryPaperTicketDao::class), new CryptoRandomSource());
    }

    public function testFlow(): void {
        $s = $this->service();
        $scan = new LotteryScanService($s, $this->papers($s));
        [$a, $b, $cc, $d] = array_values(self::$logins);

        $id = $s->saveLottery(null, ['name' => self::PREFIX . 'veletrh', 'status' => 'open']);
        self::$lotteryId = $id;
        $r11 = $s->saveRound($id, null, '2027-01-27T11:00', false);
        $r12 = $s->saveRound($id, null, '2027-01-27T12:00', true);   // kolo s náhradníkem
        $s->saveRound($id, null, '2027-01-27T13:00', false);
        $s->savePrize($id, null, ['name' => 'Batoh', 'quantity' => 1, 'round_id_fk' => $r11]);
        $special = $s->savePrize($id, null, ['name' => 'Zvláštní cena', 'quantity' => 1, 'round_id_fk' => $r12]);
        $q = $s->saveQuestion($id, null, ['text' => 'Odkud jste?', 'type' => 'single', 'options' => "Brno\nPraha", 'required' => 1]);

        // zapojení: bez souhlasu a odpovědi ne, pak ano
        $this->assertNotEmpty($s->join($id, $a, false, []));
        $this->assertFalse($s->isJoined($id, $a));
        foreach ([$a, $b, $cc] as $login) {
            $this->assertSame([], $s->join($id, $login, true, [$q => 'Brno']));
        }
        $this->assertSame([], $s->join($id, $a, true, [$q => 'Praha']), 'opakované zapojení jen změní odpověď');
        $this->assertSame(1, $s->ticketCount($id, $a));
        $this->assertSame([$q => 'Praha'], $s->answers($id, $a));

        // kód je neměnný
        $codeA = $s->codeFor($a);
        $this->assertSame($codeA, $s->codeFor($a));
        $this->assertSame($a, $s->loginByCode($codeA));

        // sken u vchodu
        $this->at('10:20');
        $res = $scan->scan($id, 'https://example.test/events/v1/scan/' . $codeA, 'vchod', null, 'obsluha');
        $this->assertSame('issued', $res['result'], $res['message']);
        $this->assertStringContainsString('11:00', $res['message']);
        $this->assertSame('not_joined', $scan->scan($id, $s->codeFor($d), 'vchod', null, 'obsluha')['result']);
        $this->assertSame('invalid_code', $scan->scan($id, 'nesmysl', 'vchod', null, 'obsluha')['result']);
        $this->assertSame('not_valid', $scan->scan($id, $s->codeFor($b), 'infostanek', null, 'obsluha')['result'], 'infostánek nevydává');
        // fronta offline: sken v 10:25 odeslaný v 10:50
        $this->at('10:50');
        $offline = (new DateTimeImmutable('2027-01-27 10:25:00', new \DateTimeZone('Europe/Prague')))->format(DATE_ATOM);
        $this->assertSame('issued', $scan->scan($id, $s->codeFor($b), 'vchod', $offline, 'obsluha')['result']);
        $this->assertSame('2027-01-27 10:25:00', $s->presence($id, $b)['issued']);
        // c dostane los pozdě – do 11:00 nehraje
        $this->assertSame('issued', $scan->scan($id, $s->codeFor($cc), 'vchod', null, 'obsluha')['result']);

        // kolo 11:00: účastní se a, b (c ne – vydán v 10:50)
        $this->at('11:00');
        $draw = $this->draw($s);
        $round11 = $s->round($r11);
        $this->assertEqualsCanonicalizing([Participant::visitor($a), Participant::visitor($b)], array_keys($draw->participants($round11)));
        $w = $draw->drawOne($r11, null, 'obsluha');
        $this->assertNotNull($w);
        $this->assertContains($w['login_login_name_fk'], [$a, $b]);
        $this->assertNull($draw->drawOne($r11, null, 'obsluha'), 'cena kola je rozdaná');
        $this->assertFalse($draw->substitute((int) $w['id'], 'obsluha')['forfeited'], 'kolo bez náhradníka');
        $this->assertTrue($draw->claim((int) $w['id'], false, 'obsluha'));
        $first = $w['login_login_name_fk'];
        $other = $first === $a ? $b : $a;

        // kolo 12:00 s náhradníkem: výherce z 11:00 nehraje
        $this->at('12:00');
        $round12 = $s->round($r12);
        $this->assertArrayNotHasKey(Participant::visitor($first), $draw->participants($round12));
        $w2 = $draw->drawOne($r12, $special, 'obsluha');
        $this->assertNotNull($w2);
        $sub = $draw->substitute((int) $w2['id'], 'obsluha');
        $this->assertTrue($sub['forfeited']);
        $this->assertNotNull($sub['winner']);
        $this->assertNotSame($w2['login_login_name_fk'], $sub['winner']['login_login_name_fk'], 'propadlý výherce o tutéž cenu nehraje');
        $this->assertSame((int) $w2['id'], (int) $sub['winner']['replaces_id_fk']);
        $unclaimed = $draw->winnersOfLottery($id, true);
        $this->assertCount(1, $unclaimed);

        // prodloužení na infostánku ve 12:30 (los propadl ve 12:00) → platí do 13:30, hraje ve 13:00
        $this->at('12:30');
        $res = $scan->scan($id, $s->codeFor($other), 'infostanek', null, 'obsluha');
        $this->assertSame('refreshed', $res['result'], $res['message']);
        $this->assertSame('2027-01-27 13:30:00', $s->presence($id, $other)['valid_until']);
        $this->assertStringContainsString('13:00', $res['message']);
        // u vchodu se propadlý los vydá znovu (znovu 30 min): ve 12:40 → hraje až od 13:10
        $this->at('12:40');
        $this->assertSame('issued', $scan->scan($id, $s->codeFor($first), 'vchod', null, 'obsluha')['result']);
        $this->assertSame('2027-01-27 13:10:00', $s->presence($id, $first)['eligible_from']);
        $status = $s->visitorStatus($id, $first);
        $this->assertCount(1, $status['wins']);
        $this->assertTrue($status['joined']);
    }

    public function testCopyRound(): void {
        $s = $this->service();
        $id = $s->saveLottery(null, ['name' => self::PREFIX . 'duplikat', 'status' => 'open']);
        $other = $s->saveLottery(null, ['name' => self::PREFIX . 'duplikat-jine', 'status' => 'open']);
        $r = $s->saveRound($id, null, '2027-01-27T11:00', true);
        $s->savePrize($id, null, ['name' => 'Batoh', 'quantity' => 2, 'priority' => 3, 'round_id_fk' => $r]);
        $s->savePrize($id, null, ['name' => 'Hrnek', 'quantity' => 1, 'round_id_fk' => $r]);
        $s->savePrize($id, null, ['name' => 'Kterékoli kolo', 'quantity' => 1, 'round_id_fk' => 0]);

        $copy = $s->copyRound($id, $r, (string) LotteryService::dtFromParts('2027-01-27', '14:30'));
        $this->assertNotNull($copy);
        $round = $s->round($copy);
        $this->assertSame('2027-01-27 14:30:00', $round['draw_at']);
        $this->assertSame(1, (int) $round['substitute']);
        $this->assertSame('planned', $round['status']);
        $copied = array_values(array_filter($s->prizes($id), fn($p) => (int) $p['round_id_fk'] === $copy));
        $this->assertSame(['Hrnek', 'Batoh'], array_column($copied, 'name'), 'řazení podle pořadí');
        $this->assertSame([1, 2], array_map('intval', array_column($copied, 'quantity')));
        $this->assertSame(3, (int) $copied[1]['priority']);
        $this->assertCount(5, $s->prizes($id), 'cena bez kola se nekopíruje');

        $this->assertNull($s->copyRound($id, $r, ''), 'bez času');
        $this->assertNull($s->copyRound($other, $r, '2027-01-27 15:00'), 'kolo jiného losování');
        $this->assertCount(2, $s->rounds($id));
    }

    public function testPaperTickets(): void {
        $this->at('09:00');
        $s = $this->service();
        $papers = $this->papers($s);
        $scan = new LotteryScanService($s, $papers);
        $draw = $this->draw($s);
        $visitor = self::$logins['d'];

        $id = $s->saveLottery(null, ['name' => self::PREFIX . 'listky', 'status' => 'open']);
        $other = $s->saveLottery(null, ['name' => self::PREFIX . 'jine', 'status' => 'open']);
        $r11 = $s->saveRound($id, null, '2027-01-27T11:00', true);
        $s->savePrize($id, null, ['name' => 'Hrnek', 'quantity' => 3, 'round_id_fk' => $r11]);
        $this->assertSame([], $s->join($id, $visitor, true, []));

        // sady: čísla navazují, kódy různé
        $this->assertSame(['from' => 1, 'to' => 3], $papers->createSet($id, 3));
        $this->assertSame(['from' => 4, 'to' => 5], $papers->createSet($id, 2));
        $tickets = $papers->tickets($id);
        $this->assertSame([1, 2, 3, 4, 5], array_map(fn($t) => (int) $t['number'], $tickets));
        $this->assertCount(5, array_unique(array_column($tickets, 'code')));
        $papers->createSet($other, 1);
        $foreign = $papers->tickets($other)[0];

        // sken u vchodu: QR (adresa s L<kód>) i ruční číslo
        $this->at('10:20');
        $res = $scan->scan($id, 'https://example.test/events/v1/scan/' . LotteryPaperService::scanCode($tickets[0]['code']), 'vchod', null, 'obsluha');
        $this->assertSame('issued', $res['result'], $res['message']);
        $this->assertSame('Lístek č. 0001', $res['name']);
        $this->assertStringContainsString('11:00', $res['message']);
        $this->assertSame('issued', $scan->scan($id, '0002', 'vchod', null, 'obsluha')['result']);
        $this->assertSame('refreshed', $scan->scan($id, '2', 'vchod', null, 'obsluha')['result'], 'opakovaný sken prodlouží');
        $this->assertSame('not_valid', $scan->scan($id, '3', 'infostanek', null, 'obsluha')['result'], 'infostánek lístek nevydává');
        $this->assertSame('unknown_code', $scan->scan($id, '0099', 'vchod', null, 'obsluha')['result']);
        $this->assertSame('unknown_code', $scan->scan($id, 'L' . str_repeat('2', 12), 'vchod', null, 'obsluha')['result']);
        $this->assertSame('other_lottery', $scan->scan($id, LotteryPaperService::scanCode($foreign['code']), 'vchod', null, 'obsluha')['result']);
        $this->assertSame('issued', $scan->scan($id, $s->codeFor($visitor), 'vchod', null, 'obsluha')['result'], 'kód návštěvníka dál funguje');
        $stats = $papers->stats($id);
        $this->assertSame([5, 2], [$stats['printed'], $stats['issued']]);

        // kolo 11:00: lístky 1 a 2 (1 los) a registrovaný návštěvník
        $this->at('11:00');
        $round = $s->round($r11);
        $participants = $draw->participants($round);
        $p1 = Participant::paper((int) $tickets[0]['id']);
        $p2 = Participant::paper((int) $tickets[1]['id']);
        $this->assertEqualsCanonicalizing([$p1, $p2, Participant::visitor($visitor)], array_keys($participants));
        $this->assertSame(1, $participants[$p1]);

        $w = $draw->drawOne($r11, null, 'obsluha');
        $this->assertNotNull($w);
        // náhradník: propadlý účastník (lístek i návštěvník) o tutéž cenu nehraje
        $sub = $draw->substitute((int) $w['id'], 'obsluha');
        $this->assertTrue($sub['forfeited']);
        $this->assertNotNull($sub['winner']);
        $this->assertNotSame(Participant::ofRow($w), Participant::ofRow($sub['winner']));
        $last = $draw->drawOne($r11, null, 'obsluha');
        $this->assertNotNull($last, 'třetí kus ceny dostane zbývající účastník');
        $this->assertNull($draw->drawOne($r11, null, 'obsluha'), 'všichni účastníci vyhráli nebo propadli');
        $this->assertNotSame(Participant::ofRow($w), Participant::ofRow($last));

        $all = $draw->winnersOfLottery($id);
        $this->assertCount(3, $all);
        $paperWins = array_values(array_filter($all, fn($x) => $x['paper_ticket_id_fk'] !== null));
        $this->assertNotEmpty($paperWins, 'aspoň jeden ze dvou lístků vyhrál');
        foreach ($paperWins as $x) {
            $this->assertNull($x['login_login_name_fk']);
            $this->assertSame('Lístek č. ' . LotteryPaperService::formatNumber((int) $x['paper_number']), $x['display_name']);
        }
        $status = $papers->status($papers->byNumber($id, (int) $paperWins[0]['paper_number']));
        $this->assertCount(1, $status['wins']);
        $this->assertTrue($status['valid']);
    }
}
