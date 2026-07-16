<?php
declare(strict_types=1);

namespace Test\Support;

use Pes\Bootstrap\BootstrapEntry;

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

        $projectRoot = dirname(__DIR__, 2);
        chdir($projectRoot);

        if (!isset($_SERVER['DOCUMENT_ROOT']) || $_SERVER['DOCUMENT_ROOT'] === '') {
            $_SERVER['DOCUMENT_ROOT'] = $projectRoot;
        }

        if (!defined('PROJECT_PATH')) {
            define('PROJECT_PATH', str_replace('\\', '/', $projectRoot) . '/');
        }

        if (!defined('PES_BOOTSTRAP_SETINGS_PATH')) {
            define('PES_BOOTSTRAP_SETINGS_PATH', 'tests/bootstrap/');
        }

        if (!defined('PES_DEVELOPMENT') && !defined('PES_PRODUCTION')) {
            BootstrapEntry::load();
        } else {
            require_once $projectRoot . '/vendor/autoload.php';
        }

        self::$loaded = true;
    }
}
