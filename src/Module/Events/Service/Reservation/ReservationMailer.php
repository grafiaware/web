<?php

namespace Events\Service\Reservation;

use Mail\MailSettings;
use Mail\MessageFactory\HtmlMessage;
use Pes\Mail\Assembly;
use Pes\Mail\Assembly\Content;
use Pes\Mail\MailInterface;

/**
 * R1 (#48): e-maily rezervací pro školy (podle AuthMailer). Odesílatel z MailSettings (konfigurace reservation.mail.sender),
 * upozornění organizátorovi na skupinu příjemců reservation.notify (mail.recipients).
 *
 * Telefon pedagoga není v žádném e-mailu; odkaz pro správu (token) jen v e-mailu pedagogovi, nikdy organizátorovi ani v logu.
 */
class ReservationMailer {

    private const MESSAGES = __DIR__ . '/../../Middleware/Events/Controler/Reservation/Messages/';
    private const SUBJECT_PREFIX = 'Veletrh práce a vzdělávání - ';

    public const CREATED = 'created';
    public const CHANGED = 'changed';
    public const CANCELLED = 'cancelled';
    public const CHANGED_BY_ORGANIZER = 'changed_by_organizer';
    public const CANCELLED_BY_ORGANIZER = 'cancelled_by_organizer';

    private const SUBJECTS = [
        self::CREATED => 'Rezervace přednášky pro školu.',
        self::CHANGED => 'Změna rezervace přednášky.',
        self::CANCELLED => 'Zrušení rezervace přednášky.',
        self::CHANGED_BY_ORGANIZER => 'Organizátor upravil vaši rezervaci.',
        self::CANCELLED_BY_ORGANIZER => 'Organizátor zrušil vaši rezervaci.',
    ];

    private const ORGANIZER_SUBJECTS = [
        self::CREATED => 'Nová rezervace školy',
        self::CHANGED => 'Změna rezervace školy',
        self::CANCELLED => 'Škola zrušila rezervaci',
    ];

    public function __construct(
        private MailInterface $mail,
        private HtmlMessage $messageFactory,
        private MailSettings $settings,
        private ReservationConfig $config,
    ) {
    }

    /**
     * E-mail pedagogovi. $manageUrl = odkaz pro správu (jen u nové rezervace – token se jinak neuchovává);
     * $registerUrl = adresa webu pro pozvánku k registraci (jen u nové rezervace).
     */
    public function sendToPedagog(string $kind, array $reservation, ?string $manageUrl = null, ?string $registerUrl = null): void {
        $email = trim((string) $reservation['contact_email']);
        if ($email === '') {
            return;
        }
        $context = self::context($kind, $reservation) + [
            'manageUrl' => $manageUrl ?? false,
            'registerUrl' => $registerUrl ?? false,
            'organizerContact' => $this->config->organizerContact !== '' ? $this->config->organizerContact : false,
            'data_logo_grafia' => $this->settings->logoDataUri('grafia'),
        ];
        $body = $this->messageFactory->create(self::MESSAGES . 'reservation-pedagog.php', $context);
        $this->mail->mail((new Assembly())
            ->setContent((new Content())->setSubject(self::SUBJECT_PREFIX . self::SUBJECTS[$kind])->setHtml($body))
            ->setParty($this->settings->party($this->config->mailSender)->addTo($email, (string) ($reservation['contact_name'] ?? ''))));
    }

    /** Upozornění organizátorovi (bez telefonu a bez odkazu pro správu). */
    public function notifyOrganizer(string $kind, array $reservation): void {
        $party = $this->settings->addRecipients($this->settings->party($this->config->mailSender), 'to', $this->config->notifyGroup);
        $context = self::context($kind, $reservation) + [
            'heading' => self::ORGANIZER_SUBJECTS[$kind] ?? 'Rezervace školy',
            'data_logo_grafia' => $this->settings->logoDataUri('grafia'),
        ];
        $body = $this->messageFactory->create(self::MESSAGES . 'reservation-organizer.php', $context);
        $subject = (self::ORGANIZER_SUBJECTS[$kind] ?? 'Rezervace školy') . ': ' . $reservation['school_name'];
        $this->mail->mail((new Assembly())
            ->setContent((new Content())->setSubject(self::SUBJECT_PREFIX . $subject)->setHtml($body))
            ->setParty($party));
    }

    /**
     * Kontext šablon: druh zprávy a souhrn rezervace jako řádky [popisek, hodnota] (prázdné hodnoty jako „–“,
     * HtmlMessage prázdné hodnoty nepřijímá). Telefon se neuvádí.
     */
    public static function context(string $kind, array $r): array {
        $v = fn($x) => trim((string) $x) !== '' ? (string) $x : '–';
        $lecture = SchoolReservationService::fmt($r['event_start'] ?? null, 'j. n. Y G:i')
            . (!empty($r['event_end']) ? '–' . SchoolReservationService::fmt($r['event_end'], 'G:i') : '');
        return [
            'kind' => $kind,
            'offerName' => $v($r['offer_name'] ?? ''),
            'deadline' => $v(SchoolReservationService::fmt($r['offer_change_until'] ?? null)),
            'rows' => [
                ['Přednáška', $v($r['event_title'] ?? '')],
                ['Čas', $v($lecture)],
                ['Škola', $v($r['school_name'] ?? '')],
                ['Obec', $v($r['school_place'] ?? '')],
                ['Cílový obor', $v(ReservationValidator::FIELDS[$r['field_code'] ?? ''] ?? '')],
                ['Třída', $v($r['school_class'] ?? '')],
                ['Počet žáků', (string) (int) ($r['pupils'] ?? 0)],
                ['Počet pedagogů', (string) (int) ($r['teachers'] ?? 0)],
                ['Pedagog', $v($r['contact_name'] ?? '')],
                ['E-mail', $v($r['contact_email'] ?? '')],
                ['Poznámka', $v($r['note'] ?? '')],
            ],
        ];
    }
}
