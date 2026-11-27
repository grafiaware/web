<?php

namespace Auth\Service\Source;

/**
 * F1 (#47, docs/vp/zadani/F1.md): zdroj návštěvy – kód odkazu a kanál (/events/v1/r/<kód>/<kanál>) nebo UTM parametry.
 * Žádné osobní údaje; ukládá se do session a se souhlasem „analytika“ i do cookie vp_zdroj.
 */
final class VisitSource {

    public const CODE_PATTERN = '/^[a-z][a-z0-9-]{1,29}$/';
    public const CHANNEL_PATTERN = '/^[a-z0-9-]{1,10}$/';
    public const UTM_MAX = 100;

    public function __construct(
        public readonly ?string $linkCode,
        public readonly ?string $channel,
        public readonly ?string $utmSource,
        public readonly ?string $utmMedium,
        public readonly ?string $utmCampaign,
        public readonly string $landingAt,      // 'Y-m-d H:i:s'
    ) {
    }

    public static function fromLink(string $code, ?string $channel, \DateTimeInterface $now): ?self {
        if (!preg_match(self::CODE_PATTERN, $code) || ($channel !== null && !preg_match(self::CHANNEL_PATTERN, $channel))) {
            return null;
        }
        return new self($code, $channel, null, null, null, $now->format('Y-m-d H:i:s'));
    }

    /** UTM z parametrů adresy; null = žádný použitelný utm_source ani utm_campaign. */
    public static function fromUtm(array $query, \DateTimeInterface $now): ?self {
        $source = self::utm($query['utm_source'] ?? null);
        $campaign = self::utm($query['utm_campaign'] ?? null);
        if ($source === null && $campaign === null) {
            return null;
        }
        return new self(null, null, $source, self::utm($query['utm_medium'] ?? null), $campaign, $now->format('Y-m-d H:i:s'));
    }

    /** Hodnota UTM: text bez řídicích znaků, oříznutý, nejvýš UTM_MAX znaků; prázdné = null. */
    public static function utm($value): ?string {
        if (!is_string($value)) {
            return null;
        }
        if (!mb_check_encoding($value, 'UTF-8')) {
            return null;
        }
        $v = trim(preg_replace('/[\p{Cc}\p{Cf}<>"]+/u', '', $value) ?? '');
        return $v === '' ? null : mb_substr($v, 0, self::UTM_MAX);
    }

    public function toArray(): array {
        return ['c' => $this->linkCode, 'k' => $this->channel, 's' => $this->utmSource, 'm' => $this->utmMedium,
            'n' => $this->utmCampaign, 't' => $this->landingAt];
    }

    /** Ze session / cookie – každá hodnota se znovu kontroluje (cookie může kdokoli změnit). */
    public static function fromArray($a): ?self {
        if (!is_array($a)) {
            return null;
        }
        $code = is_string($a['c'] ?? null) && preg_match(self::CODE_PATTERN, $a['c']) ? $a['c'] : null;
        $channel = $code !== null && is_string($a['k'] ?? null) && preg_match(self::CHANNEL_PATTERN, $a['k']) ? $a['k'] : null;
        $t = is_string($a['t'] ?? null) && preg_match('/^\d{4}-\d\d-\d\d \d\d:\d\d:\d\d$/', $a['t']) ? $a['t'] : null;
        $source = new self($code, $channel, self::utm($a['s'] ?? null), self::utm($a['m'] ?? null), self::utm($a['n'] ?? null), $t ?? '');
        if ($t === null || ($code === null && $source->utmSource === null && $source->utmCampaign === null)) {
            return null;
        }
        return $source;
    }

    public function encode(): string {
        return rtrim(strtr(base64_encode(json_encode($this->toArray(), JSON_UNESCAPED_UNICODE)), '+/', '-_'), '=');
    }

    public static function decode(?string $value): ?self {
        if ($value === null || $value === '' || strlen($value) > 2000) {
            return null;
        }
        $json = base64_decode(strtr($value, '-_', '+/'), true);
        return $json === false ? null : self::fromArray(json_decode($json, true));
    }

    public function landingAt(): \DateTimeImmutable {
        return new \DateTimeImmutable($this->landingAt);
    }
}
