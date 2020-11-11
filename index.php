<?php
declare(strict_types=1);
define('PES_FORCE_DEVELOPMENT', 'force_development');
// nebo
//define('PES_FORCE_PRODUCTION', 'force_production');

define('PROJECT_PATH', str_replace("\\", "/", preg_replace('/^' . preg_quote($_SERVER['DOCUMENT_ROOT'], '/') . '/', '', __DIR__))."/");

include 'vendor/pes/pes/src/Bootstrap/Bootstrap.php';

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

//echo "<pre>".print_r($bootstrapLoggerArray, true)."</pre>";

$environment = (new EnvironmentFactory())->createFromGlobals();
$app = (new WebAppFactory())->createFromEnvironment($environment);
$appContainer =(new AppContainerConfigurator())->configure(new Container());  //(new AppContainerConfigurator())->configure(new Container(new AutowiringContainer()));
$app->setAppContainer($appContainer);

$selector = $appContainer->get(Selector::class);
(new SelectorItems($app))->addItems($selector);

// registrace api do ResourceRegistry, ResourceRegistry se zaregistrovaným api je dostupný v kontejneru aplikace
$app->getAppContainer()->get(ApiRegistrator::class)->registerApi($app->getAppContainer()->get(ResourceRegistry::class));
$response = $app->run($selector, new UnprocessedRequestHandler());

(new ResponseSender())->send($response);
