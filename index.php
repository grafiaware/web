<?php
declare(strict_types=1);
define('PES_FORCE_DEVELOPMENT', 'force_development');
// nebo
//define('PES_FORCE_PRODUCTION', 'force_production');

define('PROJECT_PATH', str_replace("\\", "/", preg_replace('/^' . preg_quote($_SERVER['DOCUMENT_ROOT'], '/') . '/', '', __DIR__))."/");

include 'vendor/pes/pes/src/Bootstrap/Bootstrap.php';

use Application\WebAppFactory;
use Application\SelectorFactory;
use Application\Api\ApiRegistrator;
use Pes\Router\Resource\ResourceRegistry;

use Pes\Container\AutowiringContainer;

use Pes\Http\Factory\EnvironmentFactory;
use Pes\Middleware\UnprocessedRequestHandler;  //NoMatchSelectorItemRequestHandler;
use Pes\Http\ResponseSender;

$environment = (new EnvironmentFactory())->createFromGlobals();

$app = (new WebAppFactory())->createFromEnvironment($environment);

// middleware selector
$selector = (new SelectorFactory($app))->create();
// registrace api do ResourceRegistry, ResourceRegistry se zaregistrovaným api je dostupný v kontejneru aplikace
$app->getAppContainer()->get(ApiRegistrator::class)->registerApi($app->getAppContainer()->get(ResourceRegistry::class));
$response = $app->run($selector, new UnprocessedRequestHandler());

(new ResponseSender())->send($response);
