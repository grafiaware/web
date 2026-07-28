<?php
declare(strict_types=1);

namespace Test\Support;

/**
 * Cesty pro logy PHPUnit testů.
 *
 * Base path je {projectRoot}/tests/, relativní cesty FileLoggeru (Logs/Mail, Logs/Events, …)
 * tak směřují do tests/Logs/….
 */
final class TestLogPaths
{
    public static function projectRoot(): string
    {
        return str_replace('\\', '/', dirname(__DIR__, 2));
    }

    /**
     * Absolutní base path pro FileLogger (končí lomítkem).
     * Relativní Logs/... se zapisují jako tests/Logs/...
     */
    public static function logsBasePath(): string
    {
        return rtrim(self::projectRoot(), '/') . '/tests/';
    }
}
