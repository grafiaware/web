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
use Container\StaticItemContainerConfigurator;
use Container\WebModelContainerConfigurator;
use Container\DbUpgradeContainerConfigurator;

use Web\Middleware\Page\Controler\PageControler;
use Web\Middleware\Page\Controler\ComponentControler;
use Web\Middleware\Page\Controler\FlashControler;

use Web\Middleware\Page\PrepareService\Prepare;

class Web extends AppMiddlewareAbstract implements MiddlewareInterface {

    const HEADER = 'X-WEB-Time';

    private $container;
    private $routeGenerator;

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
                (new StaticItemContainerConfigurator())->configure(
                    (new WebModelContainerConfigurator())->configure(
                        (new DbUpgradeContainerConfigurator())->configure(
                            new Container($this->getApp()->getAppContainer())
                        )
                    )
                )
            );
        // Nový kontejner nastaví jako kontejner aplikace - pro middleware Transformator
        $this->getApp()->setAppContainer($this->container);

        /** @var RouteSegmentGenerator $this->routeGenerator */
        $this->routeGenerator = $this->container->get(RouteSegmentGenerator::class);
        
        #### ComponentControler ####
        $this->routeGenerator->addRouteForAction('GET', '/web/v1/flash', function(ServerRequestInterface $request) {
            /** @var FlashControler $ctrl */
            $ctrl = $this->container->get(FlashControler::class);
            return $ctrl->flash($request);
            });        

        $this->routeGenerator->addRouteForAction('GET', '/web/v1/component/:name', function(ServerRequestInterface $request, $name) {
            /** @var ComponentControler $ctrl */
            $ctrl = $this->container->get(ComponentControler::class);
            return $ctrl->component($request, $name);
            });
        #### PageControler ####
        $this->routeGenerator->addRouteForAction('GET', '/web/v1/page/block/:name', function(ServerRequestInterface $request, $name) {
            /** @var PageControler $ctrl */
            $ctrl = $this->container->get(PageControler::class);
            return $ctrl->block($request, $name);
            });
        $this->routeGenerator->addRouteForAction('GET', '/web/v1/page/item/:uid', function(ServerRequestInterface $request, $uid) {
            /** @var PageControler $ctrl */
            $ctrl = $this->container->get(PageControler::class);
            return $ctrl->item($request, $uid);
            });
        $this->routeGenerator->addRouteForAction('GET', '/web/v1/page/searchresult', function(ServerRequestInterface $request) {
            /** @var PageControler $ctrl */
            $ctrl = $this->container->get(PageControler::class);
            return $ctrl->searchResult($request);
            });
        $this->routeGenerator->addRouteForAction('GET', '/', function(ServerRequestInterface $request) {
            /** @var PageControler $ctrl */
            $ctrl = $this->container->get(PageControler::class);
            return $ctrl->home($request);
            });

####################################
        /** @var $router RouterInterface */
        $router = $this->container->get(RouterInterface::class);
        $router->exchangeRoutes($this->routeGenerator);
        
        /** @var Prepare $prepare */
        $prepare = $this->container->get(Prepare::class);
        $prepare->prepareDbByStatus();
        
        $response =  $router->process($request, $handler) ;

        return $response->withHeader(self::HEADER, sprintf('%2.3fms', (microtime(true) - $startTime) * 1000));

    }
}


