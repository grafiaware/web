<?php

namespace Events\Service\Lottery;

use DateTimeImmutable;

/**
 * D1: platnost losu na místě – čistá logika bez DB (docs/vp/zadani/D1.md, Platnost losu na místě).
 *
 * Los vydaný v čase T hraje v kolech s plánovaným časem >= T + issueMinMinutes, nejvýš v issueRounds kolech.
 * Prodloužení akcí v čase A s intervalem M: validUntil = MAX(validUntil, A + M).
 * Kolo R se účastní, když eligibleFrom <= R <= validUntil. Rozhoduje plánovaný čas kola.
 */
final class PresenceCalculator {

    public function __construct(private int $issueMinMinutes, private int $issueRounds) {
    }

    public static function fromConfig(LotteryConfig $config): self {
        return new self($config->issueMinMinutes, $config->issueRounds);
    }

    /**
     * @param DateTimeImmutable[] $roundTimes plánované časy kol losování
     * @return array{eligibleFrom: DateTimeImmutable, validUntil: DateTimeImmutable}
     */
    public function issue(DateTimeImmutable $issuedAt, array $roundTimes): array {
        $eligibleFrom = $issuedAt->modify("+{$this->issueMinMinutes} minutes");
        $next = array_values(array_filter($this->sorted($roundTimes), fn(DateTimeImmutable $t) => $t >= $eligibleFrom));
        if (!$next) {
            // žádné další kolo – los nehraje nikde (platnost končí hned)
            return ['eligibleFrom' => $eligibleFrom, 'validUntil' => $eligibleFrom];
        }
        $last = $next[min($this->issueRounds, count($next)) - 1];
        return ['eligibleFrom' => $eligibleFrom, 'validUntil' => $last];
    }

    public function refresh(DateTimeImmutable $validUntil, DateTimeImmutable $eventAt, int $minutes): DateTimeImmutable {
        $candidate = $eventAt->modify("+{$minutes} minutes");
        return $candidate > $validUntil ? $candidate : $validUntil;
    }

    public function isEligible(DateTimeImmutable $eligibleFrom, DateTimeImmutable $validUntil, DateTimeImmutable $drawAt): bool {
        return $eligibleFrom <= $drawAt && $drawAt <= $validUntil;
    }

    /** Los ještě platí (lze prodloužit); po uplynutí platnosti se u vchodu vydá znovu. */
    public function isValidAt(DateTimeImmutable $validUntil, DateTimeImmutable $at): bool {
        return $at <= $validUntil;
    }

    /**
     * @param DateTimeImmutable[] $roundTimes
     * @return DateTimeImmutable[] kola, ve kterých los hraje
     */
    public function eligibleRounds(DateTimeImmutable $eligibleFrom, DateTimeImmutable $validUntil, array $roundTimes): array {
        return array_values(array_filter($this->sorted($roundTimes),
            fn(DateTimeImmutable $t) => $this->isEligible($eligibleFrom, $validUntil, $t)));
    }

    /** @return DateTimeImmutable[] */
    private function sorted(array $times): array {
        usort($times, fn($a, $b) => $a <=> $b);
        return $times;
    }
}
