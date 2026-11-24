<?php

namespace Events\Service\Reservation;

/**
 * R1 (#48): kontrola údajů rezervace z formuláře (veřejný formulář, správa odkazem, administrace). Bez DB.
 *
 * validate() vrací [čistá data, chyby]. Čistá data mají klíče sloupců school_reservation (+ event_id_fk).
 */
final class ReservationValidator {

    /** Cílové obory (kód v DB => název podle školského zákona, kódové písmeno oboru KKOV). */
    public const FIELDS = [
        'C' => 'Základní škola (obor C – např. 2. stupeň ZŠ)',
        'J' => 'Střední vzdělání bez maturity a výučního listu (obor J – 1–2leté obory)',
        'HE' => 'Střední vzdělání s výučním listem (obory H, E – učební obory)',
        'K' => 'Střední vzdělání s maturitou – všeobecné (obor K – gymnázia)',
        'ML' => 'Střední vzdělání s maturitou – odborné / s výučním listem (obory M, L – odborné školy, lycea, nástavby)',
        'NP' => 'Vyšší odborné vzdělání / Konzervatoř (obory N, P – VOŠ a konzervatoře)',
    ];

    /** Krátký název oboru (přehledy, CSV). */
    public const FIELDS_SHORT = ['C' => 'ZŠ (C)', 'J' => 'J', 'HE' => 'H, E', 'K' => 'K (gymnázium)', 'ML' => 'M, L', 'NP' => 'N, P'];

    /** Skryté pole proti robotům – člověk ho nevyplní. */
    public const HONEYPOT = 'web_skoly';

    public function __construct(private ReservationConfig $config) {
    }

    /**
     * @param array $in vstup z formuláře
     * @param bool $requireConsent nová rezervace z veřejného formuláře (souhlas se zpracováním povinný)
     * @return array{0: array<string, mixed>, 1: list<string>}
     */
    public function validate(array $in, bool $requireConsent = true): array {
        $errors = [];
        $text = fn(string $k, int $max) => mb_substr(trim(preg_replace('/\s+/u', ' ', (string) ($in[$k] ?? ''))), 0, $max);

        if (trim((string) ($in[self::HONEYPOT] ?? '')) !== '') {
            return [[], ['Formulář se nepodařilo odeslat. Zkuste to prosím znovu.']];
        }
        $data = [
            'event_id_fk' => ctype_digit(trim((string) ($in['event_id'] ?? ''))) ? (int) $in['event_id'] : null,
            'school_name' => $text('school_name', 200),
            'school_place' => $text('school_place', 100),
            'field_code' => strtoupper(trim((string) ($in['field_code'] ?? ''))),
            'school_class' => $text('school_class', 50),
            'contact_email' => mb_strtolower(trim((string) ($in['contact_email'] ?? ''))),
            'contact_name' => $text('contact_name', 200),
            'contact_phone' => self::normalizePhone((string) ($in['contact_phone'] ?? '')),
            'note' => mb_substr(trim((string) ($in['note'] ?? '')), 0, 2000),
        ];
        if ($data['event_id_fk'] === null) {
            $errors[] = 'Vyberte přednášku.';
        }
        if ($data['school_name'] === '') {
            $errors[] = 'Vyplňte název školy.';
        }
        if ($data['school_place'] === '') {
            $errors[] = 'Vyplňte obec školy.';
        }
        if (!isset(self::FIELDS[$data['field_code']])) {
            $errors[] = 'Vyberte cílový obor.';
        }
        $pupils = trim((string) ($in['pupils'] ?? ''));
        if (!ctype_digit($pupils) || (int) $pupils < 1) {
            $errors[] = 'Počet žáků musí být celé číslo, aspoň 1.';
        } elseif ((int) $pupils > $this->config->maxPupils) {
            $errors[] = "Počet žáků v jedné rezervaci může být nejvýš {$this->config->maxPupils}. Větší skupinu rozdělte do více rezervací.";
        }
        $data['pupils'] = (int) $pupils;
        $teachers = trim((string) ($in['teachers'] ?? ''));
        if ($teachers === '') {
            $teachers = '1';   // když nezadá, 1 pedagog
        }
        if (!ctype_digit($teachers) || (int) $teachers > $this->config->maxTeachers) {
            $errors[] = "Počet pedagogů musí být celé číslo od 0 do {$this->config->maxTeachers}.";
        }
        $data['teachers'] = (int) $teachers;
        if (filter_var($data['contact_email'], FILTER_VALIDATE_EMAIL) === false || mb_strlen($data['contact_email']) > 200) {
            $errors[] = 'Vyplňte platný kontaktní e-mail.';
        }
        $rawPhone = trim((string) ($in['contact_phone'] ?? ''));
        $phoneConsent = (bool) ($in['phone_consent'] ?? false);
        if ($rawPhone !== '' && $data['contact_phone'] === null) {
            $errors[] = 'Telefon zadejte jako číslo, např. 777 123 456 nebo +420 777 123 456.';
        } elseif ($data['contact_phone'] !== null && !$phoneConsent) {
            $errors[] = 'Telefon uložíme jen se souhlasem k ověření příchodu skupiny – zaškrtněte ho, nebo telefon nevyplňujte.';
        }
        $data['phone_consent'] = $data['contact_phone'] !== null && $phoneConsent;
        if ($requireConsent && !($in['consent'] ?? false)) {
            $errors[] = 'Pro rezervaci je nutný souhlas se zpracováním údajů.';
        }
        foreach (['school_class', 'contact_name', 'note'] as $k) {
            if ($data[$k] === '') {
                $data[$k] = null;
            }
        }
        return [$data, $errors];
    }

    /** „777 123 456“, „+420 777-123-456“ → „+420777123456“ / „777123456“; neplatné → null, prázdné → null. */
    public static function normalizePhone(string $phone): ?string {
        $phone = trim($phone);
        if ($phone === '') {
            return null;
        }
        $digits = preg_replace('/[\s\-\.\/\(\)]/', '', $phone);
        if (str_starts_with($digits, '00')) {
            $digits = '+' . substr($digits, 2);
        }
        if (!preg_match('/^\+?\d{9,15}$/', $digits)) {
            return null;
        }
        return $digits;
    }

    /** Telefon pro odkaz tel: (české číslo bez předvolby doplní +420). */
    public static function telHref(string $phone): string {
        return 'tel:' . (preg_match('/^\d{9}$/', $phone) ? '+420' . $phone : $phone);
    }
}
