<?php
namespace Status\Middleware\Component;

use Pes\Middleware\AppMiddlewareAbstract;
use Pes\Container\Container;

use Pes\Router\RouteSegmentGenerator;
use Pes\Router\RouterInterface;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Container\{
    ComponentContainerConfigurator, RedModelContainerConfigurator, DbUpgradeContainerConfigurator, AuthContainerConfigurator
};

use \Status\Middleware\Component\Controler\StatusComponentControler;

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
                            (new Container(
                                    (new AuthContainerConfigurator())->configure(
                                        new Container($this->getApp()->getAppContainer())
                                    )
                                )
                            )
                );

####################################
        /** @var RouteSegmentGenerator $routeGenerator */
        $routeGenerator = $this->container->get(RouteSegmentGenerator::class);

        #### ComponentController ####
        $routeGenerator->addRouteForAction('GET', '/web/v1/flash', function(ServerRequestInterface $request) {
            /** @var StatusComponentControler $ctrl */
            $ctrl = $this->container->get(StatusComponentControler::class);
            return $ctrl->flash($request);
            });

####################################

        /** @var $router RouterInterface */
        $router = $this->container->get(RouterInterface::class);
        $router->exchangeRoutes($routeGenerator);

        return $router->process($request, $handler) ;
    }
}


