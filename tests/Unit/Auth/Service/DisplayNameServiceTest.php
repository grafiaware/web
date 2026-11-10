<?php
declare(strict_types=1);

namespace Test\Unit\Auth\Service;

use Auth\Service\DisplayNameService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * A8: normalizace osobního jména (docs/vp/zadani/A8.md).
 */
final class DisplayNameServiceTest extends TestCase
{
    public static function names(): array
    {
        return [
            'běžné jméno' => ['Jan Novák', 'Jan Novák'],
            'okrajové mezery' => ['  Jana   Nováková ', 'Jana Nováková'],
            'tabulátor a nový řádek' => ["Jan\t\nNovák", 'Jan Novák'],
            'řídicí znaky' => ["Jan\x00\x07Novák", 'Jan Novák'],
            'neviditelné znaky' => ["Jan\u{200B}Novák", 'Jan Novák'],
            'prázdné' => ['', null],
            'jen mezery' => ['   ', null],
            'null' => [null, null],
            'HTML zůstane textem' => ['<b>x</b>', '<b>x</b>'],
        ];
    }

    #[DataProvider('names')]
    public function testNormalize(?string $input, ?string $expected): void
    {
        $this->assertSame($expected, DisplayNameService::normalize($input));
    }

    public function testNormalizeCutsToMaxLength(): void
    {
        $name = DisplayNameService::normalize(str_repeat('ř', 150));
        $this->assertSame(DisplayNameService::MAX_LENGTH, mb_strlen($name));
    }

    public function testNormalizeInvalidUtf8(): void
    {
        $name = DisplayNameService::normalize("Jan \xC3\x28 Novák");
        $this->assertNotNull($name);
        $this->assertTrue(mb_check_encoding($name, 'UTF-8'));
    }
}
