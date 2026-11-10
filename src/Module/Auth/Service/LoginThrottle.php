<?php

namespace Auth\Service;

use Auth\Model\Dao\LoginProfileDao;

/**
 * A9 (docs/vp/zadani/A9.md): zpomalené přihlašování heslem. Stav v DB (login_profile), platí pro všechny session.
 *
 * Po $freeAttempts neúspěšných pokusech je účet zablokovaný na base × 2^(pokusy − freeAttempts) sekund, nejvýš $maxSeconds.
 * Úspěšné přihlášení (heslem, odkazem, potvrzením registrace, nastavením hesla) počítadlo nuluje.
 * Neexistující účty se nezapisují (cizí klíč na login); odpověď je pro ně stejná.
 */
class LoginThrottle {

    /** @var callable(): \DateTimeImmutable */
    private $clock;

    public function __construct(
        private LoginProfileDao $dao,
        private int $freeAttempts = 5,
        private int $baseSeconds = 30,
        private int $maxSeconds = 900,
        ?callable $clock = null,
    ) {
        $this->clock = $clock ?? fn() => new \DateTimeImmutable();
    }

    /**
     * Kolik sekund ještě trvá blokování (0 = smí se zkusit).
     */
    public function secondsLocked(string $loginName): int {
        $lockedUntil = $this->dao->getThrottle($loginName)['lockedUntil'];
        if ($lockedUntil === null) {
            return 0;
        }
        $diff = (new \DateTimeImmutable($lockedUntil))->getTimestamp() - $this->now()->getTimestamp();
        return max(0, $diff);
    }

    /**
     * Zapíše neúspěšný pokus; vrátí délku nového blokování v sekundách (0 = zatím bez blokování).
     */
    public function failure(string $loginName): int {
        $count = $this->dao->incrementFailed($loginName, $this->now()->format('Y-m-d H:i:s'));
        $delay = self::delaySeconds($count, $this->freeAttempts, $this->baseSeconds, $this->maxSeconds);
        if ($delay > 0) {
            $this->dao->setLockedUntil($loginName, $this->now()->modify("+{$delay} seconds")->format('Y-m-d H:i:s'));
        }
        return $delay;
    }

    public function success(string $loginName): void {
        $this->dao->recordSuccess($loginName, $this->now()->format('Y-m-d H:i:s'));
    }

    /**
     * Prodleva po $failedCount neúspěšných pokusech.
     */
    public static function delaySeconds(int $failedCount, int $freeAttempts, int $baseSeconds, int $maxSeconds): int {
        if ($failedCount < $freeAttempts) {
            return 0;
        }
        $exponent = min(30, $failedCount - $freeAttempts);
        return (int) min($maxSeconds, $baseSeconds * (2 ** $exponent));
    }

    /** Text pro uživatele, např. „za 2 minuty“. */
    public static function waitText(int $seconds): string {
        if ($seconds < 60) {
            return 'za ' . max(1, $seconds) . ' s';
        }
        $minutes = (int) ceil($seconds / 60);
        return 'za ' . $minutes . ' ' . ($minutes === 1 ? 'minutu' : ($minutes <= 4 ? 'minuty' : 'minut'));
    }

    private function now(): \DateTimeImmutable {
        return ($this->clock)();
    }
}
