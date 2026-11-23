<?php
declare(strict_types=1);

namespace Test\Integration\Event\Reservation;

use Container\EventsModelContainerConfigurator;
use DateTimeImmutable;
use DateTimeZone;
use Events\Model\Dao\Reservation\ReservationOfferDao;
use Events\Model\Dao\Reservation\SchoolReservationDao;
use Events\Model\Repository\EventRepo;
use Events\Service\Reservation\ManageToken;
use Events\Service\Reservation\ReservationConfig;
use Events\Service\Reservation\SchoolReservationService;
use Pes\Container\Container;
use Test\AppRunner\AppRunner;
use Test\Integration\Event\Container\TestDbEventsContainerConfigurator;
use Test\Integration\Event\Fixture\TestContentType;

/**
 * R1 (#48): rezervace pro školy proti DB events – nabídka, rezervace, plná přednáška a náhradní přednášky, zámek při souběhu,
 * změna, přesun, zrušení, překročení kapacity správcem, mazání telefonů po akci, anonymizace, přiřazení k účtu,
 * event.capacity přes EventRepo. Data testu mají prefix a po testu se smažou (vývojová data zůstanou).
 */
final class SchoolReservationFlowTest extends AppRunner {

    private const PREFIX = 'r1-test-';

    private static Container $c;
    private static DateTimeImmutable $now;
    private static array $events = [];
    private static int $contentId;
    private static string $login;

    public static function setUpBeforeClass(): void {
        self::bootstrapBeforeClass();
        self::$c = self::container();
        self::cleanup();
        $dao = self::$c->get(ReservationOfferDao::class);
        $dao->exec("INSERT INTO event_content (title, event_content_type_id_fk) VALUES (:t, :ty)", ['t' => self::PREFIX . 'přednáška', 'ty' => TestContentType::id()]);
        self::$contentId = $dao->lastId();
        foreach (['a' => ['10:00', 40], 'b' => ['11:00', 30], 'c' => ['12:00', null], 'd' => ['13:00', 5]] as $k => [$hm, $cap]) {
            $dao->exec("INSERT INTO event (published, start, end, event_content_id_fk, capacity) VALUES (1, :s, :e, :c, :cap)", [
                's' => "2031-01-29 {$hm}:00", 'e' => "2031-01-29 " . substr($hm, 0, 2) . ":45:00", 'c' => self::$contentId, 'cap' => $cap]);
            self::$events[$k] = $dao->lastId();
        }
        self::$login = self::PREFIX . uniqid();
        $dao->exec("INSERT INTO login (login_name) VALUES (:n)", ['n' => self::$login]);
        self::$now = new DateTimeImmutable('2031-01-10 09:00:00', new DateTimeZone('Europe/Prague'));
    }

    public static function tearDownAfterClass(): void {
        self::cleanup();
        TestContentType::remove();
    }

    private static function container(): Container {
        return (new EventsModelContainerConfigurator())->configure((new TestDbEventsContainerConfigurator())->configure(new Container()));
    }

    private static function cleanup(): void {
        $dao = self::$c->get(ReservationOfferDao::class);
        $dao->exec("DELETE r FROM school_reservation r JOIN reservation_offer o ON o.id = r.offer_id_fk WHERE o.code LIKE :p", ['p' => self::PREFIX . '%']);
        $dao->exec("DELETE FROM reservation_offer WHERE code LIKE :p", ['p' => self::PREFIX . '%']);   // kaskáda: reservation_offer_event
        $dao->exec("DELETE e FROM event e JOIN event_content c ON c.id = e.event_content_id_fk WHERE c.title LIKE :p", ['p' => self::PREFIX . '%']);
        $dao->exec("DELETE FROM event_content WHERE title LIKE :p", ['p' => self::PREFIX . '%']);
        $dao->exec("DELETE FROM login WHERE login_name LIKE :p", ['p' => self::PREFIX . '%']);
    }

    private function service(?Container $c = null): SchoolReservationService {
        $c = $c ?? self::$c;
        return new SchoolReservationService($c->get(ReservationOfferDao::class), $c->get(SchoolReservationDao::class),
            ReservationConfig::fromArray(['maxPerEmail' => 3]), fn() => self::$now);
    }

    private function at(string $dt): void {
        self::$now = new DateTimeImmutable($dt, new DateTimeZone('Europe/Prague'));
    }

    private function input(string $eventKey, int $pupils, int $teachers = 1, array $override = []): array {
        return $override + ['event_id' => (string) self::$events[$eventKey], 'school_name' => self::PREFIX . 'ZŠ', 'school_place' => 'Plzeň',
            'field_code' => 'C', 'school_class' => '9. A', 'pupils' => (string) $pupils, 'teachers' => (string) $teachers,
            'contact_email' => 'r1-test-jana@example.test', 'contact_name' => 'Jana', 'consent' => '1'];
    }

    public function testFlow(): void {
        $s = $this->service();
        $res = $s->saveOffer(null, ['code' => self::PREFIX . 'skoly', 'name' => 'Test R1', 'event_date' => '2031-01-29', 'status' => 'draft']);
        $this->assertSame([], $res['errors']);
        $oid = $res['id'];
        $offer = $s->offer($oid);
        $this->assertSame('2031-01-27 23:59:00', $offer['change_until'], 'výchozí uzávěrka 2 dny před akcí');
        $this->assertNotEmpty($s->saveOffer(null, ['code' => self::PREFIX . 'skoly', 'name' => 'x', 'event_date' => '2031-01-29'])['errors'], 'kód je unikátní');
        $this->assertCount(4, $s->lectureCandidates($offer));
        $s->setOfferEvents($oid, [self::$events['a'], self::$events['b'], self::$events['c'], self::$events['d']]);

        // nabídka ve stavu „připravuje se“ rezervaci nepřijme
        $this->assertFalse($s->book($s->offer($oid), $this->input('a', 20))['ok']);
        $s->saveOffer($oid, ['status' => 'open'] + $offer);
        $offer = $s->offer($oid);

        // rezervace s telefonem a bez
        $r1 = $s->book($offer, $this->input('a', 25, 2, ['contact_phone' => '777 123 456', 'phone_consent' => '1']), null);
        $this->assertTrue($r1['ok'], implode(' ', $r1['errors']));
        $this->assertTrue(ManageToken::isWellFormed($r1['token']));
        $row = self::$c->get(SchoolReservationDao::class)->row("SELECT * FROM school_reservation WHERE id = :id", ['id' => $r1['id']]);
        $this->assertSame(ManageToken::hash($r1['token']), $row['manage_token_hash']);
        $this->assertStringNotContainsString($r1['token'], implode('|', array_map('strval', $row)), 'token v DB čitelně není');
        $this->assertSame('777123456', $row['contact_phone']);
        $this->assertNotNull($row['phone_consent_at']);

        // plná přednáška: 27 + 14 > 40 → náhradní přednášky s dost místy (b, ne c bez kapacity, ne d s 5 místy)
        $full = $s->book($offer, $this->input('a', 13, 1));
        $this->assertFalse($full['ok']);
        $this->assertTrue($full['full']);
        $this->assertStringContainsString('zbývá jen 13 míst, vaše skupina má 14 osob', $full['errors'][0]);
        $this->assertSame([self::$events['b']], array_map(fn($l) => (int) $l['id'], $full['alternatives']));
        $this->assertTrue($s->book($offer, $this->input('a', 12, 1))['ok'], 'přesně do kapacity');
        $this->assertSame(0, $this->lecture($s, $oid, 'a')['free']);
        $this->assertFalse($s->book($offer, $this->input('c', 5))['ok'], 'přednáška bez kapacity nejde rezervovat');

        // limit rezervací z jednoho e-mailu (konfigurace 3)
        $this->assertTrue($s->book($offer, $this->input('b', 5))['ok']);
        $limit = $s->book($offer, $this->input('b', 5));
        $this->assertFalse($limit['ok']);
        $this->assertStringContainsString('nejvýš 3', $limit['errors'][0]);

        // změna pedagogem: zvětšení nad kapacitu ne, přesun na b ano
        $this->assertFalse($s->updateByOwner($r1['token'], $this->input('a', 26, 2))['ok']);
        $moved = $s->updateByOwner($r1['token'], $this->input('b', 20, 2, ['contact_phone' => '777123456', 'phone_consent' => '1']));
        $this->assertTrue($moved['ok'], implode(' ', $moved['errors']));
        $this->assertTrue($moved['moved']);
        $this->assertSame(13, $this->lecture($s, $oid, 'a')['occupied']);
        $this->assertSame(28, $this->lecture($s, $oid, 'b')['occupied']);
        $this->assertSame('777123456', $s->reservationByToken($r1['token'])['contact_phone']);

        // správce smí kapacitu překročit s upozorněním (b: 28 → 28 - 22 + 40 = 46 > 30)
        $admin = $s->updateByAdmin($r1['id'], $this->input('b', 38, 2), 'r1-admin');
        $this->assertTrue($admin['ok']);
        $this->assertStringContainsString('překročena', $admin['warnings'][0]);
        $this->assertSame('777123456', $s->reservation($r1['id'])['contact_phone'], 'správce telefon nemění');
        $this->assertSame(0, $this->lecture($s, $oid, 'b')['free']);

        // přiřazení k účtu podle e-mailu a předvyplnění bez telefonu
        $this->assertSame(3, $s->linkToLogin(self::$login, 'R1-TEST-jana@example.test'));
        $this->assertCount(3, $s->reservationsOfLogin(self::$login));
        $prefill = $s->prefill(self::$login);
        $this->assertSame(self::PREFIX . 'ZŠ', $prefill['school_name']);
        $this->assertArrayNotHasKey('contact_phone', $prefill);

        // po uzávěrce pedagog nemění ani neruší, správce ano
        $this->at('2031-01-28 08:00');
        $this->assertFalse($s->updateByOwner($r1['token'], $this->input('b', 10))['ok']);
        $this->assertFalse($s->cancelByOwner($r1['token'])['ok']);
        $this->at('2031-01-20 08:00');
        $this->assertTrue($s->cancelByOwner($r1['token'])['ok']);
        $cancelled = $s->reservation($r1['id']);
        $this->assertSame('cancelled', $cancelled['status']);
        $this->assertNull($cancelled['contact_phone'], 'zrušení smaže telefon hned');
        $this->assertNotNull($cancelled['phone_deleted_at']);
        $this->assertNull($s->reservationByToken('neplatny'));

        // telefon u jiné rezervace se smaže až po konci dne přednášky
        $r5 = $s->book($offer, $this->input('d', 3, 1, ['contact_email' => 'r1-test-petr@example.test', 'contact_phone' => '602111222', 'phone_consent' => '1']));
        $this->assertTrue($r5['ok'], implode(' ', $r5['errors']));
        $this->at('2031-01-29 23:59');
        $s->housekeeping();
        $this->assertSame('602111222', $s->reservation($r5['id'])['contact_phone']);
        $this->assertNull($s->anonymize($oid), 'anonymizace až po dni akce');
        $this->at('2031-01-30 00:00');
        $s->housekeeping();
        $this->assertNull($s->reservation($r5['id'])['contact_phone']);
        $this->assertNotNull($s->reservation($r5['id'])['phone_deleted_at']);

        // CSV bez telefonů, anonymizace ponechá školu a počty
        $csv = json_encode($s->csvRows($oid), JSON_UNESCAPED_UNICODE);
        $this->assertStringNotContainsString('602111222', $csv);
        $this->assertStringContainsString('r1-test-petr@example.test', $csv);
        $this->assertGreaterThan(0, $s->anonymize($oid));
        $anon = $s->reservation($r5['id']);
        $this->assertSame('', $anon['contact_email']);
        $this->assertNull($anon['contact_name']);
        $this->assertSame(self::PREFIX . 'ZŠ', $anon['school_name']);
        $this->assertSame(3, (int) $anon['pupils']);
        $this->assertSame([], $s->reservationsOfLogin(self::$login));

        // přednášku s rezervacemi nelze z nabídky vyřadit
        $this->assertContains(self::$events['a'], $s->setOfferEvents($oid, []));
    }

    public function testConcurrentBookingWaitsForLock(): void {
        $s = $this->service();
        $res = $s->saveOffer(null, ['code' => self::PREFIX . 'soubeh', 'name' => 'Test souběh', 'event_date' => '2031-01-29', 'status' => 'open']);
        $oid = $res['id'];
        $s->setOfferEvents($oid, [self::$events['d']]);
        $this->at('2031-01-10 09:00');
        // druhé připojení drží zámek řádku přednášky (jiná transakce rezervuje) → tato rezervace musí čekat, nesmí projít
        $other = self::container();
        $otherDao = $other->get(ReservationOfferDao::class);
        $otherDao->exec("SET SESSION innodb_lock_wait_timeout = 1");
        self::$c->get(ReservationOfferDao::class)->exec("SET SESSION innodb_lock_wait_timeout = 1");
        $before = $this->lecture($s, $oid, 'd')['occupied'];
        $handler = (new \ReflectionProperty($otherDao, 'dbHandler'))->getValue($otherDao);
        $handler->beginTransaction();
        $otherDao->row("SELECT id FROM event WHERE id = :e FOR UPDATE", ['e' => self::$events['d']]);
        try {
            $s->book($s->offer($oid), $this->input('d', 1, 0, ['contact_email' => 'r1-test-soubeh@example.test']));
            $this->fail('rezervace prošla, i když jiná transakce drží zámek přednášky');
        } catch (\Throwable $e) {
            $this->assertStringContainsString('Lock wait timeout', ($e->getPrevious() ?? $e)->getMessage());
        } finally {
            $handler->rollBack();
        }
        $this->assertSame($before, $this->lecture($s, $oid, 'd')['occupied'], 'nic se neuložilo');
        $this->assertTrue($s->book($s->offer($oid), $this->input('d', 1, 0, ['contact_email' => 'r1-test-soubeh@example.test']))['ok'], 'po uvolnění zámku projde');
        $this->assertSame($before + 1, $this->lecture($s, $oid, 'd')['occupied']);
    }

    public function testEventCapacityThroughRepo(): void {
        /** @var EventRepo $repo */
        $repo = self::$c->get(EventRepo::class);
        $event = $repo->get(self::$events['a']);
        $this->assertSame(40, $event->getCapacity());
        $this->assertNull($repo->get(self::$events['c'])->getCapacity());
    }

    private function lecture(SchoolReservationService $s, int $oid, string $k): array {
        foreach ($s->lectures($oid) as $l) {
            if ((int) $l['id'] === self::$events[$k]) {
                return $l;
            }
        }
        $this->fail("Přednáška {$k} není v nabídce.");
    }
}
