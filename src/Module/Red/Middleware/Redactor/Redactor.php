<?php
namespace Red\Middleware\Redactor;

use Application\Api\Catalog\RedRouteCatalog;
use Application\Api\RouteCatalogWiring;

use Pes\Application\Middleware\AppMiddlewareAbstract;
use Pes\Container\Container;

use Pes\Router\RouteSegmentGenerator;
use Pes\Router\RouterInterface;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Container\StaticItemContainerConfigurator;
use Container\RedGetContainerConfigurator;
use Container\RedPostContainerConfigurator;
use Container\RedModelContainerConfigurator;
use Container\DbUpgradeContainerConfigurator;

use Red\Middleware\Redactor\Controler\Exception\UnexpectedRequestMethodException;

class Redactor extends AppMiddlewareAbstract implements MiddlewareInterface {

    private $container;

    private $routeGenerator;

    /**
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return Response
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {

        if ($request->getMethod()=="GET") {
            $this->prepareProcessGet();
        } elseif ($request->getMethod()=="POST" || $request->getMethod()=="PUT") {
            $this->prepareProcessPost();
         } else {
            throw new UnexpectedRequestMethodException("Neznámá metoda HTTP request '{$request->getMethod()}'.");
        }

        /** @var $router RouterInterface */
        $router = $this->container->get(RouterInterface::class);
        $router->exchangeRoutes($this->routeGenerator);
        return $router->process($request, $handler) ;
    }

#### GET ################################

    private function prepareProcessGet() {
        // middleware kontejner:
        //      nový kontejner konfigurovaný MenuContainerConfigurator
        //      -> delegát další nový kontejner konfigurovaný ApiContainerConfigurator a LoginContainerConfigurator
        //      -> delegát aplikační kontejner
        // operace s menu používají databázi z menu kontejneru (upgrade), ostatní používají starou databázi z app kontejneru (připojovací informace
        // jsou v jednotlivých kontejnerech)
        $this->container =
            (new RedGetContainerConfigurator())->configure(
                (new StaticItemContainerConfigurator())->configure(
                    (new RedModelContainerConfigurator())->configure(
                        (new DbUpgradeContainerConfigurator())->configure(
                                new Container($this->getApp()->getAppContainer())
                        )
                    )
                )
            );

        #### dočasně - pro Transformator ##########################################################
        #
            // Nový kontejner nastaví jako kontejner aplikace - pro middleware Transformator
            $this->getApp()->setAppContainer($this->container);
        #
        ###########################################################################################

        /** @var RouteSegmentGenerator $this->routeGenerator */
        $this->routeGenerator = $this->container->get(RouteSegmentGenerator::class);

        RouteCatalogWiring::wire(
            $this->routeGenerator,
            $this->container,
            RedRouteCatalog::definitions(),
            ['GET']
        );
    }

#### POST and PUT ################################

    private function prepareProcessPost() {
        $this->container =
                (new RedPostContainerConfigurator())->configure(
                    (new StaticItemContainerConfigurator())->configure(
                        (new RedModelContainerConfigurator())->configure(
                            (new DbUpgradeContainerConfigurator())->configure(
                                    new Container($this->getApp()->getAppContainer())
                            )
                        )
                    )
                );

        /** @var RouteSegmentGenerator $this->routeGenerator */
        $this->routeGenerator = $this->container->get(RouteSegmentGenerator::class);

        RouteCatalogWiring::wire(
            $this->routeGenerator,
            $this->container,
            RedRouteCatalog::definitions(),
            ['POST', 'PUT']
        );
    }
}
