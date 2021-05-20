<?php
namespace Red\Middleware\Component;

use Pes\Middleware\AppMiddlewareAbstract;
use Pes\Container\Container;

use Pes\Router\RouteSegmentGenerator;
use Pes\Router\RouterInterface;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Container\{
    ComponentContainerConfigurator, HierarchyContainerConfigurator, DbUpgradeContainerConfigurator, LoginContainerConfigurator
};

use \Red\Middleware\Component\Controller\{
    TemplateController, ComponentController
};

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
                (new ComponentContainerConfigurator())->configure(
                    (new HierarchyContainerConfigurator())->configure(
                       (new DbUpgradeContainerConfigurator())->configure(
                            (new Container(
                                    (new LoginContainerConfigurator())->configure(
                                        new Container($this->getApp()->getAppContainer())
                                    )
                                )
                            )
                        )
                    )
                );

####################################
        /** @var RouteSegmentGenerator $routeGenerator */
        $routeGenerator = $this->container->get(RouteSegmentGenerator::class);

        #### TemplateController ####

        $routeGenerator->addRouteForAction('GET', '/red/v1/papertemplate/:folder', function(ServerRequestInterface $request, $folder) {
            /** @var TemplateController $ctrl */
            $ctrl = $this->container->get(TemplateController::class);
            return $ctrl->papertemplate($request, $folder);
            });
        $routeGenerator->addRouteForAction('GET', '/red/v1/authortemplate/:folder/:name', function(ServerRequestInterface $request, $folder, $name) {
            /** @var TemplateController $ctrl */
            $ctrl = $this->container->get(TemplateController::class);
            return $ctrl->authorTemplate($request, $folder, $name);
            });

        #### ComponentController ####
        $routeGenerator->addRouteForAction('GET', '/web/v1/flash', function(ServerRequestInterface $request) {
            /** @var ComponentController $ctrl */
            $ctrl = $this->container->get(ComponentController::class);
            return $ctrl->flash($request);
            });
        $routeGenerator->addRouteForAction('GET', '/web/v1/service/:name', function(ServerRequestInterface $request, $name) {
            /** @var ComponentController $ctrl */
            $ctrl = $this->container->get(ComponentController::class);
            return $ctrl->serviceComponent($request, $name);
            });
        $routeGenerator->addRouteForAction('GET', '/web/v1/static/:staticName', function(ServerRequestInterface $request, $staticName) {
            /** @var ComponentController $ctrl */
            $ctrl = $this->container->get(ComponentController::class);
            return $ctrl->static($request, $staticName);
            });
//        $routeGenerator->addRouteForAction('GET', '/web/v1/staticfolded/:folderName', function(ServerRequestInterface $request, $staticName) {
//            /** @var ComponentController $ctrl */
//            $ctrl = $this->container->get(ComponentController::class);
//            return $ctrl->static($request, $staticName);
//            });
        $routeGenerator->addRouteForAction('GET', '/web/v1/itempaper/:menuItemId', function(ServerRequestInterface $request, $menuItemId) {
            /** @var ComponentController $ctrl */
            $ctrl = $this->container->get(ComponentController::class);
            return $ctrl->paper($request, $menuItemId);
            });
        $routeGenerator->addRouteForAction('GET', '/web/v1/itempapereditable/:menuItemId', function(ServerRequestInterface $request, $menuItemId) {
            /** @var ComponentController $ctrl */
            $ctrl = $this->container->get(ComponentController::class);
            return $ctrl->paperEditable($request, $menuItemId);
            });
####################################
        /** @var $router RouterInterface */
        $router = $this->container->get(RouterInterface::class);
        $router->exchangeRoutes($routeGenerator);

        return $router->process($request, $handler) ;
    }
}


