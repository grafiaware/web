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
use Events\Model\Dao\Lottery\VisitorCodeDao;
use Events\Service\Lottery\CryptoRandomSource;
use Events\Service\Lottery\LotteryConfig;
use Events\Service\Lottery\LotteryDrawService;
use Events\Service\Lottery\LotteryScanService;
use Events\Service\Lottery\LotteryService;
use Pes\Container\Container;
use Test\AppRunner\AppRunner;
use Test\Integration\Event\Container\TestDbEventsContainerConfigurator;

/**
 * D1 (#46): zapojení s anketou, kód, sken u vchodu a na infostánku, losování kola, náhradník, převzetí – proti DB events.
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
        self::$now = new DateTimeImmutable('2027-01-27 09:00:00');
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
        self::$now = new DateTimeImmutable("2027-01-27 {$hm}:00");
    }

    public function testFlow(): void {
        $s = $this->service();
        $scan = new LotteryScanService($s);
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
        $offline = (new DateTimeImmutable('2027-01-27 10:25:00'))->format(DATE_ATOM);
        $this->assertSame('issued', $scan->scan($id, $s->codeFor($b), 'vchod', $offline, 'obsluha')['result']);
        $this->assertSame('2027-01-27 10:25:00', $s->presence($id, $b)['issued']);
        // c dostane los pozdě – do 11:00 nehraje
        $this->assertSame('issued', $scan->scan($id, $s->codeFor($cc), 'vchod', null, 'obsluha')['result']);

        // kolo 11:00: účastní se a, b (c ne – vydán v 10:50)
        $this->at('11:00');
        $draw = $this->draw($s);
        $round11 = $s->round($r11);
        $this->assertEqualsCanonicalizing([$a, $b], array_keys($draw->participants($round11)));
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
        $this->assertArrayNotHasKey($first, $draw->participants($round12));
        $w2 = $draw->drawOne($r12, $special, 'obsluha');
        $this->assertNotNull($w2);
        $sub = $draw->substitute((int) $w2['id'], 'obsluha');
        $this->assertTrue($sub['forfeited']);
        $this->assertNotNull($sub['winner']);
        $this->assertNotSame($w2['login_login_name_fk'], $sub['winner']['login_login_name_fk'], 'propadlý výherce o tutéž cenu nehraje');
        $this->assertSame((int) $w2['id'], (int) $sub['winner']['replaces_id_fk']);
        $unclaimed = $draw->winnersOfLottery($id, true);
        $this->assertCount(1, $unclaimed);

        // prodloužení na infostánku ve 12:30 → platí do 13:30
        $this->at('12:30');
        $res = $scan->scan($id, $s->codeFor($other), 'infostanek', null, 'obsluha');
        if ($other !== $sub['winner']['login_login_name_fk']) {
            $this->assertSame('refreshed', $res['result'], $res['message']);
            $this->assertSame('2027-01-27 13:30:00', $s->presence($id, $other)['valid_until']);
        }
        $status = $s->visitorStatus($id, $first);
        $this->assertCount(1, $status['wins']);
        $this->assertTrue($status['joined']);
    }
}
