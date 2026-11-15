<?php

namespace Events\Service\Lottery;

use Site\ConfigurationCache;

/**
 * D1: nastavení losování (konfigurace `lottery` site, viz docs/vp/zadani/D1.md). Chybějící hodnoty mají výchozí stav ze zadání.
 */
final class LotteryConfig {

    public const STATION_ISSUE = 'issue';

    /**
     * @param array<string, int> $refresh akce => minuty prodloužení (čas akce + minuty)
     * @param array<string, array{label: string, issue: bool, refresh: string}> $stations
     */
    public function __construct(
        public readonly int $issueMinMinutes = 30,
        public readonly int $issueRounds = 2,
        public readonly array $refresh = ['vchod' => 60, 'infostanek' => 60, 'rucne' => 60],
        public readonly array $stations = [
            'vchod' => ['label' => 'Vchod', 'issue' => true, 'refresh' => 'vchod'],
            'infostanek' => ['label' => 'Infostánek', 'issue' => false, 'refresh' => 'infostanek'],
        ],
        public readonly bool $oneWinAcrossLotteries = false,
        public readonly int $maxOfflineMinutes = 240,
        public readonly string $joinedMailSender = 'info',
    ) {
    }

    public static function fromArray(array $c): self {
        $d = new self();
        return new self(
            (int) ($c['presence']['issueMinMinutes'] ?? $d->issueMinMinutes),
            max(1, (int) ($c['presence']['issueRounds'] ?? $d->issueRounds)),
            array_map('intval', $c['presence']['refresh'] ?? $d->refresh),
            $c['stations'] ?? $d->stations,
            (bool) ($c['oneWinAcrossLotteries'] ?? $d->oneWinAcrossLotteries),
            (int) ($c['scan']['maxOfflineMinutes'] ?? $d->maxOfflineMinutes),
            (string) ($c['mail']['sender'] ?? $d->joinedMailSender),
        );
    }

    public static function fromConfiguration(): self {
        return self::fromArray(ConfigurationCache::lottery());
    }

    /** Minuty prodloužení pro akci, nebo null (akce platnost neprodlužuje). */
    public function refreshMinutes(string $event): ?int {
        return isset($this->refresh[$event]) ? (int) $this->refresh[$event] : null;
    }

    public function station(string $key): ?array {
        $s = $this->stations[$key] ?? null;
        return is_array($s) ? $s + ['label' => $key, 'issue' => false, 'refresh' => $key] : null;
    }
}
