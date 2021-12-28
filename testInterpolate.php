<?php
declare(strict_types=1);

define('PROJECT_PATH', str_replace("\\", "/", preg_replace('/^' . preg_quote($_SERVER['DOCUMENT_ROOT'], '/') . '/', '', __DIR__))."/");

include 'vendor/pes/pes/src/Bootstrap/Bootstrap.php';

use Application\WebAppFactory;

use Container\AppContainerConfigurator;
use Pes\Container\Container;
use Pes\Container\AutowiringContainer;

use Site\Configuration;

use Container\WebContainerConfigurator;
use Container\HierarchyContainerConfigurator;
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
            (new WebContainerConfigurator())->configure(
                (new HierarchyContainerConfigurator())->configure(
                    (new DbUpgradeContainerConfigurator())->configure(
                                new Container((new AppContainerConfigurator())->configure(new Container()))
                    )
                )
            );

//$app->setAppContainer($appContainer);
$basepath = "";
$tinyToolsbarsLang = "cs";
$view = $appContainer->get(View::class)
                    ->setTemplate(new PhpTemplate(Configuration::layoutController()['scriptsEditableMode']))
                    ->setData([
                        'tinyMCEConfig' => $appContainer->get(View::class)
                                ->setTemplate(new InterpolateTemplate(Configuration::layoutController()['tinyConfig']))
                                ->setData([
                                    // pro tiny_config.js
                                    'basePath' => $basepath,
                                    'toolbarsLang' => $tinyToolsbarsLang,
                                    // prvky pole contentCSS - tyto tři proměnné jsou prvky pole - pole je v tiny_config.js v proměnné contentCss
                                    'urlStylesCss' => Configuration::layoutController()['urlStylesCss'],
                                    'urlSemanticCss' => Configuration::layoutController()['urlSemanticCss'],
                                    'urlContentTemplatesCss' => Configuration::layoutController()['urlContentTemplatesCss']
                        ]),

                        'urlTinyMCE' => Configuration::layoutController()['urlTinyMCE'],
                        'urlJqueryTinyMCE' => Configuration::layoutController()['urlJqueryTinyMCE'],

                        'urlTinyInit' => Configuration::layoutController()['urlTinyInit'],
                        'editScript' => Configuration::layoutController()['urlEditScript'],
                    ]);



//$response = $app->run($selector, new UnprocessedRequestHandler());
$response = (new ResponseFactory())->createResponse();
$str = $view->getString();
$size = $response->getBody()->write($str);
$response->getBody()->rewind();
(new ResponseSender())->send($response);

#################

