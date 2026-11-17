<?php

namespace Events\Service\Lottery;

use DateTimeImmutable;

/**
 * D1 (#46): zpracování skenu QR kódu obsluhou na stanovišti (vchod = vydání / prodloužení, infostánek = prodloužení).
 * Čas skenu může přijít z fronty offline v telefonu obsluhy (ScanTime).
 * D1c (#108): rozpozná i papírový lístek – kód L<12 znaků> z QR ústřižku nebo ručně zadané číslo lístku.
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
    public const R_OTHER_LOTTERY = 'other_lottery';

    public function __construct(private LotteryService $lotteries, private LotteryPaperService $papers) {
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
        $paper = $this->paperFromText($lotteryId, $scannedText);
        if ($paper !== null) {
            if ($paper['ticket'] === null) {
                return $this->out(false, $paper['result'], $paper['message']);
            }
            $label = LotteryPaperService::label((int) $paper['ticket']['number']);
            $at = ScanTime::resolve($scannedAt, $this->lotteries->now(), $config->maxOfflineMinutes);
            if ($at === null) {
                return $this->out(false, self::R_TOO_OLD, 'Sken je příliš starý, naskenujte lístek znovu.', $label);
            }
            $p = $this->lotteries->presenceActionPaper($lotteryId, (int) $paper['ticket']['id'], (string) $station['refresh'], $at, $by,
                (bool) $station['issue'], $stationKey);
            return $this->result($lotteryId, $p, $label, null, $at, 'Lístek zatím nebyl vydán – lístek se odevzdává u vchodu.');
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

    /**
     * Papírový lístek v naskenovaném / zadaném textu: null = není to lístek; jinak lístek nebo chybový výsledek.
     *
     * @return array{ticket: ?array, result: string, message: string}|null
     */
    private function paperFromText(int $lotteryId, string $text): ?array {
        $code = LotteryPaperService::codeFromScannedText($text);
        if ($code !== null) {
            $ticket = $this->papers->byCode($code);
            if ($ticket === null) {
                return ['ticket' => null, 'result' => self::R_UNKNOWN_CODE, 'message' => 'Neznámý lístek.'];
            }
            if ((int) $ticket['lottery_id_fk'] !== $lotteryId) {
                return ['ticket' => null, 'result' => self::R_OTHER_LOTTERY, 'message' => 'Lístek patří k jinému losování.'];
            }
            return ['ticket' => $ticket, 'result' => '', 'message' => ''];
        }
        $number = LotteryPaperService::numberFromText($text);
        if ($number !== null) {
            $ticket = $this->papers->byNumber($lotteryId, $number);
            return $ticket !== null ? ['ticket' => $ticket, 'result' => '', 'message' => '']
                : ['ticket' => null, 'result' => self::R_UNKNOWN_CODE, 'message' => 'Lístek č. ' . LotteryPaperService::formatNumber($number) . ' v tomto losování není.'];
        }
        return null;
    }

    private function apply(int $lotteryId, string $login, string $event, bool $allowIssue, DateTimeImmutable $at, string $by, string $station): array {
        $name = $this->lotteries->displayName($login);
        $p = $this->lotteries->presenceAction($lotteryId, $login, $event, $at, $by, $allowIssue, $station);
        return $this->result($lotteryId, $p, $name, $login, $at, 'Los zatím nebyl vydán – pošlete návštěvníka ke vchodu.');
    }

    private function result(int $lotteryId, array $p, string $name, ?string $login, DateTimeImmutable $at, string $notValidMessage): array {
        $rounds = $p['eligibleFrom'] && $p['validUntil']
            ? PresenceCalculator::fromConfig($this->lotteries->config())->eligibleRounds($p['eligibleFrom'], $p['validUntil'],
                $this->lotteries->roundTimes($lotteryId, true, $at))
            : [];
        $list = implode(', ', array_map(fn(DateTimeImmutable $t) => $t->format('G:i'), $rounds));
        switch ($p['result']) {
            case LotteryService::P_ISSUED:
                $msg = $rounds ? "Los vydán – hraje v kolech {$list}." : 'Los vydán, ale dnes už nezbývá žádné kolo losování.';
                return $this->out(true, self::R_ISSUED, $msg, $name, $login, $at);
            case LotteryService::P_REFRESHED:
                $msg = 'Přítomnost potvrzena, los platí do ' . $p['validUntil']->format('G:i') . ($rounds ? " – hraje v kolech {$list}." : '.');
                return $this->out(true, self::R_REFRESHED, $msg, $name, $login, $at);
            case LotteryService::P_LOGGED:
                return $this->out(true, self::R_LOGGED, 'Zaznamenáno (platnost se nemění).', $name, $login, $at);
            case LotteryService::P_NOT_JOINED:
                return $this->out(false, self::R_NOT_JOINED, 'Není zapojen do losování – ať se zapojí v profilu a přijde znovu.', $name, $login, $at);
            default:
                return $this->out(false, self::R_NOT_VALID, $notValidMessage, $name, $login, $at);
        }
    }

    private function out(bool $ok, string $result, string $message, string $name = '', ?string $login = null, ?DateTimeImmutable $at = null): array {
        return ['ok' => $ok, 'result' => $result, 'message' => $message, 'name' => $name, 'login' => $login,
            'scannedAt' => $at?->format('Y-m-d H:i:s')];
    }
}
