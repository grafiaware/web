<?php

namespace Build\Migration;

/**
 * Najde a seřadí SQL migrace v migrations/{target}/.
 */
final class MigrationScanner
{
    public function __construct(
        private readonly string $migrationsRoot,
    ) {
    }

    /**
     * @return list<MigrationDefinition>
     */
    public function scan(MigrationTarget $target): array
    {
        $dir = $this->migrationsRoot . DIRECTORY_SEPARATOR . $target->migrationsSubdir();
        if (!is_dir($dir)) {
            return [];
        }

        $files = glob($dir . DIRECTORY_SEPARATOR . '*.sql') ?: [];
        $definitions = [];
        foreach ($files as $path) {
            $filename = basename($path);
            if (!preg_match('/^(\d{14}_[A-Za-z0-9_\-]+)\.sql$/', $filename, $m)) {
                continue;
            }
            $definitions[] = new MigrationDefinition($m[1], $path, $filename);
        }

        usort(
            $definitions,
            static fn (MigrationDefinition $a, MigrationDefinition $b): int => strcmp($a->version, $b->version)
        );

        return $definitions;
    }

    public function getMigrationsRoot(): string
    {
        return $this->migrationsRoot;
    }
}
