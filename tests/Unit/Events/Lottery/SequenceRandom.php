<?php
declare(strict_types=1);

namespace Test\Unit\Events\Lottery;

use Events\Service\Lottery\RandomSourceInterface;

/** Deterministický zdroj náhody pro testy: vrací zadaná čísla dokola (oříznutá do intervalu). */
final class SequenceRandom implements RandomSourceInterface {

    private int $i = 0;

    public function __construct(private array $values) {
    }

    public function int(int $min, int $max): int {
        $v = $this->values[$this->i++ % count($this->values)];
        return max($min, min($max, $v));
    }
}
