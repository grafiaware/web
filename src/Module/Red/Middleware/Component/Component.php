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

use \Red\Middleware\Component\Controller\RedComponentControler;
use \Red\Middleware\Component\Controller\TemplateControler;

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

        #### ComponentController ####
        $routeGenerator->addRouteForAction('GET', '/web/v1/flash', function(ServerRequestInterface $request) {
            /** @var RedComponentControler $ctrl */
            $ctrl = $this->container->get(RedComponentControler::class);
            return $ctrl->flash($request);
            });
        $routeGenerator->addRouteForAction('GET', '/web/v1/service/:name', function(ServerRequestInterface $request, $name) {
            /** @var RedComponentControler $ctrl */
            $ctrl = $this->container->get(RedComponentControler::class);
            return $ctrl->serviceComponent($request, $name);
            });
        $routeGenerator->addRouteForAction('GET', '/web/v1/static/:staticName', function(ServerRequestInterface $request, $staticName) {
            /** @var RedComponentControler $ctrl */
            $ctrl = $this->container->get(RedComponentControler::class);
            return $ctrl->static($request, $staticName);
            });
//        $routeGenerator->addRouteForAction('GET', '/web/v1/staticfolded/:folderName', function(ServerRequestInterface $request, $staticName) {
//            /** @var ComponentController $ctrl */
//            $ctrl = $this->container->get(ComponentController::class);
//            return $ctrl->static($request, $staticName);
//            });
        $routeGenerator->addRouteForAction('GET', '/web/v1/empty/:menuItemId', function(ServerRequestInterface $request, $menuItemId) {
            /** @var RedComponentControler $ctrl */
            $ctrl = $this->container->get(RedComponentControler::class);
            return $ctrl->empty($request, $menuItemId);
            });
        $routeGenerator->addRouteForAction('GET', '/web/v1/paper/:menuItemId', function(ServerRequestInterface $request, $menuItemId) {
            /** @var RedComponentControler $ctrl */
            $ctrl = $this->container->get(RedComponentControler::class);
            return $ctrl->paper($request, $menuItemId);
            });
        $routeGenerator->addRouteForAction('GET', '/web/v1/article/:menuItemId', function(ServerRequestInterface $request, $menuItemId) {
            /** @var RedComponentControler $ctrl */
            $ctrl = $this->container->get(RedComponentControler::class);
            return $ctrl->article($request, $menuItemId);
            });
        $routeGenerator->addRouteForAction('GET', '/web/v1/multipage/:menuItemId', function(ServerRequestInterface $request, $menuItemId) {
            /** @var RedComponentControler $ctrl */
            $ctrl = $this->container->get(RedComponentControler::class);
            return $ctrl->multipage($request, $menuItemId);
            });

#### TemplateController ####

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


