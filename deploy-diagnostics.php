<?php
/**
 * Deploy diagnostika — syntaxe kompatibilní s PHP 7.0+ (musí běžet i na starším PHP než projekt).
 * Voláno z index.php: if ($deploy) { require __DIR__ . '/deploy-diagnostics.php'; exit; }
 *
 * Očekává definované PROJECT_PATH v index.php.
 */

use Application\Api\ApiRegistrator;
use Application\SelectorItems;
use Application\WebAppFactory;
use Container\AppContainerConfigurator;
use Pes\Application\Middleware\NoMatchedRouteRequestHandler;
use Pes\Application\Middleware\Selector;
use Pes\Bootstrap\BootstrapEntry;
use Pes\Container\Container;
use Pes\Http\Factory\EnvironmentFactory;
use Pes\Http\ResponseSender;
use Pes\Router\Resource\ResourceRegistry;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$deployEcho = function ($label, $value = '') {
    echo '<p><strong>' . htmlspecialchars((string) $label, ENT_QUOTES, 'UTF-8') . ':</strong> '
        . '<code>' . htmlspecialchars(is_bool($value) ? ($value ? 'true' : 'false') : (string) $value, ENT_QUOTES, 'UTF-8') . '</code></p>';
};

$deployCheckReadable = function ($path) use ($deployEcho) {
    $ok = is_readable($path);
    $deployEcho($path, $ok ? 'OK (readable)' : 'MISSING or not readable');
    return $ok;
};

$deployFormatException = function ($e) {
    return get_class($e) . ': ' . $e->getMessage() . "\n" . $e->getFile() . ':' . $e->getLine() . "\n\n" . $e->getTraceAsString();
};

echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Deploy diagnostics</title></head><body>';
echo '<h2>Deploy diagnostics (pre-bootstrap)</h2>';

$deployEcho('PHP version', PHP_VERSION . ' (composer.json requires ^8.4)');
if (version_compare(PHP_VERSION, '8.4.0', '<')) {
    echo '<p style="color:red"><strong>Varování:</strong> PHP ' . htmlspecialchars(PHP_VERSION, ENT_QUOTES, 'UTF-8')
        . ' je pod požadovanou verzí 8.4 — aplikace na tomto PHP nenaběhne.</p>';
} else {
    echo '<p style="color:green"><strong>OK:</strong> PHP splňuje minimální požadavek 8.4.</p>';
}
$deployEcho('SAPI', PHP_SAPI);
$deployEcho('__DIR__', __DIR__);
$deployEcho('getcwd()', getcwd() ? getcwd() : '(empty)');
$deployEcho('DOCUMENT_ROOT', isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : '(not set)');
$deployEcho('SCRIPT_FILENAME', isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '(not set)');
$deployEcho('HTTP_HOST', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '(not set)');

if (getcwd() !== __DIR__) {
    echo '<p style="color:darkorange"><strong>Varování:</strong> getcwd() se liší od __DIR__. '
        . 'pes-bootstrap hledá složku bootstrap/ přes getcwd(). Volám chdir(__DIR__)...</p>';
    chdir(__DIR__);
    $deployEcho('getcwd() po chdir', getcwd() ? getcwd() : '(empty)');
}

register_shutdown_function(function () use ($deployEcho) {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR), true)) {
        echo '<h3 style="color:red">Fatal error (shutdown handler)</h3>';
        $deployEcho('type', (string) $error['type']);
        $deployEcho('message', $error['message']);
        $deployEcho('file', $error['file'] . ':' . $error['line']);
    }
});

$projectRoot = __DIR__;
$bootstrapSettingsPath = 'bootstrap/';
$deployEcho('Očekávaná cesta SetBootstrap.php', $projectRoot . '/' . $bootstrapSettingsPath . 'SetBootstrap.php');
$deployEcho('Očekávaná cesta RegisterAutoloader.php', $projectRoot . '/' . $bootstrapSettingsPath . 'RegisterAutoloader.php');

echo '<h3>Kritické soubory</h3>';
$criticalFiles = array(
    $projectRoot . '/vendor/pes/pes-bootstrap/src/BootstrapEntry.php',
    $projectRoot . '/vendor/pes/pes-bootstrap/bootstrap/Bootstrap.php',
    $projectRoot . '/vendor/autoload.php',
    $projectRoot . '/bootstrap/SetBootstrap.php',
    $projectRoot . '/bootstrap/RegisterAutoloader.php',
    $projectRoot . '/app/Site/ConfigurationCache.php',
    $projectRoot . '/app/Site/active-site.php',
);
foreach ($criticalFiles as $path) {
    $deployCheckReadable($path);
}

echo '<h3>Vendor pes balíčky</h3>';
echo '<p>Obsah <code>vendor/pes/</code>. Procedurální bootstrap (<code>bootstrap/Bootstrap.php</code>) existuje '
    . 'pouze v balíčku <code>pes-bootstrap</code>. U ostatních balíčků (pes-core, pes-http, …) je to normální — mají jen <code>src/</code>.</p>';
$pesVendorDir = $projectRoot . '/vendor/pes';
if (is_dir($pesVendorDir)) {
    $entries = scandir($pesVendorDir);
    if ($entries === false) {
        $entries = array();
    }
    foreach ($entries as $entry) {
        if ($entry === '.' || $entry === '..') {
            continue;
        }
        $packagePath = $pesVendorDir . '/' . $entry;
        if (is_link($packagePath)) {
            $target = readlink($packagePath);
            $linkInfo = 'symlink → ' . ($target !== false ? $target : '(broken)');
        } elseif (is_dir($packagePath)) {
            $linkInfo = 'directory';
        } else {
            $linkInfo = 'file';
        }
        $srcProbe = $packagePath . '/src';
        $srcStatus = is_dir($srcProbe) ? 'OK' : 'MISSING';
        if ($entry === 'pes-bootstrap') {
            $bootstrapProbe = $packagePath . '/bootstrap/Bootstrap.php';
            $bootstrapStatus = is_readable($bootstrapProbe)
                ? 'OK'
                : 'nenalezen — BootstrapEntry::load() nebude fungovat (zkontrolujte nahrání vendor/pes/pes-bootstrap)';
            $deployEcho(
                'vendor/pes/' . $entry,
                $linkInfo . '; bootstrap/Bootstrap.php: ' . $bootstrapStatus . '; src/: ' . $srcStatus
            );
        } else {
            $deployEcho(
                'vendor/pes/' . $entry,
                $linkInfo . '; src/: ' . $srcStatus
            );
        }
    }
} else {
    $deployEcho('vendor/pes', 'MISSING — složka vendor/pes neexistuje');
}

$activeSite = null;
$activeSiteFile = $projectRoot . '/app/Site/active-site.php';
if (is_readable($activeSiteFile)) {
    $activeSite = include $activeSiteFile;
    $deployEcho('active-site.php vrací', is_string($activeSite) ? $activeSite : gettype($activeSite));
}

echo '<h3>Composer autoload probe</h3>';
$autoloadFile = $projectRoot . '/vendor/autoload.php';
if (is_readable($autoloadFile)) {
    try {
        require_once $autoloadFile;
        $deployEcho('vendor/autoload.php', class_exists('Composer\\Autoload\\ClassLoader') ? 'loaded OK' : 'loaded, ClassLoader missing');
    } catch (Throwable $e) {
        $deployEcho('vendor/autoload.php ERROR', $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    }
}

echo '<h3>ConfigurationCache (jen kontrola souborů, include až po bootstrapu)</h3>';
$deployEcho('ConfigurationCache.php readable', is_readable($projectRoot . '/app/Site/ConfigurationCache.php') ? 'yes' : 'no');

if (is_string($activeSite)) {
    $siteBootstrapFiles = array(
        $projectRoot . '/app/Site/' . $activeSite . '/ConfigurationBootstrap.php',
        $projectRoot . '/app/Site/' . $activeSite . '/ConfigurationConstants.php',
    );
    foreach ($siteBootstrapFiles as $path) {
        $deployCheckReadable($path);
    }
}

$bootstrapEntryFile = $projectRoot . '/vendor/pes/pes-bootstrap/src/BootstrapEntry.php';
if (!is_readable($bootstrapEntryFile)) {
    http_response_code(500);
    echo '<h3 style="color:red">BootstrapEntry.php nenalezen</h3>';
    echo '<p>Zkontrolujte, zda na serveru existuje <code>vendor/pes/pes-bootstrap</code> (composer install).</p></body></html>';
    exit;
}

echo '<h2>BootstrapEntry::load()</h2>';
if (version_compare(PHP_VERSION, '8.4.0', '<')) {
    echo '<p style="color:darkorange"><strong>Přeskočeno:</strong> BootstrapEntry a aplikace vyžadují PHP 8.4+. '
        . 'Po přepnutí PHP na hostingu obnovte stránku.</p></body></html>';
    exit;
}

require_once $bootstrapEntryFile;

try {
    BootstrapEntry::load();
    echo '<p style="color:green"><strong>BootstrapEntry::load() dokončen bez výjimky.</strong></p>';
} catch (Throwable $e) {
    http_response_code(500);
    echo '<h3 style="color:red">BootstrapEntry::load() selhal</h3>';
    echo '<pre>' . htmlspecialchars($deployFormatException($e), ENT_QUOTES, 'UTF-8') . '</pre></body></html>';
    exit;
}

echo '<h2>Stav po bootstrapu</h2>';
error_log('Error log available!', 0);
echo (defined('PES_DEVELOPMENT') && PES_DEVELOPMENT) ? '<p>PES_DEVELOPMENT: ano (chyby se zobrazí)</p>' : '<p>PES_DEVELOPMENT: ne (chyby se skrývají → typicky 500)</p>';
echo (defined('PES_PRODUCTION') && PES_PRODUCTION) ? '<p>PES_PRODUCTION: ano</p>' : '<p>PES_PRODUCTION: ne</p>';
echo PES_RUNNING_ON_PRODUCTION_HOST ? '<p>Production host: ano</p>' : '<p>Production host: ne — DB připojení může selhat!</p>';
echo '<p>host name: ' . htmlspecialchars(gethostname(), ENT_QUOTES, 'UTF-8') . '</p>';
echo '<p>PES_PRODUCTION_MACHINE_HOST_NAME: ' . htmlspecialchars(PES_PRODUCTION_MACHINE_HOST_NAME, ENT_QUOTES, 'UTF-8') . '</p>';
echo '<p>$_SERVER[\'DOCUMENT_ROOT\']: ' . htmlspecialchars($_SERVER['DOCUMENT_ROOT'], ENT_QUOTES, 'UTF-8') . '</p>';
echo '<p>PROJECT_PATH: ' . htmlspecialchars(constant('PROJECT_PATH'), ENT_QUOTES, 'UTF-8') . '</p>';
echo '<p>PES_BOOTSTRAP_LOGS_BASE_PATH: ' . htmlspecialchars(constant('PES_BOOTSTRAP_LOGS_BASE_PATH'), ENT_QUOTES, 'UTF-8') . '</p>';
echo '<p>Logs directory exists: ' . (is_dir(PES_BOOTSTRAP_LOGS_BASE_PATH) ? 'yes' : 'no') . '</p>';
echo '<p>Logs directory writable: ' . (is_dir(PES_BOOTSTRAP_LOGS_BASE_PATH) && is_writable(PES_BOOTSTRAP_LOGS_BASE_PATH) ? 'yes' : 'no') . '</p>';
if (class_exists('Site\\ConfigurationCache')) {
    echo '<p>ConfigurationCache::activeSiteName(): ' . htmlspecialchars(\Site\ConfigurationCache::activeSiteName(), ENT_QUOTES, 'UTF-8') . '</p>';
}

try {
    $environment = (new EnvironmentFactory())->createFromGlobals();
    $app = (new WebAppFactory())->createFromEnvironment($environment);
    $appContainer = (new AppContainerConfigurator())->configure(new Container());
    $app->setAppContainer($appContainer);

    $selector = $appContainer->get(Selector::class);
    (new SelectorItems($app))->addItems($selector);

    $app->getAppContainer()->get(ApiRegistrator::class)->registerApi($app->getAppContainer()->get(ResourceRegistry::class));

    echo '<p>REQUEST_URI: ' . htmlspecialchars($environment->get('REQUEST_URI'), ENT_QUOTES, 'UTF-8') . '</p>';

    $noMatchHandler = $appContainer->get(NoMatchedRouteRequestHandler::class);
    $response = $app->run($selector, $noMatchHandler);

    echo '<p>Response status: ' . (int) $response->getStatusCode() . '</p>';
    echo '</body></html>';
    (new ResponseSender())->send($response);
} catch (Throwable $e) {
    http_response_code(500);
    echo '<h3 style="color:red">Chyba po bootstrapu (aplikace)</h3>';
    echo '<pre>' . htmlspecialchars($deployFormatException($e), ENT_QUOTES, 'UTF-8') . '</pre></body></html>';
}
