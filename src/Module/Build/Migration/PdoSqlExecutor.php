<?php

namespace Build\Migration;

use PDO;

/**
 * Jednoduchý executor pro testy (SQLite) a přímé PDO.
 * Odstraní komentáře a spouští statementy oddělené středníkem.
 */
final class PdoSqlExecutor implements SqlExecutorInterface
{
    public function __construct(
        private readonly PDO $pdo,
    ) {
    }

    public function executeScript(string $sql): void
    {
        $sql = trim($sql);
        if ($sql === '') {
            return;
        }

        // Odstranění /* ... */ a -- řádkových komentářů (dostatečné pro naše migrační SQL)
        $sql = preg_replace('#/\*.*?\*/#s', '', $sql) ?? $sql;
        $lines = preg_split('/\R/', $sql) ?: [];
        $cleaned = [];
        foreach ($lines as $line) {
            $trimmed = ltrim($line);
            if (str_starts_with($trimmed, '--')) {
                continue;
            }
            $cleaned[] = $line;
        }
        $sql = implode("\n", $cleaned);

        $statements = array_filter(
            array_map('trim', explode(';', $sql)),
            static fn (string $s): bool => $s !== ''
        );

        foreach ($statements as $statement) {
            $this->pdo->exec($statement);
        }
    }
}
