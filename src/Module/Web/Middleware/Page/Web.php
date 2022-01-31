<?php
namespace Web\Middleware\Page;

use Pes\Middleware\AppMiddlewareAbstract;
use Pes\Container\Container;

use Pes\Router\RouteSegmentGenerator;
use Pes\Router\RouterInterface;

use Pes\Acl\ResourcePrefix;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Container\WebContainerConfigurator;
use Container\HierarchyContainerConfigurator;
use Container\DbUpgradeContainerConfigurator;

use Web\Middleware\Page\Controller\PageController;

class Web extends AppMiddlewareAbstract implements MiddlewareInterface {

    private $container;

    /**
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return Response
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {

        // middleware kontejner:
        //      používá jen db upgrade (k db old se přistupuje z login middleware)
        //      app container se nekonfuguruje znovu - bere se z app
//        $this->container =
//            (new WebContainerConfigurator())->configure(
//                (new HierarchyContainerConfigurator())->configure(
//                    (new DbUpgradeContainerConfigurator())->configure(
//                            new Container($this->getApp()->getAppContainer())
//                    )
//                )
//            );
        $this->container = $this->getApp()->getAppContainer();


        /** @var RouteSegmentGenerator $routeGenerator */
        $routeGenerator = $this->container->get(RouteSegmentGenerator::class);

        $routeGenerator->addRouteForAction('GET', '/web/v1/page/block/:name', function(ServerRequestInterface $request, $name) {
            /** @var PageController $ctrl */
            $ctrl = $this->container->get(PageController::class);
            return $ctrl->block($request, $name);
            });
            $routeGenerator->addRouteForAction('GET', '/web/v1/page/item/:uid', function(ServerRequestInterface $request, $uid) {
            /** @var PageController $ctrl */
            $ctrl = $this->container->get(PageController::class);
            return $ctrl->item($request, $uid);
            });
            $routeGenerator->addRouteForAction('GET', '/web/v1/page/subitem/:uid', function(ServerRequestInterface $request, $uid) {
            /** @var PageController $ctrl */
            $ctrl = $this->container->get(PageController::class);
            return $ctrl->subitem($request, $uid);
            });
            $routeGenerator->addRouteForAction('GET', '/web/v1/page/searchresult', function(ServerRequestInterface $request) {
            /** @var PageController $ctrl */
            $ctrl = $this->container->get(PageController::class);
            return $ctrl->searchResult($request);
            });
        $routeGenerator->addRouteForAction('GET', '/', function(ServerRequestInterface $request) {
            /** @var PageController $ctrl */
            $ctrl = $this->container->get(PageController::class);
            return $ctrl->home($request);
            });

####################################
        /** @var $router RouterInterface */
        $router = $this->container->get(RouterInterface::class);
        $router->exchangeRoutes($routeGenerator);

        return $router->process($request, $handler) ;
    }
}


