<?php
declare(strict_types=1);

namespace Test\Unit\Events\Reservation;

use DateTimeImmutable;
use DateTimeZone;
use Events\Service\Reservation\ManageToken;
use Events\Service\Reservation\ReservationConfig;
use Events\Service\Reservation\ReservationMailer;
use Events\Service\Reservation\ReservationValidator;
use Events\Service\Reservation\SchoolReservationService as S;
use Mail\MessageFactory\HtmlMessage;
use PHPUnit\Framework\TestCase;

/**
 * R1 (#48): pravidla rezervací pro školy bez DB – validace formuláře, volná místa a náhradní přednášky, stav nabídky,
 * uzávěrka, token, mazání telefonu, konfigurace, šablony e-mailů (bez telefonu).
 */
final class ReservationRulesTest extends TestCase {

    private function at(string $dt): DateTimeImmutable {
        return new DateTimeImmutable($dt, new DateTimeZone('Europe/Prague'));
    }

    private function valid(array $override = []): array {
        return $override + [
            'event_id' => '7', 'school_name' => '  ZŠ   Komenského ', 'school_place' => 'Plzeň', 'field_code' => 'c',
            'school_class' => '', 'pupils' => '24', 'teachers' => '', 'contact_email' => 'Jana.Ucitelka@Example.TEST',
            'contact_name' => 'Jana Učitelka', 'contact_phone' => '', 'note' => '', 'consent' => '1',
        ];
    }

    public function testValidFormDefaultsAndNormalization(): void {
        [$d, $errors] = (new ReservationValidator(new ReservationConfig()))->validate($this->valid());
        $this->assertSame([], $errors);
        $this->assertSame(7, $d['event_id_fk']);
        $this->assertSame('ZŠ Komenského', $d['school_name']);
        $this->assertSame('C', $d['field_code']);
        $this->assertSame(1, $d['teachers'], 'nezadaný počet pedagogů = 1');
        $this->assertSame('jana.ucitelka@example.test', $d['contact_email']);
        $this->assertNull($d['school_class']);
        $this->assertNull($d['contact_phone']);
        $this->assertFalse($d['phone_consent']);
    }

    public function testRequiredFieldsAndLimits(): void {
        $v = new ReservationValidator(new ReservationConfig(maxPupils: 50));
        [, $errors] = $v->validate(['consent' => '']);
        $this->assertGreaterThanOrEqual(7, count($errors));
        [, $errors] = $v->validate($this->valid(['pupils' => '0']));
        $this->assertCount(1, $errors);
        [, $errors] = $v->validate($this->valid(['pupils' => '51']));
        $this->assertStringContainsString('nejvýš 50', $errors[0]);
        [, $errors] = $v->validate($this->valid(['field_code' => 'X']));
        $this->assertSame(['Vyberte cílový obor.'], $errors);
        [, $errors] = $v->validate($this->valid(['consent' => '']));
        $this->assertCount(1, $errors);
        [, $errors] = $v->validate($this->valid(['consent' => '']), false);
        $this->assertSame([], $errors, 'změna rezervace souhlas znovu nevyžaduje');
        [, $errors] = $v->validate($this->valid(['teachers' => '0']));
        $this->assertSame([], $errors);
    }

    public function testPhoneNeedsConsent(): void {
        $v = new ReservationValidator(new ReservationConfig());
        [, $errors] = $v->validate($this->valid(['contact_phone' => '777 123 456']));
        $this->assertCount(1, $errors, 'telefon bez souhlasu se neuloží');
        [$d, $errors] = $v->validate($this->valid(['contact_phone' => '+420 777-123-456', 'phone_consent' => '1']));
        $this->assertSame([], $errors);
        $this->assertSame('+420777123456', $d['contact_phone']);
        $this->assertTrue($d['phone_consent']);
        [, $errors] = $v->validate($this->valid(['contact_phone' => 'zavolejte', 'phone_consent' => '1']));
        $this->assertCount(1, $errors);
        $this->assertSame('+42077712345', ReservationValidator::normalizePhone('0042077712345'));
        $this->assertSame('tel:+420777123456', ReservationValidator::telHref('777123456'));
    }

    public function testHoneypotRejects(): void {
        [, $errors] = (new ReservationValidator(new ReservationConfig()))->validate($this->valid([ReservationValidator::HONEYPOT => 'spam']));
        $this->assertCount(1, $errors);
    }

    public function testFreeSeatsAndAlternatives(): void {
        $this->assertSame(10, S::freeSeats(40, 30));
        $this->assertSame(0, S::freeSeats(40, 45), 'po překročení správcem nikdy záporné');
        $this->assertNull(S::freeSeats(null, 5));
        $this->assertSame(['1 místo', '2 místa', '4 místa', '5 míst', '0 míst'], array_map([S::class, 'places'], [1, 2, 4, 5, 0]));
        $this->assertSame('3 osoby', S::persons(3));
        $lectures = [
            ['id' => 1, 'bookable' => true, 'free' => 5],
            ['id' => 2, 'bookable' => true, 'free' => 30],
            ['id' => 3, 'bookable' => false, 'free' => 30],
            ['id' => 4, 'bookable' => true, 'free' => null],
            ['id' => 5, 'bookable' => true, 'free' => 25],
        ];
        $this->assertSame([2, 5], array_column(S::alternatives($lectures, 25), 'id'));
        $this->assertSame([5], array_column(S::alternatives($lectures, 25, 2), 'id'));
    }

    public function testBookableLecture(): void {
        $now = $this->at('2027-01-27 09:00');
        $lecture = ['published' => 1, 'capacity' => 40, 'start' => '2027-01-27 10:00:00'];
        $this->assertTrue(S::isBookableLecture($lecture, $now));
        $this->assertFalse(S::isBookableLecture(['published' => 0] + $lecture, $now));
        $this->assertFalse(S::isBookableLecture(['capacity' => null] + $lecture, $now));
        $this->assertFalse(S::isBookableLecture($lecture, $this->at('2027-01-27 10:00')), 'od začátku přednášky už ne');
    }

    public function testBookingState(): void {
        $offer = ['status' => S::OFFER_OPEN, 'event_date' => '2027-01-27', 'booking_from' => '2026-12-01 00:00:00',
            'booking_until' => '2027-01-20 23:59:00'];
        $this->assertTrue(S::bookingState($offer, $this->at('2026-12-15 10:00'))['open']);
        $this->assertStringContainsString('začnou', S::bookingState($offer, $this->at('2026-11-30 10:00'))['message']);
        $this->assertStringContainsString('skončily', S::bookingState($offer, $this->at('2027-01-21 00:00'))['message']);
        $this->assertFalse(S::bookingState(['status' => S::OFFER_DRAFT] + $offer, $this->at('2026-12-15'))['open']);
        $this->assertFalse(S::bookingState(['status' => S::OFFER_CLOSED] + $offer, $this->at('2026-12-15'))['open']);
        $this->assertFalse(S::bookingState(null, $this->at('2026-12-15'))['open']);
        $open = ['booking_from' => null, 'booking_until' => null] + $offer;
        $this->assertTrue(S::bookingState($open, $this->at('2027-01-27 13:00'))['open']);
        $this->assertSame('Akce už proběhla.', S::bookingState($open, $this->at('2027-01-28 00:00'))['message']);
    }

    public function testChangeDeadline(): void {
        $offer = ['event_date' => '2027-01-27', 'change_until' => null];
        $this->assertSame('2027-01-25 23:59:00', S::changeDeadline($offer));
        $r = ['status' => S::ACTIVE, 'anonymized_at' => null];
        $this->assertTrue(S::canOwnerChange($r, $offer, $this->at('2027-01-25 23:59')));
        $this->assertFalse(S::canOwnerChange($r, $offer, $this->at('2027-01-26 00:00')));
        $this->assertFalse(S::canOwnerChange(['status' => S::CANCELLED] + $r, $offer, $this->at('2027-01-20')));
        $this->assertSame('2027-01-24 12:00:00', S::changeDeadline(['change_until' => '2027-01-24 12:00:00'] + $offer));
    }

    public function testPhonePurgeAfterEventDay(): void {
        $this->assertFalse(S::phonePurgeDue('2027-01-27 10:00:00', $this->at('2027-01-27 23:59')));
        $this->assertTrue(S::phonePurgeDue('2027-01-27 10:00:00', $this->at('2027-01-28 00:00')));
    }

    public function testManageToken(): void {
        $t = ManageToken::generate();
        $this->assertTrue(ManageToken::isWellFormed($t));
        $this->assertSame(43, strlen($t));
        $this->assertNotSame($t, ManageToken::generate());
        $this->assertSame(64, strlen(ManageToken::hash($t)));
        $this->assertStringNotContainsString($t, ManageToken::hash($t));
        $this->assertFalse(ManageToken::isWellFormed('abc'));
        $this->assertFalse(ManageToken::isWellFormed(str_repeat('a', 42) . '/'));
    }

    public function testConfigDefaultsAndOverrides(): void {
        $d = ReservationConfig::fromArray([]);
        $this->assertSame(5, $d->maxPerEmail);
        $this->assertSame(2, $d->changeDaysBefore);
        $this->assertSame('reservation.notify', $d->notifyGroup);
        $c = ReservationConfig::fromArray(['maxPerEmail' => 3, 'mail' => ['sender' => 'noreply'], 'policyUrl' => 'https://example.test/z']);
        $this->assertSame(3, $c->maxPerEmail);
        $this->assertSame('noreply', $c->mailSender);
        $this->assertSame('https://example.test/z', $c->policyUrl);
        $this->assertTrue($d->hasPlaceholderTexts(), 'výchozí texty jsou náznak „Nutno nahradit“ (#140)');
        $this->assertFalse(ReservationConfig::fromArray(['consentText' => 'Souhlasím.', 'phoneConsentText' => 'Souhlasím s telefonem.',
            'policyUrl' => 'https://example.test/zasady'])->hasPlaceholderTexts());
    }

    public function testMailTemplatesWithoutPhone(): void {
        $r = ['offer_name' => 'Přednášky pro školy 2027', 'offer_change_until' => '2027-01-25 23:59:00', 'event_title' => 'Jak <b>na</b> CV',
            'event_start' => '2027-01-27 10:00:00', 'event_end' => '2027-01-27 10:45:00', 'school_name' => 'ZŠ Komenského',
            'school_place' => 'Plzeň', 'field_code' => 'C', 'school_class' => null, 'pupils' => 24, 'teachers' => 2,
            'contact_name' => null, 'contact_email' => 'jana@example.test', 'contact_phone' => '777123456', 'note' => null];
        $dir = __DIR__ . '/../../../../src/Module/Events/Middleware/Events/Controler/Reservation/Messages/';
        $html = (new HtmlMessage())->create($dir . 'reservation-pedagog.php', ReservationMailer::context(ReservationMailer::CREATED, $r) + [
            'manageUrl' => 'https://example.test/events/v1/reservation/manage/TOKEN', 'registerUrl' => 'https://example.test/',
            'organizerContact' => 'info@example.test', 'data_logo_grafia' => 'data:image/gif;base64,AA']);
        $this->assertStringContainsString('Jak &lt;b&gt;na&lt;/b&gt; CV', $html);
        $this->assertStringContainsString('manage/TOKEN', $html);
        $this->assertStringContainsString('25. 1. 2027 23:59', $html);
        $this->assertStringContainsString('Zaregistrujte se', $html);
        $this->assertStringNotContainsString('777123456', $html);
        $org = (new HtmlMessage())->create($dir . 'reservation-organizer.php', ReservationMailer::context(ReservationMailer::CREATED, $r) + [
            'heading' => 'Nová rezervace školy', 'data_logo_grafia' => 'data:image/gif;base64,AA']);
        $this->assertStringNotContainsString('777123456', $org);
        $this->assertStringNotContainsString('manage/', $org);
        $this->assertStringContainsString('ZŠ Komenského', $org);
        $cancel = (new HtmlMessage())->create($dir . 'reservation-pedagog.php', ReservationMailer::context(ReservationMailer::CANCELLED_BY_ORGANIZER, $r) + [
            'manageUrl' => false, 'registerUrl' => false, 'organizerContact' => false, 'data_logo_grafia' => 'data:image/gif;base64,AA']);
        $this->assertStringContainsString('organizátor veletrhu zrušil', $cancel);
    }
}
