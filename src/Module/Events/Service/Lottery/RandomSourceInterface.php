<?php

namespace Events\Service\Lottery;

/**
 * D1: zdroj náhody pro losování a kódy (v testech deterministický).
 */
interface RandomSourceInterface {

    /** Náhodné celé číslo z intervalu <$min, $max>. */
    public function int(int $min, int $max): int;
}
