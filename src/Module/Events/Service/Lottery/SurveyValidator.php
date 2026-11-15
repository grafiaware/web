<?php

namespace Events\Service\Lottery;

/**
 * D1: kontrola odpovědí ankety losování. Povinné otázky jsou podmínkou zapojení.
 *
 * Otázka: ['id', 'text', 'type' => single|multi|text, 'options' => "možnost na řádek", 'required' => 0|1].
 * Odpověď v DB: single – text zvolené možnosti, multi – zvolené možnosti oddělené novým řádkem, text – oříznutý text.
 */
final class SurveyValidator {

    public const TYPE_SINGLE = 'single';
    public const TYPE_MULTI = 'multi';
    public const TYPE_TEXT = 'text';
    public const TYPES = [self::TYPE_SINGLE => 'jedna možnost', self::TYPE_MULTI => 'více možností', self::TYPE_TEXT => 'text'];
    public const MAX_TEXT = 2000;

    /** @return string[] */
    public static function options(?string $options): array {
        return array_values(array_filter(array_map('trim', preg_split('/\R/u', (string) $options)), fn($o) => $o !== ''));
    }

    /**
     * @param array<int, array> $questions
     * @param array<int|string, mixed> $input id otázky => hodnota (řetězec, u multi pole)
     * @return array{answers: array<int, string>, errors: array<int, string>}
     */
    public function validate(array $questions, array $input): array {
        $answers = [];
        $errors = [];
        foreach ($questions as $q) {
            $id = (int) $q['id'];
            $raw = $input[$id] ?? ($input[(string) $id] ?? null);
            $value = '';
            switch ($q['type']) {
                case self::TYPE_MULTI:
                    $allowed = self::options($q['options'] ?? '');
                    $chosen = array_values(array_intersect($allowed, array_map('strval', is_array($raw) ? $raw : ($raw === null ? [] : [$raw]))));
                    $value = implode("\n", $chosen);
                    break;
                case self::TYPE_SINGLE:
                    $allowed = self::options($q['options'] ?? '');
                    $raw = is_array($raw) ? null : $raw;
                    $value = in_array((string) $raw, $allowed, true) ? (string) $raw : '';
                    break;
                default:
                    $value = is_array($raw) ? '' : mb_substr(trim((string) $raw), 0, self::MAX_TEXT);
            }
            if ($value === '' && (int) ($q['required'] ?? 0) === 1) {
                $errors[$id] = 'Odpovězte prosím na otázku „' . $q['text'] . '“.';
            }
            $answers[$id] = $value;
        }
        return ['answers' => $answers, 'errors' => $errors];
    }
}
