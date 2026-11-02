<?php
declare(strict_types=1);

namespace Test\Unit\Auth\Service;

use Auth\Service\LoginNameGenerator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class LoginNameGeneratorTest extends TestCase
{
    private function generator(array $existing = []): LoginNameGenerator
    {
        $lower = array_map('strtolower', $existing);
        return new LoginNameGenerator(fn(string $name) => in_array(strtolower($name), $lower, true));
    }

    #[DataProvider('baseProvider')]
    public function testBaseFromEmail(string $email, string $expected): void
    {
        $this->assertSame($expected, LoginNameGenerator::baseFromEmail($email));
    }

    public static function baseProvider(): array
    {
        return [
            'tečka' => ['jan.novak@example.test', 'jan_novak'],
            'velká písmena a mezery' => ['  Jan.Novak@Example.TEST ', 'jan_novak'],
            'diakritika' => ['jiří.šťastný@example.test', 'jiri_stastny'],
            'plus a pomlčka' => ['petr+vp-2027@example.test', 'petr_vp_2027'],
            'více oddělovačů' => ['a..b__c--d@example.test', 'a_b_c_d'],
            'okraje' => ['_.jana._@example.test', 'jana'],
            'jen znaky' => ['...@example.test', 'ucastnik'],
            'bez zavináče' => ['marie', 'marie'],
            'dlouhé' => [str_repeat('a', 80) . '@example.test', str_repeat('a', 46)],
        ];
    }

    public function testUniqueName(): void
    {
        $this->assertSame('jan_novak', $this->generator()->generate('jan.novak@example.test'));
    }

    public function testSuffixOnCollisionCaseInsensitive(): void
    {
        $this->assertSame('jan_novak2', $this->generator(['Jan_Novak'])->generate('jan.novak@example.test'));
        $this->assertSame('jan_novak3', $this->generator(['jan_novak', 'jan_novak2'])->generate('jan.novak@example.test'));
    }

    public function testShortNamesArePaddedWithNumber(): void
    {
        $this->assertSame('jan01', $this->generator()->generate('jan@example.test'));
        $this->assertSame('jan02', $this->generator(['jan01'])->generate('jan@example.test'));
        $this->assertSame('ab001', $this->generator()->generate('ab@example.test'));
        $this->assertSame('x0001', $this->generator()->generate('x@example.test'));
    }

    public function testLongNameWithSuffixFitsMaxLength(): void
    {
        $base = str_repeat('a', 46);
        $existing = [$base];
        for ($i = 2; $i <= 10; $i++) {
            $existing[] = $base . $i;
        }
        $name = $this->generator($existing)->generate(str_repeat('a', 60) . '@example.test');
        $this->assertSame($base . '11', $name);
        $this->assertLessThanOrEqual(LoginNameGenerator::MAX_LENGTH, strlen($name));
    }

    public function testResultMatchesLoginPatternAndHasNoAt(): void
    {
        foreach (['jan.novak@example.test', 'ž@example.test', '@example.test', 'Ödön+test@example.test'] as $email) {
            $name = $this->generator()->generate($email);
            $this->assertMatchesRegularExpression('/^\w{5,50}$/', $name, $email);
            $this->assertStringNotContainsString('@', $name);
        }
    }
}
