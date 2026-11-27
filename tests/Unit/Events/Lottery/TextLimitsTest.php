<?php
declare(strict_types=1);

namespace Test\Unit\Events\Lottery;

use Events\Service\Lottery\TextLimits;
use PHPUnit\Framework\TestCase;

/**
 * D1 (#155): délky textů z administrace losování.
 */
final class TextLimitsTest extends TestCase {

    public function testWithinLimitPasses(): void {
        $this->assertNull(TextLimits::check(TextLimits::QUESTION, ['text' => str_repeat('a', 500), 'options' => "ano\nne"]));
        $this->assertNull(TextLimits::check(TextLimits::LOTTERY, ['name' => 'Losování', 'description' => null]));
        $this->assertNull(TextLimits::check(TextLimits::PRIZE, []));
    }

    public function testCountsCharactersNotBytes(): void {
        $this->assertNull(TextLimits::check(TextLimits::QUESTION, ['text' => str_repeat('ž', 500)]));
    }

    public function testTrimmedLikeSaved(): void {
        $this->assertNull(TextLimits::check(TextLimits::PRIZE, ['name' => '  ' . str_repeat('a', 200) . "\n"]));
    }

    public function testTooLongReturnsCzechMessage(): void {
        $msg = TextLimits::check(TextLimits::QUESTION, ['text' => str_repeat('ř', 501)]);
        $this->assertSame('Otázka může mít nejvýš 500 znaků (zadáno 501). Nic se neuložilo – zkraťte text a uložte znovu.', $msg);
    }

    public function testFirstTooLongFieldReported(): void {
        $msg = TextLimits::check(TextLimits::LOTTERY, ['name' => str_repeat('a', 201), 'rules_url' => str_repeat('b', 501)]);
        $this->assertStringStartsWith('Název může mít nejvýš 200 znaků', (string) $msg);
        $msg = TextLimits::check(TextLimits::LOTTERY, ['name' => 'ok', 'rules_url' => str_repeat('b', 501)]);
        $this->assertStringStartsWith('Odkaz na pravidla může mít nejvýš 500 znaků', (string) $msg);
    }
}
