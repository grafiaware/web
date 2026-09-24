<?php
namespace Build\Middleware\Build;

use Application\Api\Catalog\BuildRouteCatalog;
use Application\Api\RouteCatalogWiring;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

use Pes\Container\Container;
use Pes\Application\Middleware\AppMiddlewareAbstract;
use Pes\Router\RouteSegmentGenerator;
use Pes\Router\RouterInterface;

use Container\{
    BuildContainerConfigurator, DbUpgradeContainerConfigurator, AuthDbContainerConfigurator
};

/**
 * Description of MenuApplication
 *
 * @author pes2704
 */
class Build extends AppMiddlewareAbstract implements MiddlewareInterface {

    /**
     * @var Container
     */
    private $container;

    //-------------------------------------------------------------------------------
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {

        $this->container =
            (new BuildContainerConfigurator())->configure(
                new Container(      // pro Build musí bý nový kontejner - jeho konfigurace přepisuje objekty v DbUpgrade
                    (new DbUpgradeContainerConfigurator())->configure(
                        new Container(      // pro DbUpgrade musí bý nový kontejner - jeho konfigurace přepisuje objekty v DbOld
                                (new AuthDbContainerConfigurator())->configure(
                                    new Container($this->getApp()->getAppContainer())
                                )
                        )
                    )
                )
            );

        /** @var RouteSegmentGenerator $routeGenerator */
        $routeGenerator = $this->container->get(RouteSegmentGenerator::class);

        RouteCatalogWiring::wire(
            $routeGenerator,
            $this->container,
            BuildRouteCatalog::definitions(),
            ['GET', 'POST']
        );

        /** @var RouterInterface $router */
        $router = $this->container->get(RouterInterface::class);
        $router->exchangeRoutes($routeGenerator);

        return $router->process($request, $handler) ;
    }

}
