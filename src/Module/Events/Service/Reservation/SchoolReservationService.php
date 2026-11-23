<?php

namespace Events\Service\Reservation;

use DateTimeImmutable;
use Events\Model\Dao\Reservation\ReservationOfferDao;
use Events\Model\Dao\Reservation\SchoolReservationDao;

/**
 * R1 (#48): rezervace míst na přednáškách pro školy. Zadání docs/vp/zadani/R1.md.
 *
 * Nabídka (reservation_offer) = rezervace pro školy na jeden ročník; přednášky do ní správce zařadí ručně
 * (reservation_offer_event). Slot = event z rozpisu programu (G0) s kapacitou event.capacity.
 * Kapacita počítá všechny osoby (žáci + pedagogové) aktivních rezervací. Veřejný formulář a správa odkazem kapacitu
 * nepřekročí (kontrola v transakci se zámkem FOR UPDATE na řádku eventu), správce smí vědomě překročit.
 *
 * Token pro správu: 32 náhodných bajtů base64url jen v odkazu v e-mailu, v DB SHA-256 hash.
 * Telefon: jen se souhlasem, vidí ho jen správce, smaže se po konci dne přednášky (housekeeping() při každém požadavku
 * na rezervace, dokud není plánovač Z2), tlačítkem a při zrušení rezervace.
 *
 * Deklarovaná odchylka ADR 001: Dao + služba s poli (kvůli transakci se zámkem, stejně jako D1).
 */
class SchoolReservationService {

    public const OFFER_DRAFT = 'draft';
    public const OFFER_OPEN = 'open';
    public const OFFER_CLOSED = 'closed';
    public const OFFER_STATUSES = [self::OFFER_DRAFT => 'připravuje se', self::OFFER_OPEN => 'otevřená', self::OFFER_CLOSED => 'uzavřená'];

    public const ACTIVE = 'active';
    public const CANCELLED = 'cancelled';
    public const CANCELLED_SELF = 'self';

    private const LECTURE_SQL = "SELECT e.id, e.start, e.end, e.published, e.capacity, c.title, c.perex, c.party,
            t.type AS type_code, t.name AS type_name
        FROM event e
        LEFT JOIN event_content c ON c.id = e.event_content_id_fk
        LEFT JOIN event_content_type t ON t.id = c.event_content_type_id_fk";

    private const RESERVATION_SQL = "SELECT r.*, e.start AS event_start, e.end AS event_end, e.capacity AS event_capacity,
            c.title AS event_title, o.code AS offer_code, o.name AS offer_name, o.event_date AS offer_event_date,
            o.status AS offer_status, o.change_until AS offer_change_until
        FROM school_reservation r
        JOIN reservation_offer o ON o.id = r.offer_id_fk
        JOIN event e ON e.id = r.event_id_fk
        LEFT JOIN event_content c ON c.id = e.event_content_id_fk";

    private \DateTimeZone $tz;
    /** @var callable(): DateTimeImmutable */
    private $clock;
    private ReservationValidator $validator;

    public function __construct(
            private ReservationOfferDao $offerDao,
            private SchoolReservationDao $reservationDao,
            private ReservationConfig $config,
            ?callable $clock = null) {
        $this->tz = new \DateTimeZone($config->timezone);
        $this->clock = $clock ?? fn() => new DateTimeImmutable('now', $this->tz);
        $this->validator = new ReservationValidator($config);
    }

    public function config(): ReservationConfig {
        return $this->config;
    }

    public function validator(): ReservationValidator {
        return $this->validator;
    }

    public function now(): DateTimeImmutable {
        return ($this->clock)();
    }

    private function nowSql(): string {
        return $this->now()->format('Y-m-d H:i:s');
    }

    //----------------------------------------------------------------------------------------------------- nabídky

    public function offers(): array {
        return $this->offerDao->rows("SELECT * FROM reservation_offer ORDER BY event_date DESC, id DESC");
    }

    public function offer(int $id): ?array {
        return $this->offerDao->row("SELECT * FROM reservation_offer WHERE id = :id", ['id' => $id]);
    }

    public function offerByCode(string $code): ?array {
        return $this->offerDao->row("SELECT * FROM reservation_offer WHERE code = :c", ['c' => $code]);
    }

    /** Nabídka pro veřejný formulář: daný kód, jinak nejnovější otevřená. */
    public function publicOffer(?string $code = null): ?array {
        if ($code !== null && $code !== '') {
            return $this->offerByCode($code);
        }
        return $this->offerDao->row("SELECT * FROM reservation_offer WHERE status = :s ORDER BY event_date DESC, id DESC LIMIT 1",
            ['s' => self::OFFER_OPEN]);
    }

    /**
     * Uloží nabídku. Data: code, name, event_date (Y-m-d), status, booking_from, booking_until, change_until (Y-m-d H:i:s nebo '').
     * Chybějící uzávěrka změn = changeDaysBefore dní před akcí 23:59.
     *
     * @return array{id: ?int, errors: list<string>}
     */
    public function saveOffer(?int $id, array $data): array {
        $errors = [];
        $code = strtolower(trim((string) ($data['code'] ?? '')));
        $name = trim((string) ($data['name'] ?? ''));
        $date = trim((string) ($data['event_date'] ?? ''));
        $status = (string) ($data['status'] ?? self::OFFER_DRAFT);
        if (!preg_match('/^[a-z0-9][a-z0-9\-]{1,48}$/', $code)) {
            $errors[] = 'Kód nabídky: malá písmena bez diakritiky, číslice a pomlčky, např. skoly-2027.';
        } else {
            $other = $this->offerByCode($code);
            if ($other !== null && (int) $other['id'] !== (int) $id) {
                $errors[] = "Nabídka s kódem {$code} už existuje.";
            }
        }
        if ($name === '') {
            $errors[] = 'Vyplňte název nabídky.';
        }
        $eventDate = DateTimeImmutable::createFromFormat('!Y-m-d', $date, $this->tz);
        if ($eventDate === false || $eventDate->format('Y-m-d') !== $date) {
            $errors[] = 'Vyplňte den akce.';
        }
        if (!isset(self::OFFER_STATUSES[$status])) {
            $status = self::OFFER_DRAFT;
        }
        if ($errors) {
            return ['id' => $id, 'errors' => $errors];
        }
        $changeUntil = self::dtOrNull($data['change_until'] ?? null)
            ?? $eventDate->modify("-{$this->config->changeDaysBefore} days")->format('Y-m-d 23:59:00');
        $row = ['code' => $code, 'name' => mb_substr($name, 0, 200), 'event_date' => $date, 'status' => $status,
            'booking_from' => self::dtOrNull($data['booking_from'] ?? null), 'booking_until' => self::dtOrNull($data['booking_until'] ?? null),
            'change_until' => $changeUntil];
        if ($id === null) {
            $this->offerDao->exec("INSERT INTO reservation_offer (code, name, event_date, status, booking_from, booking_until, change_until)
                VALUES (:code, :name, :event_date, :status, :booking_from, :booking_until, :change_until)", $row);
            return ['id' => $this->offerDao->lastId(), 'errors' => []];
        }
        $this->offerDao->exec("UPDATE reservation_offer SET code = :code, name = :name, event_date = :event_date, status = :status,
            booking_from = :booking_from, booking_until = :booking_until, change_until = :change_until, updated = :now WHERE id = :id",
            $row + ['id' => $id, 'now' => $this->nowSql()]);
        return ['id' => $id, 'errors' => []];
    }

    /**
     * Přednášky v nabídce (ruční výběr). Přednášku s aktivními rezervacemi nelze z nabídky vyřadit – zůstane.
     *
     * @param int[] $eventIds
     * @return list<int> id přednášek, které zůstaly kvůli rezervacím
     */
    public function setOfferEvents(int $offerId, array $eventIds): array {
        $eventIds = array_values(array_unique(array_map('intval', $eventIds)));
        return $this->offerDao->transaction(function () use ($offerId, $eventIds) {
            $kept = [];
            foreach ($this->offerDao->rows("SELECT event_id_fk FROM reservation_offer_event WHERE offer_id_fk = :o", ['o' => $offerId]) as $r) {
                $eid = (int) $r['event_id_fk'];
                if (in_array($eid, $eventIds, true)) {
                    continue;
                }
                if ($this->occupied($eid) > 0) {
                    $kept[] = $eid;
                    continue;
                }
                $this->offerDao->exec("DELETE FROM reservation_offer_event WHERE offer_id_fk = :o AND event_id_fk = :e", ['o' => $offerId, 'e' => $eid]);
            }
            foreach ($eventIds as $eid) {
                $this->offerDao->exec("INSERT IGNORE INTO reservation_offer_event (offer_id_fk, event_id_fk) VALUES (:o, :e)", ['o' => $offerId, 'e' => $eid]);
            }
            return $kept;
        });
    }

    /**
     * Eventy dne akce pro výběr do nabídky (všechny typy obsahu, přednášky první), s příznakem in_offer.
     */
    public function lectureCandidates(array $offer): array {
        $rows = $this->offerDao->rows(self::LECTURE_SQL . " WHERE e.start >= :from AND e.start < :to ORDER BY e.start, e.id", [
            'from' => $offer['event_date'] . ' 00:00:00',
            'to' => (new DateTimeImmutable($offer['event_date']))->modify('+1 day')->format('Y-m-d 00:00:00'),
        ]);
        $in = array_flip($this->offerEventIds((int) $offer['id']));
        $occupied = $this->occupiedByEvent();
        foreach ($rows as &$r) {
            $r['in_offer'] = isset($in[(int) $r['id']]);
            $r['occupied'] = $occupied[(int) $r['id']] ?? 0;
        }
        return $rows;
    }

    /** @return list<int> */
    public function offerEventIds(int $offerId): array {
        return array_map(fn($r) => (int) $r['event_id_fk'],
            $this->offerDao->rows("SELECT event_id_fk FROM reservation_offer_event WHERE offer_id_fk = :o", ['o' => $offerId]));
    }

    /**
     * Přednášky nabídky s obsazeností: occupied (osoby aktivních rezervací), free (null = bez kapacity), bookable.
     */
    public function lectures(int $offerId): array {
        $rows = $this->offerDao->rows(self::LECTURE_SQL . " JOIN reservation_offer_event oe ON oe.event_id_fk = e.id AND oe.offer_id_fk = :o
            ORDER BY e.start, e.id", ['o' => $offerId]);
        $occupied = $this->occupiedByEvent();
        $now = $this->now();
        foreach ($rows as &$r) {
            $r['occupied'] = $occupied[(int) $r['id']] ?? 0;
            $r['free'] = self::freeSeats($r['capacity'] === null ? null : (int) $r['capacity'], $r['occupied']);
            $r['bookable'] = self::isBookableLecture($r, $now);
        }
        return $rows;
    }

    /** Přednáška je k rezervaci: zveřejněná, se zadanou kapacitou, ještě nezačala. */
    public static function isBookableLecture(array $lecture, \DateTimeInterface $now): bool {
        return (int) $lecture['published'] === 1 && $lecture['capacity'] !== null && (int) $lecture['capacity'] > 0
            && new DateTimeImmutable($lecture['start'], $now->getTimezone()) > $now;
    }

    /** Volná místa; kapacita null = bez limitu (vrací null). Nikdy záporné. */
    public static function freeSeats(?int $capacity, int $occupied): ?int {
        return $capacity === null ? null : max(0, $capacity - $occupied);
    }

    /**
     * Náhradní přednášky pro skupinu $persons osob: rezervovatelné s dost volnými místy, kromě $exceptEventId.
     */
    public static function alternatives(array $lectures, int $persons, ?int $exceptEventId = null): array {
        return array_values(array_filter($lectures, fn($l) => $l['bookable'] && (int) $l['id'] !== (int) $exceptEventId
            && $l['free'] !== null && $l['free'] >= $persons));
    }

    /**
     * Stav nabídky pro veřejný formulář: ['open' => bool, 'message' => text proč ne].
     */
    public static function bookingState(?array $offer, \DateTimeInterface $now): array {
        if ($offer === null || $offer['status'] === self::OFFER_DRAFT) {
            return ['open' => false, 'message' => 'Rezervace pro školy zatím nejsou otevřené.'];
        }
        if ($offer['status'] === self::OFFER_CLOSED) {
            return ['open' => false, 'message' => 'Rezervace pro školy jsou uzavřené.'];
        }
        $tz = $now->getTimezone();
        if (!empty($offer['booking_from']) && $now < new DateTimeImmutable($offer['booking_from'], $tz)) {
            return ['open' => false, 'message' => 'Rezervace začnou ' . self::fmt($offer['booking_from']) . '.'];
        }
        if (!empty($offer['booking_until']) && $now > new DateTimeImmutable($offer['booking_until'], $tz)) {
            return ['open' => false, 'message' => 'Rezervace skončily ' . self::fmt($offer['booking_until']) . '.'];
        }
        if ($now >= (new DateTimeImmutable($offer['event_date'], $tz))->modify('+1 day')) {
            return ['open' => false, 'message' => 'Akce už proběhla.'];
        }
        return ['open' => true, 'message' => ''];
    }

    /** Uzávěrka změn pedagogem (z nabídky, jinak N dní před akcí 23:59). */
    public static function changeDeadline(array $offer, int $daysBefore = 2): string {
        if (!empty($offer['change_until'])) {
            return $offer['change_until'];
        }
        return (new DateTimeImmutable($offer['event_date']))->modify("-{$daysBefore} days")->format('Y-m-d 23:59:00');
    }

    /** Smí pedagog odkazem měnit / rušit? Do uzávěrky včetně, a jen aktivní a neanonymizovaná rezervace. */
    public static function canOwnerChange(array $reservation, array $offer, \DateTimeInterface $now, int $daysBefore = 2): bool {
        return $reservation['status'] === self::ACTIVE && empty($reservation['anonymized_at'])
            && $now <= new DateTimeImmutable(self::changeDeadline($offer, $daysBefore), $now->getTimezone());
    }

    /** Má se telefon už smazat? Po konci dne přednášky (od půlnoci následujícího dne). */
    public static function phonePurgeDue(string $eventStart, \DateTimeInterface $now): bool {
        $dayAfter = (new DateTimeImmutable(substr($eventStart, 0, 10), $now->getTimezone()))->modify('+1 day');
        return $now >= $dayAfter;
    }

    //------------------------------------------------------------------------------------------------- obsazenost

    /** Osoby (žáci + pedagogové) aktivních rezervací přednášky, volitelně bez rezervace $exceptId. */
    public function occupied(int $eventId, ?int $exceptId = null): int {
        $row = $this->reservationDao->row("SELECT COALESCE(SUM(pupils + teachers), 0) AS n FROM school_reservation
            WHERE event_id_fk = :e AND status = :s AND id <> :x", ['e' => $eventId, 's' => self::ACTIVE, 'x' => (int) $exceptId]);
        return (int) ($row['n'] ?? 0);
    }

    /** @return array<int, int> event id => osoby aktivních rezervací */
    public function occupiedByEvent(): array {
        $ret = [];
        foreach ($this->reservationDao->rows("SELECT event_id_fk, SUM(pupils + teachers) AS n FROM school_reservation
                WHERE status = :s GROUP BY event_id_fk", ['s' => self::ACTIVE]) as $r) {
            $ret[(int) $r['event_id_fk']] = (int) $r['n'];
        }
        return $ret;
    }

    /** Počet rezervací (i zrušených) přednášky – event s rezervacemi nejde smazat (FK RESTRICT). */
    public function reservationCount(int $eventId): int {
        $row = $this->reservationDao->row("SELECT COUNT(*) AS n FROM school_reservation WHERE event_id_fk = :e", ['e' => $eventId]);
        return (int) ($row['n'] ?? 0);
    }

    //------------------------------------------------------------------------------------------------- rezervace

    /**
     * Nová rezervace z veřejného formuláře.
     *
     * @return array{ok: bool, errors: list<string>, id?: int, token?: string, alternatives?: array, full?: bool}
     */
    public function book(array $offer, array $input, ?string $loginName = null): array {
        $state = self::bookingState($offer, $this->now());
        if (!$state['open']) {
            return ['ok' => false, 'errors' => [$state['message']]];
        }
        [$data, $errors] = $this->validator->validate($input, true);
        if ($errors) {
            return ['ok' => false, 'errors' => $errors];
        }
        $offerId = (int) $offer['id'];
        $persons = $data['pupils'] + $data['teachers'];
        return $this->reservationDao->transaction(function () use ($offerId, $data, $persons, $loginName) {
            $check = $this->checkSeats($offerId, (int) $data['event_id_fk'], $persons, null);
            if ($check !== null) {
                return $check;
            }
            $count = $this->reservationDao->row("SELECT COUNT(*) AS n FROM school_reservation WHERE offer_id_fk = :o AND contact_email = :m
                AND status = :s", ['o' => $offerId, 'm' => $data['contact_email'], 's' => self::ACTIVE]);
            if ((int) $count['n'] >= $this->config->maxPerEmail) {
                return ['ok' => false, 'errors' => ["Z jednoho e-mailu lze mít nejvýš {$this->config->maxPerEmail} rezervací. "
                    . 'Pro další skupiny kontaktujte organizátora.']];
            }
            $token = ManageToken::generate();
            $now = $this->nowSql();
            $this->reservationDao->exec("INSERT INTO school_reservation (offer_id_fk, event_id_fk, status, pupils, teachers, school_name,
                    school_place, field_code, school_class, contact_email, contact_name, contact_phone, phone_consent_at, note, consent_at,
                    login_login_name_fk, manage_token_hash, created)
                VALUES (:o, :e, :s, :pupils, :teachers, :school_name, :school_place, :field_code, :school_class, :contact_email,
                    :contact_name, :contact_phone, :phone_consent_at, :note, :consent_at, :login, :hash, :created)", [
                'o' => $offerId, 'e' => (int) $data['event_id_fk'], 's' => self::ACTIVE, 'pupils' => $data['pupils'],
                'teachers' => $data['teachers'], 'school_name' => $data['school_name'], 'school_place' => $data['school_place'],
                'field_code' => $data['field_code'], 'school_class' => $data['school_class'], 'contact_email' => $data['contact_email'],
                'contact_name' => $data['contact_name'], 'contact_phone' => $data['phone_consent'] ? $data['contact_phone'] : null,
                'phone_consent_at' => $data['phone_consent'] ? $now : null, 'note' => $data['note'], 'consent_at' => $now, 'created' => $now,
                'login' => $loginName !== null && $loginName !== '' ? $loginName : null, 'hash' => ManageToken::hash($token),
            ]);
            return ['ok' => true, 'errors' => [], 'id' => $this->reservationDao->lastId(), 'token' => $token];
        });
    }

    /**
     * Zamkne přednášku a ověří, že je v nabídce, rezervovatelná a pojme $persons osob.
     * Vrací null (v pořádku) nebo výsledek s chybou a náhradními přednáškami.
     */
    private function checkSeats(int $offerId, int $eventId, int $persons, ?int $exceptId, bool $allowOver = false): ?array {
        // zámek jen na řádku eventu: souběžné rezervace téže přednášky čekají, až tato transakce skončí
        $this->reservationDao->row("SELECT id FROM event WHERE id = :e FOR UPDATE", ['e' => $eventId]);
        $lecture = $this->reservationDao->row(self::LECTURE_SQL . " JOIN reservation_offer_event oe ON oe.event_id_fk = e.id
            AND oe.offer_id_fk = :o WHERE e.id = :e", ['o' => $offerId, 'e' => $eventId]);
        if ($lecture === null) {
            return ['ok' => false, 'errors' => ['Vybraná přednáška není v nabídce pro školy.']];
        }
        if ($allowOver) {
            return null;
        }
        if (!self::isBookableLecture($lecture, $this->now())) {
            return ['ok' => false, 'errors' => ['Na vybranou přednášku už nelze rezervovat. Vyberte jinou.'],
                'alternatives' => self::alternatives($this->lectures($offerId), $persons, $eventId)];
        }
        $free = self::freeSeats((int) $lecture['capacity'], $this->occupied($eventId, $exceptId));
        if ($free < $persons) {
            $alternatives = self::alternatives($this->lectures($offerId), $persons, $eventId);
            $msg = $free > 0
                ? 'Na přednášce zbývá jen ' . self::places($free) . ', vaše skupina má ' . self::persons($persons) . ' (žáci a pedagogové).'
                : 'Přednáška je už plně obsazená.';
            return ['ok' => false, 'full' => true, 'alternatives' => $alternatives,
                'errors' => [$msg . ($alternatives ? ' Volná místa jsou na jiných přednáškách – vyberte prosím jinou.' : ' Jiná přednáška s dostatkem míst teď není.')]];
        }
        return null;
    }

    public function reservation(int $id): ?array {
        return $this->reservationDao->row(self::RESERVATION_SQL . " WHERE r.id = :id", ['id' => $id]);
    }

    public function reservationByToken(string $token): ?array {
        if (!ManageToken::isWellFormed($token)) {
            return null;
        }
        return $this->reservationDao->row(self::RESERVATION_SQL . " WHERE r.manage_token_hash = :h", ['h' => ManageToken::hash($token)]);
    }

    /** Rezervace nabídky (volitelně jedné přednášky), seřazené podle začátku přednášky a vytvoření. */
    public function reservationsOfOffer(int $offerId, bool $withCancelled = false): array {
        return $this->reservationDao->rows(self::RESERVATION_SQL . " WHERE r.offer_id_fk = :o" . ($withCancelled ? '' : " AND r.status = 'active'")
            . " ORDER BY e.start, e.id, r.status, r.created, r.id", ['o' => $offerId]);
    }

    public function reservationsOfLogin(string $loginName): array {
        return $this->reservationDao->rows(self::RESERVATION_SQL . " WHERE r.login_login_name_fk = :l AND r.anonymized_at IS NULL
            ORDER BY o.event_date DESC, e.start, r.id", ['l' => $loginName]);
    }

    /**
     * Změna rezervace pedagogem (odkaz z e-mailu): údaje, počty, přesun; jen do uzávěrky a v kapacitě.
     *
     * @return array{ok: bool, errors: list<string>, alternatives?: array, moved?: bool}
     */
    public function updateByOwner(string $token, array $input): array {
        $r = $this->reservationByToken($token);
        if ($r === null) {
            return ['ok' => false, 'errors' => ['Odkaz pro správu rezervace neplatí.']];
        }
        if (!self::canOwnerChange($r, $this->offerOf($r), $this->now(), $this->config->changeDaysBefore)) {
            return ['ok' => false, 'errors' => ['Rezervaci už nelze měnit – uplynula uzávěrka změn. Kontaktujte organizátora.']];
        }
        return $this->update($r, $input, false, null, true);
    }

    /**
     * Úprava rezervace správcem (i po uzávěrce). Kapacitu smí překročit – vrátí upozornění.
     *
     * @return array{ok: bool, errors: list<string>, warnings?: list<string>, moved?: bool}
     */
    public function updateByAdmin(int $id, array $input, string $adminLogin): array {
        $r = $this->reservation($id);
        if ($r === null) {
            return ['ok' => false, 'errors' => ['Rezervace neexistuje.']];
        }
        if (!empty($r['anonymized_at'])) {
            return ['ok' => false, 'errors' => ['Anonymizovanou rezervaci nelze měnit.']];
        }
        return $this->update($r, $input, true, $adminLogin, false);
    }

    private function update(array $r, array $input, bool $allowOver, ?string $adminLogin, bool $phoneEditable): array {
        // e-mail a telefon: správce je v administraci nemění (telefon jen se souhlasem pedagoga)
        if (!$phoneEditable) {
            $input['contact_phone'] = '';
            $input['phone_consent'] = false;
        }
        [$data, $errors] = $this->validator->validate($input, false);
        if ($errors) {
            return ['ok' => false, 'errors' => $errors];
        }
        $id = (int) $r['id'];
        $offerId = (int) $r['offer_id_fk'];
        $persons = $data['pupils'] + $data['teachers'];
        $newEvent = (int) $data['event_id_fk'];
        $oldEvent = (int) $r['event_id_fk'];
        $oldPersons = (int) $r['pupils'] + (int) $r['teachers'];
        return $this->reservationDao->transaction(function () use ($r, $data, $id, $offerId, $persons, $newEvent, $oldEvent, $oldPersons, $allowOver, $adminLogin, $phoneEditable) {
            $warnings = [];
            // změna je v pořádku bez kontroly, jen když se nemění přednáška a skupina se nezvětší
            if ($newEvent !== $oldEvent || $persons > $oldPersons) {
                $check = $this->checkSeats($offerId, $newEvent, $persons, $id, $allowOver);
                if ($check !== null) {
                    return $check;
                }
                if ($allowOver) {
                    $lecture = $this->reservationDao->row("SELECT capacity FROM event WHERE id = :e", ['e' => $newEvent]);
                    $occupied = $this->occupied($newEvent, $id) + $persons;
                    if ($lecture['capacity'] !== null && $occupied > (int) $lecture['capacity']) {
                        $warnings[] = "Kapacita přednášky je překročena: {$occupied} osob na {$lecture['capacity']} míst.";
                    }
                }
            }
            if ($data['contact_email'] !== $r['contact_email']) {
                $count = $this->reservationDao->row("SELECT COUNT(*) AS n FROM school_reservation WHERE offer_id_fk = :o
                    AND contact_email = :m AND status = :s AND id <> :id", ['o' => $offerId, 'm' => $data['contact_email'], 's' => self::ACTIVE, 'id' => $id]);
                if (!$adminLogin && (int) $count['n'] >= $this->config->maxPerEmail) {
                    return ['ok' => false, 'errors' => ["Z jednoho e-mailu lze mít nejvýš {$this->config->maxPerEmail} rezervací."]];
                }
            }
            $now = $this->nowSql();
            $params = ['id' => $id, 'e' => $newEvent, 'pupils' => $data['pupils'], 'teachers' => $data['teachers'],
                'school_name' => $data['school_name'], 'school_place' => $data['school_place'], 'field_code' => $data['field_code'],
                'school_class' => $data['school_class'], 'contact_email' => $data['contact_email'], 'contact_name' => $data['contact_name'],
                'note' => $data['note'], 'now' => $now];
            $phoneSql = '';
            if ($phoneEditable) {
                $phone = $data['phone_consent'] ? $data['contact_phone'] : null;
                if ($phone !== null) {
                    $phoneSql = ', contact_phone = :phone, phone_consent_at = COALESCE(phone_consent_at, :consent_at), phone_deleted_at = NULL';
                    $params['phone'] = $phone;
                    $params['consent_at'] = $now;
                } elseif ($r['contact_phone'] !== null) {
                    $phoneSql = ', contact_phone = NULL, phone_deleted_at = :phone_deleted';   // pedagog telefon odebral
                    $params['phone_deleted'] = $now;
                }
            }
            $this->reservationDao->exec("UPDATE school_reservation SET event_id_fk = :e, pupils = :pupils, teachers = :teachers,
                school_name = :school_name, school_place = :school_place, field_code = :field_code, school_class = :school_class,
                contact_email = :contact_email, contact_name = :contact_name, note = :note, updated = :now{$phoneSql} WHERE id = :id", $params);
            return ['ok' => true, 'errors' => [], 'warnings' => $warnings, 'moved' => $newEvent !== $oldEvent];
        });
    }

    /** Zrušení pedagogem (odkaz, do uzávěrky). Telefon se smaže hned. */
    public function cancelByOwner(string $token): array {
        $r = $this->reservationByToken($token);
        if ($r === null) {
            return ['ok' => false, 'errors' => ['Odkaz pro správu rezervace neplatí.']];
        }
        if (!self::canOwnerChange($r, $this->offerOf($r), $this->now(), $this->config->changeDaysBefore)) {
            return ['ok' => false, 'errors' => ['Rezervaci už nelze zrušit odkazem – uplynula uzávěrka změn. Kontaktujte organizátora.']];
        }
        $this->cancel((int) $r['id'], self::CANCELLED_SELF);
        return ['ok' => true, 'errors' => []];
    }

    /** Zrušení správcem (i po uzávěrce). */
    public function cancelByAdmin(int $id, string $adminLogin): array {
        $r = $this->reservation($id);
        if ($r === null || $r['status'] !== self::ACTIVE) {
            return ['ok' => false, 'errors' => ['Rezervace neexistuje nebo už je zrušená.']];
        }
        $this->cancel($id, mb_substr($adminLogin, 0, 100));
        return ['ok' => true, 'errors' => []];
    }

    private function cancel(int $id, string $by): void {
        $now = $this->nowSql();
        $this->reservationDao->exec("UPDATE school_reservation SET status = :s, cancelled_at = :now1, cancelled_by = :by, updated = :now2,
            phone_deleted_at = IF(contact_phone IS NULL, phone_deleted_at, :now3), contact_phone = NULL
            WHERE id = :id AND status = :a", ['s' => self::CANCELLED, 'now1' => $now, 'now2' => $now, 'now3' => $now, 'by' => $by,
            'id' => $id, 'a' => self::ACTIVE]);
    }

    private function offerOf(array $r): array {
        return ['event_date' => $r['offer_event_date'], 'change_until' => $r['offer_change_until'], 'status' => $r['offer_status']];
    }

    //--------------------------------------------------------------------------------------- telefon a osobní údaje

    /**
     * Úklid při každém požadavku na rezervace (dokud není plánovač Z2): smaže telefony rezervací přednášek,
     * jejichž den už skončil. Vrací počet smazaných telefonů.
     */
    public function housekeeping(): int {
        return $this->reservationDao->exec("UPDATE school_reservation r JOIN event e ON e.id = r.event_id_fk
            SET r.contact_phone = NULL, r.phone_deleted_at = :now
            WHERE r.contact_phone IS NOT NULL AND e.start < :today", [
            'now' => $this->nowSql(), 'today' => $this->now()->format('Y-m-d 00:00:00')]);
    }

    /** Tlačítko „Smazat telefony“ – všechny telefony rezervací nabídky. */
    public function deletePhones(int $offerId): int {
        return $this->reservationDao->exec("UPDATE school_reservation SET contact_phone = NULL, phone_deleted_at = :now
            WHERE offer_id_fk = :o AND contact_phone IS NOT NULL", ['o' => $offerId, 'now' => $this->nowSql()]);
    }

    /**
     * Anonymizace rezervací nabídky po akci: smaže e-mail, jméno, telefon, poznámku a vazbu na účet; škola, obor, třída
     * a počty zůstanou pro statistiku. Před koncem dne akce odmítne (null).
     */
    public function anonymize(int $offerId): ?int {
        $offer = $this->offer($offerId);
        if ($offer === null || $this->now() < (new DateTimeImmutable($offer['event_date'], $this->tz))->modify('+1 day')) {
            return null;
        }
        return $this->reservationDao->exec("UPDATE school_reservation SET contact_email = '', contact_name = NULL, note = NULL,
            phone_deleted_at = IF(contact_phone IS NULL, phone_deleted_at, :now1), contact_phone = NULL, login_login_name_fk = NULL,
            anonymized_at = :now2 WHERE offer_id_fk = :o AND anonymized_at IS NULL", ['o' => $offerId, 'now1' => $this->nowSql(), 'now2' => $this->nowSql()]);
    }

    //---------------------------------------------------------------------------------------------- účet pedagoga

    /**
     * Přiřadí k účtu rezervace se stejným kontaktním e-mailem (přihlášený uživatel s e-mailem z registrace, A1).
     * Vrací počet nově přiřazených.
     */
    public function linkToLogin(string $loginName, ?string $email): int {
        $email = mb_strtolower(trim((string) $email));
        if ($loginName === '' || $email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return 0;
        }
        return $this->reservationDao->exec("UPDATE school_reservation SET login_login_name_fk = :l
            WHERE contact_email = :m AND login_login_name_fk IS NULL AND anonymized_at IS NULL", ['l' => $loginName, 'm' => $email]);
    }

    /** Předvyplnění formuláře z poslední rezervace účtu (bez telefonu). */
    public function prefill(string $loginName): array {
        $r = $this->reservationDao->row("SELECT school_name, school_place, field_code, school_class, contact_email, contact_name
            FROM school_reservation WHERE login_login_name_fk = :l AND anonymized_at IS NULL ORDER BY created DESC, id DESC LIMIT 1",
            ['l' => $loginName]);
        return $r ?? [];
    }

    //------------------------------------------------------------------------------------------------- pomocné

    /** Řádky exportu CSV (aktivní rezervace, bez telefonů). */
    public function csvRows(int $offerId): array {
        $ret = [['Přednáška – začátek', 'Přednáška', 'Škola', 'Obec', 'Obor', 'Třída', 'Žáků', 'Pedagogů', 'Osob celkem',
            'Pedagog', 'E-mail', 'Poznámka', 'Vytvořeno', 'Změněno']];
        foreach ($this->reservationsOfOffer($offerId) as $r) {
            $ret[] = [$r['event_start'], (string) $r['event_title'], $r['school_name'], $r['school_place'],
                ReservationValidator::FIELDS_SHORT[$r['field_code']] ?? $r['field_code'], (string) $r['school_class'],
                (int) $r['pupils'], (int) $r['teachers'], (int) $r['pupils'] + (int) $r['teachers'], (string) $r['contact_name'],
                (string) $r['contact_email'], (string) $r['note'], $r['created'], (string) $r['updated']];
        }
        return $ret;
    }

    /** „1 místo“, „2 místa“, „5 míst“. */
    public static function places(int $n): string {
        return $n . ' ' . ($n === 1 ? 'místo' : ($n >= 2 && $n <= 4 ? 'místa' : 'míst'));
    }

    /** „1 osobu“, „2 osoby“, „5 osob“ (4. pád: skupina má …). */
    public static function persons(int $n): string {
        return $n . ' ' . ($n === 1 ? 'osobu' : ($n >= 2 && $n <= 4 ? 'osoby' : 'osob'));
    }

    public static function fmt(?string $dt, string $format = 'j. n. Y G:i'): string {
        return $dt ? (new DateTimeImmutable($dt))->format($format) : '';
    }

    public static function dtOrNull($value): ?string {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }
        $ts = strtotime($value);
        return $ts === false ? null : date('Y-m-d H:i:s', $ts);
    }
}
