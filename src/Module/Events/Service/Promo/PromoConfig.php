<?php

namespace Events\Service\Promo;

use Site\ConfigurationCache;

/**
 * F1 + F2 (#47): nastavení odkazů s měřením (konfigurace `promo` site, docs/vp/zadani/F1.md).
 * Chybějící hodnoty mají výchozí stav ze zadání. Kanály sociálních sítí a webu jsou v tabulce network (sloupec channel).
 */
final class PromoConfig {

    public const TARGET_PAGE = 'page';

    public const DEFAULT_TARGETS = [
        'home-register' => ['label' => 'Úvod s oknem registrace', 'path' => '', 'query' => 'modal=register0AXR2026'],   // parametr, který okno registrace otevře (initElements.js)
        'home' => ['label' => 'Úvod', 'path' => '', 'query' => ''],
        self::TARGET_PAGE => ['label' => 'Jiná stránka webu (vložte adresu)'],
    ];

    public const DEFAULT_CHANNELS = [
        'plakat' => ['label' => 'Plakát', 'group' => 'print'],
        'letak' => ['label' => 'Leták', 'group' => 'print'],
        'billboard' => ['label' => 'Billboard', 'group' => 'print'],
        'tisk' => ['label' => 'Jiný tisk', 'group' => 'print'],
        'mail' => ['label' => 'E-mail', 'group' => 'mail'],
        'soc' => ['label' => 'Jiná sociální síť', 'group' => 'social'],
        'qr' => ['label' => 'QR kód na webu', 'group' => 'qr'],
    ];

    public const DEFAULT_TEXTS = [
        'social' => 'Přijďte na Veletrh práce a vzdělávání. Registrujte se předem: {url}',
        'mail' => "Dobrý den,\n\nzveme Vás na Veletrh práce a vzdělávání.\n\nRegistrace předem: {url}\n\nS pozdravem",
        'web' => 'Veletrh práce a vzdělávání – registrace',
        'poster' => 'Přijďte na Veletrh práce a vzdělávání. Registrujte se předem na {url}',   // F3: text plakátu v podkladu PDF
    ];

    public function __construct(
        public readonly string $timezone = 'Europe/Prague',
        public readonly int $sourceCookieDays = 30,
        public readonly string $companyLinkTarget = 'home-register',
        public readonly array $targets = self::DEFAULT_TARGETS,
        public readonly array $channels = self::DEFAULT_CHANNELS,
        public readonly array $texts = self::DEFAULT_TEXTS,
    ) {
    }

    public static function fromArray(array $c): self {
        $d = new self();
        $targets = is_array($c['targets'] ?? null) && $c['targets'] ? $c['targets'] : $d->targets;
        $targets[self::TARGET_PAGE] ??= self::DEFAULT_TARGETS[self::TARGET_PAGE];
        $companyTarget = (string) ($c['companyLinkTarget'] ?? $d->companyLinkTarget);
        return new self(
            (string) ($c['timezone'] ?? $d->timezone),
            max(1, (int) ($c['sourceCookieDays'] ?? $d->sourceCookieDays)),
            isset($targets[$companyTarget]) && $companyTarget !== self::TARGET_PAGE ? $companyTarget : array_key_first($targets),
            $targets,
            is_array($c['channels'] ?? null) ? $c['channels'] : $d->channels,
            array_replace($d->texts, is_array($c['texts'] ?? null) ? $c['texts'] : []),
        );
    }

    public static function fromConfiguration(): self {
        return self::fromArray(ConfigurationCache::promo());
    }

    public function now(): \DateTimeImmutable {
        return new \DateTimeImmutable('now', new \DateTimeZone($this->timezone));
    }
}
