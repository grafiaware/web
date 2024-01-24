<?php
declare(strict_types=1);
// Pokud potřebujete zobrazovat či logovat chyby PHP, změňte si příslušné nastavení PHP v klientské sekci ve správě domény v části Webserver » Nastavení PHP. 
error_reporting(E_ALL);
define('PROJECT_PATH', str_replace("\\", "/", preg_replace('/^'.preg_quote($_SERVER['DOCUMENT_ROOT'], '/') . '/', '', __DIR__))."/");
//define('PROJECT_PATH', realpath(__DIR__ . '/..'));
include 'vendor/pes/pes/src/Bootstrap/Bootstrap.php';
error_log("Error log available!", 0);
echo PES_RUNNING_ON_PRODUCTION_HOST ? "<p>Production host</p>" : "<p>No production host</p>";
echo "<p>host name: ".gethostname()."<p>";
echo "<p>\$_SERVER['DOCUMENT_ROOT']: ".$_SERVER['DOCUMENT_ROOT']."<p>";
echo "<p>PROJECT_PATH: ".constant('PROJECT_PATH')."<p>";
echo "<p>PES_BOOTSTRAP_LOGS_BASE_PATH: ".constant('PES_BOOTSTRAP_LOGS_BASE_PATH')."<p>";

use Application\WebAppFactory;

use Application\Api\ApiRegistrator;
use Pes\Router\Resource\ResourceRegistry;

use Container\AppContainerConfigurator;
use Pes\Container\Container;
use Pes\Container\AutowiringContainer;

use Pes\Middleware\Selector;
use Application\SelectorItems;

use Pes\Http\Factory\EnvironmentFactory;
use Pes\Middleware\UnprocessedRequestHandler;  //NoMatchSelectorItemRequestHandler;
use Pes\Http\ResponseSender;
$environment = (new EnvironmentFactory())->createFromGlobals();
$app = (new WebAppFactory())->createFromEnvironment($environment);
$appContainer =(new AppContainerConfigurator())->configure(new Container());  //(new AppContainerConfigurator())->configure(new Container(new AutowiringContainer()));
$app->setAppContainer($appContainer);

$selector = $appContainer->get(Selector::class);
(new SelectorItems($app))->addItems($selector);

//TODO: ApiRegistrator dp pes, volání ->registerApi do AppFactory - APPFactory musí dostat app kontejner do konstruktoru
// registrace api do ResourceRegistry, ResourceRegistry se zaregistrovaným api je dostupný v kontejneru aplikace
$app->getAppContainer()->get(ApiRegistrator::class)->registerApi($app->getAppContainer()->get(ResourceRegistry::class));
$response = $app->run($selector, new UnprocessedRequestHandler());
$a=1/0;

(new ResponseSender())->send($response);
