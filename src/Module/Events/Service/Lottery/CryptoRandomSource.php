<?php

namespace Events\Service\Lottery;

/**
 * D1: kryptograficky bezpečný zdroj náhody (random_int).
 */
final class CryptoRandomSource implements RandomSourceInterface {

    public function int(int $min, int $max): int {
        return random_int($min, $max);
    }
}
