<?php
namespace Consent\Middleware\ConsentLogger;

use Application\Api\Catalog\ConsentRouteCatalog;
use Application\Api\RouteCatalogWiring;

use Pes\Application\Middleware\AppMiddlewareAbstract;
use Pes\Container\Container;

use Container\ConsentContainerConfigurator;

use Pes\Router\RouteSegmentGenerator;
use Pes\Router\RouterInterface;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class ConsentLogger extends AppMiddlewareAbstract implements MiddlewareInterface {

    private $container;

    /**
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return Response
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {

        $this->container =
            (new ConsentContainerConfigurator())->configure(
                new Container($this->getApp()->getAppContainer())
            );

        /** @var RouteSegmentGenerator $routeGenerator */
        $routeGenerator = $this->container->get(RouteSegmentGenerator::class);

        RouteCatalogWiring::wire(
            $routeGenerator,
            $this->container,
            ConsentRouteCatalog::definitions(),
            ['POST']
        );

        /** @var $router RouterInterface */
        $router = $this->container->get(RouterInterface::class);
        $router->exchangeRoutes($routeGenerator);

        return $router->process($request, $handler) ;
    }
}
