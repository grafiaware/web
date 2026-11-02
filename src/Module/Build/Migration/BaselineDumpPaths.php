<?php

namespace Build\Migration;

/**
 * Cesty k baseline dumpům 2025 (společný start schématu před migrate).
 */
final class BaselineDumpPaths
{
    public static function defaultRoot(?string $projectRoot = null): string
    {
        $root = $projectRoot ?? dirname(__DIR__, 4);

        return $root . DIRECTORY_SEPARATOR . 'docker'
            . DIRECTORY_SEPARATOR . 'migrations'
            . DIRECTORY_SEPARATOR . 'baseline'
            . DIRECTORY_SEPARATOR . ReleaseBaseline::PRODUCTION;
    }

    public static function dumpFile(MigrationTarget $target, ?string $projectRoot = null): string
    {
        return self::defaultRoot($projectRoot)
            . DIRECTORY_SEPARATOR . $target->value . '.sql';
    }

    public static function assertReadable(MigrationTarget $target, ?string $projectRoot = null): string
    {
        $path = self::dumpFile($target, $projectRoot);
        if (!is_readable($path)) {
            throw new \RuntimeException(
                "Chybí baseline dump pro {$target->value}: {$path}"
            );
        }

        return $path;
    }
}
