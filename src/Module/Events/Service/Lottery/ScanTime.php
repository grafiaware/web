<?php

namespace Events\Service\Lottery;

use DateTimeImmutable;

/**
 * D1: čas skenu z fronty offline. Čas vydání losu = čas skenu v telefonu obsluhy, pokud je věrohodný.
 */
final class ScanTime {

    /** Tolerance pro rozdíl hodin telefonu a serveru (sken „z budoucnosti“ se bere jako teď). */
    public const FUTURE_TOLERANCE_MINUTES = 5;

    /**
     * @return DateTimeImmutable|null čas akce, nebo null když je sken starší než $maxOfflineMinutes (odmítne se)
     */
    public static function resolve(?string $scannedAt, DateTimeImmutable $now, int $maxOfflineMinutes): ?DateTimeImmutable {
        if ($scannedAt === null || trim($scannedAt) === '') {
            return $now;
        }
        try {
            $t = is_numeric($scannedAt)
                ? (new DateTimeImmutable('@' . (int) floor(((float) $scannedAt) / 1000)))->setTimezone($now->getTimezone())
                : (new DateTimeImmutable($scannedAt))->setTimezone($now->getTimezone());
        } catch (\Exception $e) {
            return $now;
        }
        if ($t > $now->modify('+' . self::FUTURE_TOLERANCE_MINUTES . ' minutes')) {
            return $now;
        }
        if ($t < $now->modify("-{$maxOfflineMinutes} minutes")) {
            return null;
        }
        return $t > $now ? $now : $t;
    }
}
