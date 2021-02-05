<?php
declare(strict_types=1);
define('PES_FORCE_DEVELOPMENT', 'force_development');
// nebo
//define('PES_FORCE_PRODUCTION', 'force_production');

define('PROJECT_PATH', str_replace("\\", "/", preg_replace('/^' . preg_quote($_SERVER['DOCUMENT_ROOT'], '/') . '/', '', __DIR__))."/");

include 'vendor/pes/pes/src/Bootstrap/Bootstrap.php';

use Application\WebAppFactory;



use Container\AppContainerConfigurator;
use Pes\Container\Container;
use Pes\Container\AutowiringContainer;

use Site\Configuration;

use Container\WebContainerConfigurator;
use Container\DbUpgradeContainerConfigurator;
use Container\HierarchyContainerConfigurator;
use Container\ComponentContainerConfigurator;


use Middleware\Login\Controller\LoginLogoutController;

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
            (new ComponentContainerConfigurator())->configure(
                (new HierarchyContainerConfigurator())->configure(
                    (new WebContainerConfigurator())->configure(
                        (new DbUpgradeContainerConfigurator())->configure(
                                new Container((new AppContainerConfigurator())->configure(new Container()))
                        )
                    )
                )
            );

//$app->setAppContainer($appContainer);
$basepath = "";
$tinyToolsbarsLang = "cs";
$view = $appContainer->get(View::class)
                    ->setTemplate(new PhpTemplate(Configuration::layoutControler()['linksEditorJs']))
                    ->setData([
                        'tinyMCEConfig' => $appContainer->get(View::class)
                                ->setTemplate(new InterpolateTemplate(Configuration::layoutControler()['tiny_config']))
                                ->setData([

                                    // pro tiny_config.js
                                    'basePath' => $basepath,
                                    'urlStylesCss' => Configuration::layoutControler()['urlStylesCss'],
                                    'urlSemanticCss' => Configuration::layoutControler()['urlSemanticCss'],
                                    'urlContentTemplatesCss' => Configuration::layoutControler()['urlContentTemplatesCss'],
                                    'paperTemplatesUri' =>  Configuration::layoutControler()['paperTemplatesUri'],  // URI pro Template controler
                                    'authorTemplatesPath' => Configuration::layoutControler()['authorTemplatesPath'],
                                    'toolbarsLang' => $tinyToolsbarsLang
                                ]),

                        'urlTinyMCE' => Configuration::layoutControler()['urlTinyMCE'],
                        'urlJqueryTinyMCE' => Configuration::layoutControler()['urlJqueryTinyMCE'],

                        'urlTinyInit' => Configuration::layoutControler()['urlTinyInit'],
                        'editScript' => Configuration::layoutControler()['urlEditScript'],
                    ]);



//$response = $app->run($selector, new UnprocessedRequestHandler());
$response = (new ResponseFactory())->createResponse();
$str = $view->getString();
$size = $response->getBody()->write($str);
$response->getBody()->rewind();
(new ResponseSender())->send($response);

#################

