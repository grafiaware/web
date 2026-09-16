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

define('PROJECT_PATH', str_replace("\\", "/", preg_replace('/^'.preg_quote($_SERVER['DOCUMENT_ROOT'], '/') . '/', '', __DIR__))."/");
//define('PROJECT_PATH', realpath(__DIR__ . '/..'));

if ($deploy) {
    require __DIR__ . '/deploy-diagnostics.php';
    exit;
}

$bootstrapEntryFile = __DIR__ . '/vendor/pes/pes-bootstrap/src/BootstrapEntry.php';
if (!is_readable($bootstrapEntryFile)) {
    throw new RuntimeException('Missing bootstrap entry file: ' . $bootstrapEntryFile);
}
require_once $bootstrapEntryFile;
Pes\Bootstrap\BootstrapEntry::load();

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
