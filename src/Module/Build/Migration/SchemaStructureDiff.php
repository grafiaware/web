<?php

declare(strict_types=1);

namespace Build\Migration;

/**
 * Výsledek porovnání struktury live DB vs baseline dump.
 */
final class SchemaStructureDiff
{
    /**
     * @param list<string> $lines lidské diff řádky
     */
    public function __construct(
        private readonly array $lines = [],
    ) {
    }

    public function isEmpty(): bool
    {
        return $this->lines === [];
    }

    /**
     * @return list<string>
     */
    public function lines(): array
    {
        return $this->lines;
    }

    public function format(string $target, string $dumpPath): string
    {
        $header = "Struktura DB [{$target}] se liší od baseline dumpu.\n"
            . "Dump: {$dumpPath}\n"
            . "Opravte DB (např. php scripts/db-rebuild.php --target={$target} --yes) "
            . "než spustíte první zápis do schema_migrations.\n"
            . "Rozdíly:";

        return $header . "\n- " . implode("\n- ", $this->lines);
    }
}
