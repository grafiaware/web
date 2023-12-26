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
use Container\DbUpgradeContainerConfigurator;

use Web\Middleware\Page\Controller\PageController;

use Web\Middleware\Page\PrepareService\Prepare;

class Web extends AppMiddlewareAbstract implements MiddlewareInterface {

    const HEADER = 'X-WEB-Time';

    private $container;

    /**
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return Response
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        $startTime = microtime(true);

        // middleware kontejner:
        //      používá jen db upgrade (k db old se přistupuje z login middleware)
        //      app container se nekonfuguruje znovu - bere se z app
        $this->container =
            (new WebContainerConfigurator())->configure(
                (new DbUpgradeContainerConfigurator())->configure(
                    new Container($this->getApp()->getAppContainer())
                )
            );
        // Nový kontejner nastaví jako kontejner aplikace - pro middleware Transformator
        $this->getApp()->setAppContainer($this->container);

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
        
        /** @var Prepare $prepare */
        $prepare = $this->container->get(Prepare::class);
        $prepare->prepareDbByStatus();
        
        $response =  $router->process($request, $handler) ;

        return $response->withHeader(self::HEADER, sprintf('%2.3fms', (microtime(true) - $startTime) * 1000));

    }
}


