<?php
declare(strict_types=1);

// Pokud potřebujete zobrazovat či logovat chyby PHP, změňte si příslušné nastavení PHP v klientské sekci ve správě domény v části Webserver » Nastavení PHP. 


 
use Application\WebAppFactory;

use Application\Api\ApiRegistrator;
use Pes\Router\Resource\ResourceRegistry;

use Container\AppContainerConfigurator;
use Pes\Container\Container;
use Pes\Container\AutowiringContainer;

use Pes\Application\Middleware\Selector;
use Application\SelectorItems;

use Pes\Http\Factory\EnvironmentFactory;
use Pes\Application\Middleware\UnprocessedRequestHandler;
use Pes\Application\Middleware\NoMatchedRouteRequestHandler;

use Pes\Http\ResponseSender;

$deploy = false;

if ($deploy) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);

    $deployEcho = static function (string $label, mixed $value = ''): void {
        echo '<p><strong>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . ':</strong> '
            . '<code>' . htmlspecialchars(is_bool($value) ? ($value ? 'true' : 'false') : (string) $value, ENT_QUOTES, 'UTF-8') . '</code></p>';
    };

    $deployCheckReadable = static function (string $path) use ($deployEcho): bool {
        $ok = is_readable($path);
        $deployEcho($path, $ok ? 'OK (readable)' : 'MISSING or not readable');
        return $ok;
    };

    echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Deploy diagnostics</title></head><body>';
    echo '<h2>Deploy diagnostics (pre-bootstrap)</h2>';

    $deployEcho('PHP version', PHP_VERSION . ' (composer.json requires ^8.4)');
    if (version_compare(PHP_VERSION, '8.4.0', '<')) {
        echo '<p style="color:red"><strong>Varování:</strong> PHP ' . PHP_VERSION
            . ' je pod požadovanou verzí 8.4 — projekt může padat už při parsování kódu.</p>';
    }
    $deployEcho('SAPI', PHP_SAPI);
    $deployEcho('__DIR__', __DIR__);
    $deployEcho('getcwd()', getcwd() ?: '(empty)');
    $deployEcho('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT'] ?? '(not set)');
    $deployEcho('SCRIPT_FILENAME', $_SERVER['SCRIPT_FILENAME'] ?? '(not set)');
    $deployEcho('HTTP_HOST', $_SERVER['HTTP_HOST'] ?? '(not set)');

    if (getcwd() !== __DIR__) {
        echo '<p style="color:darkorange"><strong>Varování:</strong> getcwd() se liší od __DIR__. '
            . 'pes-bootstrap hledá složku bootstrap/ přes getcwd(). Volám chdir(__DIR__)...</p>';
        chdir(__DIR__);
        $deployEcho('getcwd() po chdir', getcwd() ?: '(empty)');
    }

    register_shutdown_function(static function () use ($deployEcho): void {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
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
    $criticalFiles = [
        $projectRoot . '/vendor/pes/pes-bootstrap/src/BootstrapEntry.php',
        $projectRoot . '/vendor/pes/pes-bootstrap/bootstrap/Bootstrap.php',
        $projectRoot . '/vendor/autoload.php',
        $projectRoot . '/bootstrap/SetBootstrap.php',
        $projectRoot . '/bootstrap/RegisterAutoloader.php',
        $projectRoot . '/app/Site/ConfigurationCache.php',
        $projectRoot . '/app/Site/active-site.php',
        $projectRoot . '/composer.json',
    ];
    foreach ($criticalFiles as $path) {
        $deployCheckReadable($path);
    }

    echo '<h3>Vendor pes balíčky (symlink vs. kopie)</h3>';
    $pesVendorDir = $projectRoot . '/vendor/pes';
    if (is_dir($pesVendorDir)) {
        foreach (scandir($pesVendorDir) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $packagePath = $pesVendorDir . '/' . $entry;
            $linkInfo = is_link($packagePath)
                ? 'symlink → ' . (readlink($packagePath) ?: '(broken)')
                : (is_dir($packagePath) ? 'directory' : 'file');
            $bootstrapProbe = $packagePath . '/bootstrap/Bootstrap.php';
            $srcProbe = $packagePath . '/src';
            $deployEcho(
                'vendor/pes/' . $entry,
                $linkInfo
                    . '; bootstrap/Bootstrap.php: ' . (is_readable($bootstrapProbe) ? 'OK' : 'MISSING')
                    . '; src/: ' . (is_dir($srcProbe) ? 'OK' : 'MISSING')
            );
        }
    } else {
        $deployEcho('vendor/pes', 'MISSING');
    }

    $deployEcho('Stará cesta (oa24?)', is_readable($projectRoot . '/vendor/pes/pes/src/Bootstrap/Bootstrap.php') ? 'existuje — starý monolit' : 'neexistuje');
    $deployEcho('Nová cesta (pes-bootstrap)', is_readable($projectRoot . '/vendor/pes/pes-bootstrap/bootstrap/Bootstrap.php') ? 'existuje' : 'MISSING');

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
            $deployEcho('vendor/autoload.php', class_exists(\Composer\Autoload\ClassLoader::class) ? 'loaded OK' : 'loaded, ClassLoader missing');
        } catch (\Throwable $e) {
            $deployEcho('vendor/autoload.php ERROR', $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
        }
    }

    echo '<h3>ConfigurationCache (jen kontrola souborů, include až po bootstrapu)</h3>';
    $deployEcho('ConfigurationCache.php readable', is_readable($projectRoot . '/app/Site/ConfigurationCache.php') ? 'yes' : 'no');

    $siteName = is_string($activeSite ?? null) ? $activeSite : null;
    if ($siteName !== null) {
        $siteBootstrapFiles = [
            $projectRoot . '/app/Site/' . $siteName . '/ConfigurationBootstrap.php',
            $projectRoot . '/app/Site/' . $siteName . '/ConfigurationConstants.php',
        ];
        foreach ($siteBootstrapFiles as $path) {
            $deployCheckReadable($path);
        }
    }
}

define('PROJECT_PATH', str_replace("\\", "/", preg_replace('/^'.preg_quote($_SERVER['DOCUMENT_ROOT'], '/') . '/', '', __DIR__))."/");
//define('PROJECT_PATH', realpath(__DIR__ . '/..'));

$bootstrapEntryFile = __DIR__ . '/vendor/pes/pes-bootstrap/src/BootstrapEntry.php';
if (!is_readable($bootstrapEntryFile)) {
    if ($deploy) {
        http_response_code(500);
        echo '<h3 style="color:red">BootstrapEntry.php nenalezen</h3>';
        echo '<p>Zkontrolujte, zda na serveru existuje <code>vendor/pes/pes-bootstrap</code> (composer install).</p></body></html>';
        exit;
    }
    throw new RuntimeException('Missing bootstrap entry file: ' . $bootstrapEntryFile);
}
require_once $bootstrapEntryFile;

if ($deploy) {
    echo '<h2>BootstrapEntry::load()</h2>';
    try {
        Pes\Bootstrap\BootstrapEntry::load();
        echo '<p style="color:green"><strong>BootstrapEntry::load() dokončen bez výjimky.</strong></p>';
    } catch (\Throwable $e) {
        http_response_code(500);
        echo '<h3 style="color:red">BootstrapEntry::load() selhal</h3>';
        echo '<pre>' . htmlspecialchars(
            $e::class . ': ' . $e->getMessage() . "\n" . $e->getFile() . ':' . $e->getLine() . "\n\n" . $e->getTraceAsString(),
            ENT_QUOTES,
            'UTF-8'
        ) . '</pre></body></html>';
        exit;
    }
} else {
    Pes\Bootstrap\BootstrapEntry::load();
}

if($deploy) {
    echo '<h2>Stav po bootstrapu</h2>';
    error_log("Error log available!", 0);
    echo defined('PES_DEVELOPMENT') && PES_DEVELOPMENT ? "<p>PES_DEVELOPMENT: ano (chyby se zobrazí)</p>" : "<p>PES_DEVELOPMENT: ne (chyby se skrývají → typicky 500)</p>";
    echo defined('PES_PRODUCTION') && PES_PRODUCTION ? "<p>PES_PRODUCTION: ano</p>" : "<p>PES_PRODUCTION: ne</p>";
    echo PES_RUNNING_ON_PRODUCTION_HOST ? "<p>Production host: ano</p>" : "<p>Production host: ne — DB připojení může selhat!</p>";
    echo "<p>host name: ".gethostname()."</p>";
    echo '<p>PES_PRODUCTION_MACHINE_HOST_NAME: '.PES_PRODUCTION_MACHINE_HOST_NAME.'</p>';
    echo "<p>\$_SERVER['DOCUMENT_ROOT']: ".$_SERVER['DOCUMENT_ROOT']."</p>";
    echo "<p>PROJECT_PATH: ".constant('PROJECT_PATH')."</p>";
    echo "<p>PES_BOOTSTRAP_LOGS_BASE_PATH: ".constant('PES_BOOTSTRAP_LOGS_BASE_PATH')."</p>";
    echo '<p>Logs directory exists: '.(is_dir(PES_BOOTSTRAP_LOGS_BASE_PATH) ? 'yes' : 'no').'</p>';
    echo '<p>Logs directory writable: '.(is_dir(PES_BOOTSTRAP_LOGS_BASE_PATH) && is_writable(PES_BOOTSTRAP_LOGS_BASE_PATH) ? 'yes' : 'no').'</p>';
    if (class_exists(\Site\ConfigurationCache::class)) {
        echo '<p>ConfigurationCache::activeSiteName(): '.\Site\ConfigurationCache::activeSiteName().'</p>';
    }
}


if ($deploy) {
    try {
        $environment = (new EnvironmentFactory())->createFromGlobals();
        $app = (new WebAppFactory())->createFromEnvironment($environment);
        $appContainer =(new AppContainerConfigurator())->configure(new Container());
        $app->setAppContainer($appContainer);

        $selector = $appContainer->get(Selector::class);
        (new SelectorItems($app))->addItems($selector);

        $app->getAppContainer()->get(ApiRegistrator::class)->registerApi($app->getAppContainer()->get(ResourceRegistry::class));

        echo "<p>REQUEST_URI: {$environment->get('REQUEST_URI')}</p>";

        $noMatchHandler = $appContainer->get(NoMatchedRouteRequestHandler::class);
        $response = $app->run($selector, $noMatchHandler);

        echo "<p>Response status: {$response->getStatusCode()}</p>";
        echo '</body></html>';
        (new ResponseSender())->send($response);
    } catch (\Throwable $e) {
        http_response_code(500);
        echo '<h3 style="color:red">Chyba po bootstrapu (aplikace)</h3>';
        echo '<pre>' . htmlspecialchars(
            $e::class . ': ' . $e->getMessage() . "\n" . $e->getFile() . ':' . $e->getLine() . "\n\n" . $e->getTraceAsString(),
            ENT_QUOTES,
            'UTF-8'
        ) . '</pre></body></html>';
    }
    exit;
}

$environment = (new EnvironmentFactory())->createFromGlobals();
$app = (new WebAppFactory())->createFromEnvironment($environment);
$appContainer =(new AppContainerConfigurator())->configure(new Container());  //(new AppContainerConfigurator())->configure(new Container(new AutowiringContainer()));
$app->setAppContainer($appContainer);

$selector = $appContainer->get(Selector::class);
(new SelectorItems($app))->addItems($selector);

//TODO: ApiRegistrator do pes, volání ->registerApi do AppFactory - APPFactory musí dostat app kontejner do konstruktoru
// registrace api do ResourceRegistry, ResourceRegistry se zaregistrovaným api je dostupný v kontejneru aplikace
$app->getAppContainer()->get(ApiRegistrator::class)->registerApi($app->getAppContainer()->get(ResourceRegistry::class));

$noMatchHandler = $appContainer->get(NoMatchedRouteRequestHandler::class);
$response = $app->run($selector, $noMatchHandler);

(new ResponseSender())->send($response);
