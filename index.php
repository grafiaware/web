<?php
declare(strict_types=1);
define('PES_FORCE_DEVELOPMENT', 'force_development');
// nebo
//define('PES_FORCE_PRODUCTION', 'force_production');

define('PROJECT_DIR', preg_replace('/^' . preg_quote($_SERVER['DOCUMENT_ROOT'], '/') . '/', '', __DIR__));

include 'vendor/pes/pes/src/Bootstrap/Bootstrap.php';

use Application\WebAppFactory;
use Application\SelectorFactory;
use Pes\Container\AutowiringContainer;

use Pes\Http\Factory\EnvironmentFactory;
use Pes\Middleware\UnprocessedRequestHandler;  //NoMatchSelectorItemRequestHandler;
use Pes\Http\ResponseSender;

$environment = (new EnvironmentFactory())->createFromGlobals();
$app = (new WebAppFactory())->createFromEnvironment($environment);
$selector = (new SelectorFactory($app))->create();

$response = $app->run($selector, new UnprocessedRequestHandler());

(new ResponseSender())->send($response);
