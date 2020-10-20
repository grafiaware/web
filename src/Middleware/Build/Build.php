<?php
namespace Middleware\Build;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

use Pes\Container\Container;
use Pes\Middleware\AppMiddlewareAbstract;
use Pes\Router\RouteSegmentGenerator;
use Pes\Router\RouterInterface;

use Container\{
    BuildContainerConfigurator, DbUpgradeContainerConfigurator, LoginContainerConfigurator, DbOldContainerConfigurator
};

use Middleware\Build\Controler\DatabaseControler;

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
                (new DbUpgradeContainerConfigurator())->configure(
                    new Container(      // pro DbUpgrade musí bý nový kontejner - jeho konfigurace přepisuje objekty v DbOld
                        (new LoginContainerConfigurator())->configure(
                            (new DbOldContainerConfigurator())->configure(
                                new Container($this->getApp()->getAppContainer())
                            )
                        )
                    )
                )
            );

####################################
        /** @var RouteSegmentGenerator $routeGenerator */
        $routeGenerator = $this->container->get(RouteSegmentGenerator::class);

        #### TemplateController ####
        $routeGenerator->addRouteForAction('GET', '/build/dropdb', function(ServerRequestInterface $request) {
            /** @var DatabaseControler $ctrl */
            $ctrl = $this->container->get(DatabaseControler::class);
            return $ctrl->dropDb();
            });
        $routeGenerator->addRouteForAction('GET', '/build/createdb', function(ServerRequestInterface $request) {
            /** @var DatabaseControler $ctrl */
            $ctrl = $this->container->get(DatabaseControler::class);
            return $ctrl->createDb();
            });
            $routeGenerator->addRouteForAction('GET', '/build/drop', function(ServerRequestInterface $request) {
            /** @var DatabaseControler $ctrl */
            $ctrl = $this->container->get(DatabaseControler::class);
            return $ctrl->drop();
            });
        $routeGenerator->addRouteForAction('GET', '/build/create', function(ServerRequestInterface $request) {
            /** @var DatabaseControler $ctrl */
            $ctrl = $this->container->get(DatabaseControler::class);
            return $ctrl->create();
            });
        $routeGenerator->addRouteForAction('GET', '/build/convert', function(ServerRequestInterface $request) {
            /** @var DatabaseControler $ctrl */
            $ctrl = $this->container->get(DatabaseControler::class);
            return $ctrl->convert();
            });

####################################
        /** @var $router RouterInterface */
        $router = $this->container->get(RouterInterface::class);
        $router->exchangeRoutes($routeGenerator);

        return $router->process($request, $handler) ;
    }

}
