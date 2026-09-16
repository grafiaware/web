<?php
declare(strict_types=1);

namespace Test\Support;

use Pes\Bootstrap\BootstrapEntry;
use Pes\Logger\FileLogger;

/**
 * Sdileny bootstrap pro PHPUnit testy.
 *
 * Nahradi drivejsi include vendor/pes/pes/src/Bootstrap/Bootstrap.php z AppRunner.
 */
final class ApplicationBootstrap
{
    private static bool $loaded = false;

    public static function load(): void
    {
        if (self::$loaded) {
            return;
        }

        $projectRoot = TestLogPaths::projectRoot();
        chdir($projectRoot);

        if (!isset($_SERVER['DOCUMENT_ROOT']) || $_SERVER['DOCUMENT_ROOT'] === '') {
            $_SERVER['DOCUMENT_ROOT'] = $projectRoot;
        }

        if (!defined('PROJECT_PATH')) {
            define('PROJECT_PATH', rtrim(str_replace('\\', '/', $projectRoot), '/') . '/');
        }

        if (!defined('PES_BOOTSTRAP_SETINGS_PATH')) {
            define('PES_BOOTSTRAP_SETINGS_PATH', 'tests/bootstrap/');
        }

        if (!defined('PES_DEVELOPMENT') && !defined('PES_PRODUCTION')) {
            BootstrapEntry::load();
        } else {
            require_once $projectRoot . '/vendor/autoload.php';
        }

        FileLogger::setBaseLogsDirectory(TestLogPaths::logsBasePath());

        self::$loaded = true;
    }
}
