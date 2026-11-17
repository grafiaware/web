<?php

namespace Events\Service\Lottery;

/**
 * D1: výběr jednoho účastníka s vahou podle počtu losů.
 */
final class WeightedDraw {

    public function __construct(private RandomSourceInterface $random) {
    }

    /**
     * @param array<string, int> $weights klíč účastníka => počet losů (> 0)
     * @return string|null klíč vylosovaného účastníka, null když není z koho losovat
     */
    public function pick(array $weights): ?string {
        $weights = array_filter($weights, fn($w) => (int) $w > 0);
        if (!$weights) {
            return null;
        }
        ksort($weights, SORT_STRING);   // pořadí nezávislé na pořadí z DB
        $total = array_sum(array_map('intval', $weights));
        $r = $this->random->int(1, $total);
        foreach ($weights as $key => $w) {
            $r -= (int) $w;
            if ($r <= 0) {
                return (string) $key;
            }
        }
        return (string) array_key_last($weights);
    }
}
