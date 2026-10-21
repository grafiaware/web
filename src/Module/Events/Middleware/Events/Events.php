<?php
namespace Events\Middleware\Events;

use Application\Api\Catalog\EventsRouteCatalog;
use Application\Api\RouteCatalogWiring;

use Pes\Application\Middleware\AppMiddlewareAbstract;

use Pes\Router\RouteSegmentGenerator;
use Pes\Router\RouterInterface;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Red\Middleware\Redactor\Controler\Exception\UnexpectedRequestMethodException;

class Events extends AppMiddlewareAbstract implements MiddlewareInterface {

    private $container;

    private $routeGenerator;

    /**
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return Response
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        // middleware kontejner: má nastaven kontejner z middleware ValidateUser
        $this->container = $this->getApp()->getAppContainer();  // "dědí" kontejnet z  ValidateUser
        /** @var RouteSegmentGenerator $this->routeGenerator */
        $this->routeGenerator = $this->container->get(RouteSegmentGenerator::class);

        // PUT/DELETE přidané pro static registry push z red modulu (server-to-server)
        $method = $request->getMethod();
        if (!in_array($method, ['GET', 'POST', 'PUT', 'DELETE'], true)) {
            throw new UnexpectedRequestMethodException("Neznámá metoda HTTP request '{$method}'.");
        }

        RouteCatalogWiring::wire(
            $this->routeGenerator,
            $this->container,
            EventsRouteCatalog::definitions(),
            [$method]
        );

        /** @var $router RouterInterface */
        $router = $this->container->get(RouterInterface::class);
        $router->exchangeRoutes($this->routeGenerator);
        return $router->process($request, $handler) ;
    }
}
