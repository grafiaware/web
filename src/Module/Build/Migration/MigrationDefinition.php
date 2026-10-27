<?php

namespace Build\Migration;

/**
 * Jedna verzovaná SQL migrace (soubor).
 */
final class MigrationDefinition
{
    public function __construct(
        public readonly string $version,
        public readonly string $path,
        public readonly string $filename,
    ) {
    }

    public function readSql(): string
    {
        $sql = file_get_contents($this->path);
        if ($sql === false) {
            throw new \RuntimeException("Nelze načíst migraci: {$this->path}");
        }

        return $sql;
    }
}
