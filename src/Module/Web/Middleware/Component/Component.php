<?php
namespace Web\Middleware\Component;

use Pes\Middleware\AppMiddlewareAbstract;
use Pes\Container\Container;

use Pes\Router\RouteSegmentGenerator;
use Pes\Router\RouterInterface;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Container\WebContainerConfigurator;
use Container\HierarchyContainerConfigurator;
use Container\DbUpgradeContainerConfigurator;
use Container\LoginContainerConfigurator;

use Web\Middleware\Component\Controller\ComponentControler;
use Web\Middleware\Component\Controller\TemplateControler;

class Component extends AppMiddlewareAbstract implements MiddlewareInterface {

    ## proměnné třídy - pro dostupnost v Closure definovaných v routách ##
    private $request;

    private $container;

    /**
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return Response
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {

        $this->request = $request;

        // middleware kontejner:
        //      nový kontejner konfigurovaný MenuContainerConfigurator
        //      -> delegát další nový kontejner konfigurovaný ApiContainerConfigurator a LoginContainerConfigurator
        //      -> delegát aplikační kontejner
        // operace s menu používají databázi z menu kontejneru (upgrade), ostatní používají starou databázi z app kontejneru (připojovací informace
        // jsou v jednotlivých kontejnerech)
        $this->container =
            (new WebContainerConfigurator())->configure(
                (new HierarchyContainerConfigurator())->configure(
                    (new DbUpgradeContainerConfigurator())->configure(
                            new Container($this->getApp()->getAppContainer())
                    )
                )
            );
        // Nový kontejner nastaví jako kontejner aplikace - pro middleware Transformator
        $this->getApp()->setAppContainer($this->container);

####################################
        /** @var RouteSegmentGenerator $routeGenerator */
        $routeGenerator = $this->container->get(RouteSegmentGenerator::class);

        #### ComponentController ####
        $routeGenerator->addRouteForAction('GET', '/web/v1/flash', function(ServerRequestInterface $request) {
            /** @var ComponentControler $ctrl */
            $ctrl = $this->container->get(ComponentControler::class);
            return $ctrl->flash($request);
            });
        $routeGenerator->addRouteForAction('GET', '/web/v1/service/:name', function(ServerRequestInterface $request, $name) {
            /** @var ComponentControler $ctrl */
            $ctrl = $this->container->get(ComponentControler::class);
            return $ctrl->serviceComponent($request, $name);
            });
        $routeGenerator->addRouteForAction('GET', '/web/v1/static/:staticName', function(ServerRequestInterface $request, $staticName) {
            /** @var ComponentControler $ctrl */
            $ctrl = $this->container->get(ComponentControler::class);
            return $ctrl->static($request, $staticName);
            });
//        $routeGenerator->addRouteForAction('GET', '/web/v1/staticfolded/:folderName', function(ServerRequestInterface $request, $staticName) {
//            /** @var ComponentController $ctrl */
//            $ctrl = $this->container->get(ComponentController::class);
//            return $ctrl->static($request, $staticName);
//            });
        $routeGenerator->addRouteForAction('GET', '/web/v1/empty/:menuItemId', function(ServerRequestInterface $request, $menuItemId) {
            /** @var ComponentControler $ctrl */
            $ctrl = $this->container->get(ComponentControler::class);
            return $ctrl->empty($request, $menuItemId);
            });
        $routeGenerator->addRouteForAction('GET', '/web/v1/paper/:menuItemId', function(ServerRequestInterface $request, $menuItemId) {
            /** @var ComponentControler $ctrl */
            $ctrl = $this->container->get(ComponentControler::class);
            return $ctrl->paper($request, $menuItemId);
            });
        $routeGenerator->addRouteForAction('GET', '/web/v1/article/:menuItemId', function(ServerRequestInterface $request, $menuItemId) {
            /** @var ComponentControler $ctrl */
            $ctrl = $this->container->get(ComponentControler::class);
            return $ctrl->article($request, $menuItemId);
            });
        $routeGenerator->addRouteForAction('GET', '/web/v1/multipage/:menuItemId', function(ServerRequestInterface $request, $menuItemId) {
            /** @var ComponentControler $ctrl */
            $ctrl = $this->container->get(ComponentControler::class);
            return $ctrl->multipage($request, $menuItemId);
            });
        $routeGenerator->addRouteForAction('GET', '/web/v1/unknown', function(ServerRequestInterface $request, $menuItemId) {
            /** @var ComponentControler $ctrl */
            $ctrl = $this->container->get(ComponentControler::class);
            return $ctrl->unknown($request);
            });

#### TemplateController ####
        $routeGenerator->addRouteForAction('GET', '/web/v1/templateslist/:type', function(ServerRequestInterface $request, $type) {
            /** @var TemplateControler $ctrl */
            $ctrl = $this->container->get(TemplateControler::class);
            return $ctrl->templatesList($this->request, $type);
            });
        $routeGenerator->addRouteForAction('GET', '/web/v1/papertemplate/:folder', function(ServerRequestInterface $request, $folder) {
            /** @var TemplateControler $ctrl */
            $ctrl = $this->container->get(TemplateControler::class);
            return $ctrl->papertemplate($request, $folder);
            });
        $routeGenerator->addRouteForAction('GET', '/web/v1/articletemplate/:folder', function(ServerRequestInterface $request, $folder) {
            /** @var TemplateControler $ctrl */
            $ctrl = $this->container->get(TemplateControler::class);
            return $ctrl->articletemplate($request, $folder);
            });
        $routeGenerator->addRouteForAction('GET', '/web/v1/multipagetemplate/:folder', function(ServerRequestInterface $request, $folder) {
            /** @var TemplateControler $ctrl */
            $ctrl = $this->container->get(TemplateControler::class);
            return $ctrl->multipagetemplate($request, $folder);
            });
        $routeGenerator->addRouteForAction('GET', '/web/v1/authortemplate/:name', function(ServerRequestInterface $request, $name) {
            /** @var TemplateControler $ctrl */
            $ctrl = $this->container->get(TemplateControler::class);
            return $ctrl->authorTemplate($request, $name);
            });
####################################

            /** @var $router RouterInterface */
        $router = $this->container->get(RouterInterface::class);
        $router->exchangeRoutes($routeGenerator);

        return $router->process($request, $handler) ;
    }
}


