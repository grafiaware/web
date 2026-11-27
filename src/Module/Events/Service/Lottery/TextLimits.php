<?php

namespace Events\Service\Lottery;

/**
 * D1 (#155): nejvyšší délky textů z administrace losování (ve znacích), podle sloupců v DB.
 * VARCHAR(n) = n znaků; TEXT (65 535 B) s rezervou pro 4bajtové znaky utf8mb4.
 * Kontroluje kontrolér před uložením (oprava chyby: dřív PDOException). Omezení přímo ve formulářích (maxlength)
 * patří do jednotného systému formulářů – balík Z15.
 */
final class TextLimits {

    /** lottery.name VARCHAR(200), description TEXT, rules_url VARCHAR(500) */
    public const LOTTERY = ['name' => 200, 'description' => 10000, 'rules_url' => 500];
    /** lottery_prize.name VARCHAR(200) */
    public const PRIZE = ['name' => 200];
    /** lottery_question.text VARCHAR(500), options TEXT */
    public const QUESTION = ['text' => 500, 'options' => 5000];

    private const LABELS = [
        'name' => 'Název', 'description' => 'Popis', 'rules_url' => 'Odkaz na pravidla',
        'text' => 'Otázka', 'options' => 'Možnosti',
    ];

    /**
     * Hláška pro první příliš dlouhé pole, jinak null. Délka po oříznutí mezer (tak, jak se ukládá).
     *
     * @param array<string, int> $limits
     * @param array<string, mixed> $data
     */
    public static function check(array $limits, array $data): ?string {
        foreach ($limits as $field => $max) {
            $len = mb_strlen(trim((string) ($data[$field] ?? '')));
            if ($len > $max) {
                $label = self::LABELS[$field] ?? $field;
                return "{$label} může mít nejvýš {$max} znaků (zadáno {$len}). Nic se neuložilo – zkraťte text a uložte znovu.";
            }
        }
        return null;
    }
}
