<?php
namespace Middleware\Xhr;

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

use \Middleware\Xhr\Controller\{
    TemplateControler, ComponentControler
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

        $routeGenerator->addRouteForAction('GET', '/component/v1/papertemplate/:folder', function(ServerRequestInterface $request, $folder) {
            /** @var TemplateControler $ctrl */
            $ctrl = $this->container->get(TemplateControler::class);
            return $ctrl->papertemplate($request, $folder);
            });
        $routeGenerator->addRouteForAction('GET', '/component/v1/authortemplate/:folder/:name', function(ServerRequestInterface $request, $folder, $name) {
            /** @var TemplateControler $ctrl */
            $ctrl = $this->container->get(TemplateControler::class);
            return $ctrl->authorTemplate($request, $folder, $name);
            });

        #### ComponentControler ####

        $routeGenerator->addRouteForAction('GET', '/component/v1/nameditem/:name', function(ServerRequestInterface $request, $name) {
            /** @var ComponentControler $ctrl */
            $ctrl = $this->container->get(ComponentControler::class);
            return $ctrl->componentItem($request, $name);
            });
        $routeGenerator->addRouteForAction('GET', '/component/v1/item/:langCode/:uid', function(ServerRequestInterface $request, $langCode, $uid) {
            /** @var ComponentControler $ctrl */
            $ctrl = $this->container->get(ComponentControler::class);
            return $ctrl->item($request, $langCode, $uid);
            });
        $routeGenerator->addRouteForAction('GET', '/component/v1/flash', function(ServerRequestInterface $request) {
            /** @var ComponentControler $ctrl */
            $ctrl = $this->container->get(ComponentControler::class);
            return $ctrl->flash($request);
            });
        $routeGenerator->addRouteForAction('GET', '/component/v1/service/:name', function(ServerRequestInterface $request, $name) {
            /** @var ComponentControler $ctrl */
            $ctrl = $this->container->get(ComponentControler::class);
            return $ctrl->serviceComponent($request, $name);
            });
        $routeGenerator->addRouteForAction('GET', '/component/v1/static/:staticName', function(ServerRequestInterface $request, $staticName) {
            /** @var ComponentControler $ctrl */
            $ctrl = $this->container->get(ComponentControler::class);
            return $ctrl->static($request, $staticName);
            });
####################################
        /** @var $router RouterInterface */
        $router = $this->container->get(RouterInterface::class);
        $router->exchangeRoutes($routeGenerator);

        return $router->process($request, $handler) ;
    }
}


