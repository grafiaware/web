<?php
declare(strict_types=1);

define('PROJECT_PATH', str_replace("\\", "/", preg_replace('/^' . preg_quote($_SERVER['DOCUMENT_ROOT'], '/') . '/', '', __DIR__))."/");

include 'vendor/pes/pes/src/Bootstrap/Bootstrap.php';

use Application\WebAppFactory;

use Container\AppContainerConfigurator;
use Pes\Container\Container;
use Pes\Container\AutowiringContainer;

use Site\ConfigurationCache;

use Container\RedGetContainerConfigurator;
use Container\RedModelContainerConfigurator;
use Container\DbUpgradeContainerConfigurator;

####################
use Pes\View\ViewFactory;
use Pes\View\View;
use Pes\View\Template\PhpTemplate;
use Pes\View\Template\InterpolateTemplate;

use Pes\View\Renderer\InterpolateRenderer;

use Pes\Http\Factory\EnvironmentFactory;
use Pes\Middleware\UnprocessedRequestHandler;  //NoMatchSelectorItemRequestHandler;

use Pes\Http\Factory\ResponseFactory;
use Pes\Http\Response;
use Pes\Http\ResponseSender;

//echo "<pre>".print_r($bootstrapLoggerArray, true)."</pre>";

// "C:\ApacheRoot\web\vendor\composer/../pes/pes/src\View\Renderer\InterpolateRenderer.php"
$r = new InterpolateRenderer();

$environment = (new EnvironmentFactory())->createFromGlobals();
//$app = (new WebAppFactory())->createFromEnvironment($environment);
        $appContainer =
            (new RedGetContainerConfigurator())->configure(
                (new RedModelContainerConfigurator())->configure(
                    (new DbUpgradeContainerConfigurator())->configure(
                                new Container((new AppContainerConfigurator())->configure(new Container()))
                    )
                )
            );

//$app->setAppContainer($appContainer);
$basepath = "";
$tinyToolsbarsLang = "cs";
$view = $appContainer->get(View::class)
                    ->setTemplate(new PhpTemplate(ConfigurationCache::layoutController()['scriptsEditableMode']))
                    ->setData([
                        'tinyMCEConfig' => $appContainer->get(View::class)
                                ->setTemplate(new InterpolateTemplate(ConfigurationCache::layoutController()['tinyConfig']))
                                ->setData([
                                    // pro tiny_config.js
                                    'basePath' => $basepath,
                                    'toolbarsLang' => $tinyToolsbarsLang,
                                    // prvky pole contentCSS - tyto tři proměnné jsou prvky pole - pole je v tiny_config.js v proměnné contentCss
                                    'urlStylesCss' => ConfigurationCache::layoutController()['urlStylesCss'],
                                    'urlSemanticCss' => ConfigurationCache::layoutController()['urlSemanticCss'],
                                    'urlContentTemplatesCss' => ConfigurationCache::layoutController()['urlContentTemplatesCss']
                        ]),

                        'urlTinyMCE' => ConfigurationCache::layoutController()['urlTinyMCE'],
                        'urlJqueryTinyMCE' => ConfigurationCache::layoutController()['urlJqueryTinyMCE'],

                        'urlTinyInit' => ConfigurationCache::layoutController()['urlTinyInit'],
                        'editScript' => ConfigurationCache::layoutController()['urlEditScript'],
                    ]);



//$response = $app->run($selector, new UnprocessedRequestHandler());
$response = (new ResponseFactory())->createResponse();
$str = $view->getString();
$size = $response->getBody()->write($str);
$response->getBody()->rewind();
(new ResponseSender())->send($response);

#################

