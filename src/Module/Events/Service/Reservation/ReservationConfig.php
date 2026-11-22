<?php

namespace Events\Service\Reservation;

use Site\ConfigurationCache;

/**
 * R1 (#48): nastavení rezervací pro školy (konfigurace `reservation` site, ConfigurationEvents::reservation()).
 * Chybějící hodnoty mají výchozí stav ze zadání docs/vp/zadani/R1.md.
 */
final class ReservationConfig {

    public const DEFAULT_CONSENT_TEXT = 'Souhlasím se zpracováním uvedených údajů za účelem rezervace a organizace přednášky '
        . 'na Veletrhu práce a vzdělávání.';
    public const DEFAULT_PHONE_CONSENT_TEXT = 'Souhlasím, aby mi organizátor v den akce krátce před začátkem přednášky zavolal '
        . 'a ověřil příchod skupiny. Telefon se použije jen k tomu a po skončení dne akce se smaže.';

    public function __construct(
        public readonly int $maxPerEmail = 5,               // aktivních rezervací z jednoho e-mailu v nabídce
        public readonly int $maxPupils = 200,                // horní mez počtu žáků v jedné rezervaci (ochrana proti překlepu)
        public readonly int $maxTeachers = 20,
        public readonly int $changeDaysBefore = 2,           // výchozí uzávěrka změn: N dní před akcí (23:59)
        public readonly string $consentText = self::DEFAULT_CONSENT_TEXT,
        public readonly string $phoneConsentText = self::DEFAULT_PHONE_CONSENT_TEXT,
        public readonly string $policyUrl = '',              // odkaz na zásady zpracování osobních údajů (A4)
        public readonly string $organizerContact = '',       // kontakt na organizátora po uzávěrce (text)
        public readonly string $mailSender = 'info',         // odesílatel e-mailů (MailSettings::party)
        public readonly string $notifyGroup = 'reservation.notify',   // skupina příjemců upozornění organizátorovi
        public readonly string $timezone = 'Europe/Prague',
    ) {
    }

    public static function fromArray(array $c): self {
        $d = new self();
        return new self(
            max(1, (int) ($c['maxPerEmail'] ?? $d->maxPerEmail)),
            max(1, (int) ($c['maxPupils'] ?? $d->maxPupils)),
            max(1, (int) ($c['maxTeachers'] ?? $d->maxTeachers)),
            max(0, (int) ($c['changeDaysBefore'] ?? $d->changeDaysBefore)),
            (string) ($c['consentText'] ?? $d->consentText),
            (string) ($c['phoneConsentText'] ?? $d->phoneConsentText),
            (string) ($c['policyUrl'] ?? $d->policyUrl),
            (string) ($c['organizerContact'] ?? $d->organizerContact),
            (string) ($c['mail']['sender'] ?? $d->mailSender),
            (string) ($c['mail']['notifyGroup'] ?? $d->notifyGroup),
            (string) ($c['timezone'] ?? $d->timezone),
        );
    }

    public static function fromConfiguration(): self {
        return self::fromArray(ConfigurationCache::reservation());
    }
}
