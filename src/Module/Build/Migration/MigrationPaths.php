<?php

namespace Build\Migration;

/**
 * Cesty k adresáři migrations/ v kořeni repository.
 */
final class MigrationPaths
{
    public static function defaultRoot(?string $projectRoot = null): string
    {
        $root = $projectRoot ?? dirname(__DIR__, 4);

        return $root . DIRECTORY_SEPARATOR . 'migrations';
    }
}
