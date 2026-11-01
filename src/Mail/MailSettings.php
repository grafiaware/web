<?php

namespace Mail;

use Pes\Mail\Assembly\Party;
use Site\ConfigurationCache;
use LogicException;

/**
 * Odesílatelé, pevní příjemci a loga e-mailů z konfigurace site (ConfigurationWeb::mail()).
 *
 * Klíče v konfiguraci (všechny volitelné, chybějící = výchozí hodnota níže, tj. dosavadní chování):
 * - 'mail.from'       => ['info' => ['adresa', 'jméno'], 'noreply' => [...], ...]
 * - 'mail.recipients' => ['registration.bcc' => [['adresa', 'jméno'], ...], ...]
 * - 'mail.logos'      => ['grafia' => 'cesta k souboru obrázku', ...]
 */
final class MailSettings {

    /** Výchozí hodnoty = hodnoty dříve zapsané přímo v kontrolérech. */
    private const DEFAULT_FROM = [
        'info' => ['info@najdisi.cz', 'web praci.najdisi.cz'],
        'noreply' => ['noreply@najdisi.cz', 'web praci.najdisi.cz'],
        'testmail' => ['info@najdisi.cz', 'web najdisi'],
    ];

    private const DEFAULT_RECIPIENTS = [
        'registration.bcc' => [['webmaster@grafia.cz', 'vs']],
        'exhibitorRegistration.to' => [['slukova@grafia.cz', 'Registace zástupce vystavovatele']],
        'exhibitorRegistration.cc' => [['slehoferova@grafia.cz', 'Registace zástupce vystavovatele']],
        'testmail.to' => [['selnerova@grafia.cz', 'selnerovaV'], ['svoboda@grafia.cz', 'testMail']],
    ];

    private const DEFAULT_LOGOS = [
        'grafia' => __DIR__ . '/Assets/logo_grafia.gif',
        'klic' => __DIR__ . '/Assets/logo_klic.png',
    ];

    public function __construct(
        private readonly array $config,
    ) {
    }

    public static function fromConfiguration(): self {
        return new self(ConfigurationCache::mail());
    }

    /**
     * @return array{0: string, 1: string} [adresa, jméno]
     */
    public function sender(string $key): array {
        $from = ($this->config['mail.from'] ?? []) + self::DEFAULT_FROM;
        if (!isset($from[$key])) {
            throw new LogicException("Neznámý odesílatel e-mailu '$key'.");
        }
        return [(string) $from[$key][0], (string) ($from[$key][1] ?? '')];
    }

    /**
     * @return list<array{0: string, 1: string}>
     */
    public function recipients(string $key): array {
        $recipients = ($this->config['mail.recipients'] ?? []) + self::DEFAULT_RECIPIENTS;
        if (!isset($recipients[$key])) {
            throw new LogicException("Neznámá skupina příjemců e-mailu '$key'.");
        }
        return array_map(
            static fn (array $r): array => [(string) $r[0], (string) ($r[1] ?? '')],
            array_values($recipients[$key])
        );
    }

    /**
     * Nová Party s odesílatelem podle klíče.
     */
    public function party(string $senderKey): Party {
        return (new Party())->setFrom(...$this->sender($senderKey));
    }

    /**
     * Přidá pevné příjemce ze skupiny $key jako to / cc / bcc.
     */
    public function addRecipients(Party $party, string $kind, string $key): Party {
        foreach ($this->recipients($key) as [$address, $name]) {
            match ($kind) {
                'to' => $party->addTo($address, $name),
                'cc' => $party->addCc($address, $name),
                'bcc' => $party->addBcc($address, $name),
                default => throw new LogicException("Neznámý typ příjemce '$kind'."),
            };
        }
        return $party;
    }

    /**
     * Logo jako data URI pro vložení do HTML e-mailu.
     */
    public function logoDataUri(string $key): string {
        $logos = ($this->config['mail.logos'] ?? []) + self::DEFAULT_LOGOS;
        $file = $logos[$key] ?? null;
        if ($file === null || !is_readable($file)) {
            throw new LogicException("Logo e-mailu '$key' nenalezeno" . ($file ? ": $file" : '.'));
        }
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($file) ?: 'application/octet-stream';
        return 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($file));
    }
}
