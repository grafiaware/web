<?php

namespace Events\Service\Lottery;

use DateTimeImmutable;

/**
 * D1 (#46): zpracování skenu QR kódu obsluhou na stanovišti (vchod = vydání / prodloužení, infostánek = prodloužení).
 * Čas skenu může přijít z fronty offline v telefonu obsluhy (ScanTime).
 */
class LotteryScanService {

    public const R_ISSUED = LotteryService::P_ISSUED;
    public const R_REFRESHED = LotteryService::P_REFRESHED;
    public const R_LOGGED = LotteryService::P_LOGGED;
    public const R_NOT_JOINED = LotteryService::P_NOT_JOINED;
    public const R_NOT_VALID = LotteryService::P_NOT_VALID;
    public const R_INVALID_CODE = 'invalid_code';
    public const R_UNKNOWN_CODE = 'unknown_code';
    public const R_UNKNOWN_STATION = 'unknown_station';
    public const R_TOO_OLD = 'too_old';
    public const R_LOTTERY_CLOSED = 'lottery_closed';

    public function __construct(private LotteryService $lotteries) {
    }

    /**
     * @return array{ok: bool, result: string, message: string, name: string, login: ?string, scannedAt: ?string}
     */
    public function scan(int $lotteryId, string $scannedText, string $stationKey, ?string $scannedAt, string $by): array {
        $config = $this->lotteries->config();
        $station = $config->station($stationKey);
        if ($station === null) {
            return $this->out(false, self::R_UNKNOWN_STATION, 'Neznámé stanoviště.');
        }
        $lottery = $this->lotteries->lottery($lotteryId);
        if ($lottery === null || $lottery['status'] !== LotteryService::STATUS_OPEN) {
            return $this->out(false, self::R_LOTTERY_CLOSED, 'Losování není otevřené.');
        }
        $code = VisitorCodeGenerator::fromScannedText($scannedText);
        if ($code === null) {
            return $this->out(false, self::R_INVALID_CODE, 'Neplatný kód.');
        }
        $login = $this->lotteries->loginByCode($code);
        if ($login === null) {
            return $this->out(false, self::R_UNKNOWN_CODE, 'Neznámý kód.');
        }
        $at = ScanTime::resolve($scannedAt, $this->lotteries->now(), $config->maxOfflineMinutes);
        if ($at === null) {
            return $this->out(false, self::R_TOO_OLD, 'Sken je příliš starý, naskenujte kód znovu.', $this->lotteries->displayName($login), $login);
        }
        return $this->apply($lotteryId, $login, (string) $station['refresh'], (bool) $station['issue'], $at, $by, $stationKey);
    }

    /**
     * Ruční vydání / prodloužení obsluhou (záloha bez skenu).
     */
    public function manual(int $lotteryId, string $login, bool $issue, string $by): array {
        return $this->apply($lotteryId, $login, $issue ? 'vchod' : LotteryService::EVENT_MANUAL, $issue,
            $this->lotteries->now(), $by, 'rucne');
    }

    private function apply(int $lotteryId, string $login, string $event, bool $allowIssue, DateTimeImmutable $at, string $by, string $station): array {
        $name = $this->lotteries->displayName($login);
        $p = $this->lotteries->presenceAction($lotteryId, $login, $event, $at, $by, $allowIssue, $station);
        $rounds = $p['eligibleFrom'] && $p['validUntil']
            ? PresenceCalculator::fromConfig($this->lotteries->config())->eligibleRounds($p['eligibleFrom'], $p['validUntil'],
                array_filter($this->lotteries->roundTimes($lotteryId), fn($t) => $t >= $at))
            : [];
        $list = implode(', ', array_map(fn(DateTimeImmutable $t) => $t->format('G:i'), $rounds));
        switch ($p['result']) {
            case LotteryService::P_ISSUED:
                $msg = $rounds ? "Los vydán – hraje v kolech {$list}." : 'Los vydán, ale dnes už nezbývá žádné kolo losování.';
                return $this->out(true, self::R_ISSUED, $msg, $name, $login, $at);
            case LotteryService::P_REFRESHED:
                $msg = 'Prodlouženo do ' . $p['validUntil']->format('G:i') . ($rounds ? " – hraje v kolech {$list}." : '.');
                return $this->out(true, self::R_REFRESHED, $msg, $name, $login, $at);
            case LotteryService::P_LOGGED:
                return $this->out(true, self::R_LOGGED, 'Zaznamenáno (platnost se nemění).', $name, $login, $at);
            case LotteryService::P_NOT_JOINED:
                return $this->out(false, self::R_NOT_JOINED, 'Není zapojen do losování – ať se zapojí v profilu a přijde znovu.', $name, $login, $at);
            default:
                return $this->out(false, self::R_NOT_VALID, 'Nemá platný los – pošlete ho ke vchodu.', $name, $login, $at);
        }
    }

    private function out(bool $ok, string $result, string $message, string $name = '', ?string $login = null, ?DateTimeImmutable $at = null): array {
        return ['ok' => $ok, 'result' => $result, 'message' => $message, 'name' => $name, 'login' => $login,
            'scannedAt' => $at?->format('Y-m-d H:i:s')];
    }
}
