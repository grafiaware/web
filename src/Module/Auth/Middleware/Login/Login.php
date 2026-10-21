<?php
namespace Auth\Middleware\Login;

use Application\Api\Catalog\AuthRouteCatalog;
use Application\Api\RouteCatalogWiring;

use Pes\Application\Middleware\AppMiddlewareAbstract;
use Pes\Container\Container;

use Container\AuthStaticRegistryContainerConfigurator;
use Container\AuthContainerConfigurator;
use Container\StaticItemContainerConfigurator;
use Container\AuthDbContainerConfigurator;
use Container\MailContainerConfigurator;

use Pes\Router\RouteSegmentGenerator;
use Pes\Router\RouterInterface;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;


class Login extends AppMiddlewareAbstract implements MiddlewareInterface {

    private $container;
    private $routeGenerator;

    /**
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return Response
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {

        // AuthStaticRegistryContainerConfigurator: SQLite registry + StaticRegistryRepo
        // (auth nemá red DB — StaticItem path/template se čte z lokální registry)
        $this->container =
            (new AuthContainerConfigurator())->configure(
                (new StaticItemContainerConfigurator())->configure(
                    (new AuthStaticRegistryContainerConfigurator())->configure(
                        (new AuthDbContainerConfigurator())->configure(
                            (new MailContainerConfigurator())->configure(
                                (new Container($this->getApp()->getAppContainer()))
                            )
                        )
                    )
                )
            );

        /** @var RouteSegmentGenerator $this->routeGenerator */
        $this->routeGenerator = $this->container->get(RouteSegmentGenerator::class);

        RouteCatalogWiring::wire(
            $this->routeGenerator,
            $this->container,
            AuthRouteCatalog::definitions(),
            ['GET', 'POST', 'PUT', 'DELETE']
        );

        /** @var RouterInterface $router */
        $router = $this->container->get(RouterInterface::class);
        $router->exchangeRoutes($this->routeGenerator);
        return $router->process($request, $handler);
    }
}
