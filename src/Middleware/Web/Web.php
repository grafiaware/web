<?php
namespace Middleware\Web;

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
use Container\DbUpgradeContainerConfigurator;
use Container\HierarchyContainerConfigurator;
use Container\ComponentContainerConfigurator;

use Middleware\Web\Controller\ComponentController;

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
        $this->container =
            (new ComponentContainerConfigurator())->configure(
                (new HierarchyContainerConfigurator())->configure(
                    (new WebContainerConfigurator())->configure(
                        (new DbUpgradeContainerConfigurator())->configure(
                                new Container($this->getApp()->getAppContainer())
                        )
                    )
                )
            );

        /** @var RouteSegmentGenerator $routeGenerator */
        $routeGenerator = $this->container->get(RouteSegmentGenerator::class);

        $routeGenerator->addRouteForAction('GET', '/www/last', function(ServerRequestInterface $request) {
            /** @var ComponentController $ctrl */
            $ctrl = $this->container->get(ComponentController::class);
            return $ctrl->last($request);
            });
        $routeGenerator->addRouteForAction('GET', '/www/item/:langCode/:uid', function(ServerRequestInterface $request, $langCode, $uid) {
            /** @var ComponentController $ctrl */
            $ctrl = $this->container->get(ComponentController::class);
            return $ctrl->item($request, $langCode, $uid);
            });
        $routeGenerator->addRouteForAction('GET', '/www/searchresult', function(ServerRequestInterface $request) {
            /** @var ComponentController $ctrl */
            $ctrl = $this->container->get(ComponentController::class);
            return $ctrl->searchResult($request);
            });
        $routeGenerator->addRouteForAction('GET', '/', function(ServerRequestInterface $request) {
            /** @var ComponentController $ctrl */
            $ctrl = $this->container->get(ComponentController::class);
            return $ctrl->home($request);
            });

####################################
        /** @var $router RouterInterface */
        $router = $this->container->get(RouterInterface::class);
        $router->exchangeRoutes($routeGenerator);

        return $router->process($request, $handler) ;
    }
}


