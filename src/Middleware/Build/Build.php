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

use Middleware\Build\Controller\DatabaseController;

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
        $routeGenerator->addRouteForAction('GET', '/build/createdb', function(ServerRequestInterface $request) {
            /** @var DatabaseController $ctrl */
            $ctrl = $this->container->get(DatabaseController::class);
            return $ctrl->createDb();
            });
        $routeGenerator->addRouteForAction('GET', '/build/dropdb', function(ServerRequestInterface $request) {
            /** @var DatabaseController $ctrl */
            $ctrl = $this->container->get(DatabaseController::class);
            return $ctrl->dropDb();
            });
        $routeGenerator->addRouteForAction('GET', '/build/createusers', function(ServerRequestInterface $request) {
            /** @var DatabaseController $ctrl */
            $ctrl = $this->container->get(DatabaseController::class);
            return $ctrl->createUsers();
            });
        $routeGenerator->addRouteForAction('GET', '/build/dropusers', function(ServerRequestInterface $request) {
            /** @var DatabaseController $ctrl */
            $ctrl = $this->container->get(DatabaseController::class);
            return $ctrl->dropUsers();
            });
        $routeGenerator->addRouteForAction('GET', '/build/droptables', function(ServerRequestInterface $request) {
            /** @var DatabaseController $ctrl */
            $ctrl = $this->container->get(DatabaseController::class);
            return $ctrl->dropTables();
            });
        $routeGenerator->addRouteForAction('GET', '/build/make', function(ServerRequestInterface $request) {
            /** @var DatabaseController $ctrl */
            $ctrl = $this->container->get(DatabaseController::class);
            return $ctrl->make();
            });
        $routeGenerator->addRouteForAction('GET', '/build/convert', function(ServerRequestInterface $request) {
            /** @var DatabaseController $ctrl */
            $ctrl = $this->container->get(DatabaseController::class);
            return $ctrl->convert();
            });
####################################
        /** @var $router RouterInterface */
        $router = $this->container->get(RouterInterface::class);
        $router->exchangeRoutes($routeGenerator);

        return $router->process($request, $handler) ;
    }

}
