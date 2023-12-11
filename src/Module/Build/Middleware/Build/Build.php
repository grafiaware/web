<?php
namespace Build\Middleware\Build;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

use Pes\Container\Container;
use Pes\Middleware\AppMiddlewareAbstract;
use Pes\Router\RouteSegmentGenerator;
use Pes\Router\RouterInterface;

use Container\{
    BuildContainerConfigurator, DbUpgradeContainerConfigurator, AuthContainerConfigurator, AuthDbContainerConfigurator
};

use Build\Middleware\Build\Controller\ControlPanelController;
use Build\Middleware\Build\Controller\DatabaseController;

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
//                            (new AuthContainerConfigurator())->configure(
                                (new AuthDbContainerConfigurator())->configure(
                                    new Container($this->getApp()->getAppContainer())
                                )
//                            )
                        )
                    )
                )
            );

####################################
        /** @var RouteSegmentGenerator $routeGenerator */
        $routeGenerator = $this->container->get(RouteSegmentGenerator::class);
        
        #### ControlPanelController ####
        $routeGenerator->addRouteForAction('GET', '/build', function(ServerRequestInterface $request) {
            /** @var ControlPanelController $ctrl */
            $ctrl = $this->container->get(ControlPanelController::class);
            return $ctrl->panel($request);
            });
            
        #### DatabaseController ####
        $routeGenerator->addRouteForAction('POST', '/build/listconfig', function(ServerRequestInterface $request) {
            /** @var DatabaseController $ctrl */
            $ctrl = $this->container->get(DatabaseController::class);
            return $ctrl->listConfig($request);
            });

        $routeGenerator->addRouteForAction('POST', '/build/createdb', function(ServerRequestInterface $request) {
            /** @var DatabaseController $ctrl */
            $ctrl = $this->container->get(DatabaseController::class);
            return $ctrl->createDb();
            });
        $routeGenerator->addRouteForAction('POST', '/build/dropdb', function(ServerRequestInterface $request) {
            /** @var DatabaseController $ctrl */
            $ctrl = $this->container->get(DatabaseController::class);
            return $ctrl->dropDb();
            });
        $routeGenerator->addRouteForAction('POST', '/build/createusers', function(ServerRequestInterface $request) {
            /** @var DatabaseController $ctrl */
            $ctrl = $this->container->get(DatabaseController::class);
            return $ctrl->createUsers();
            });
        $routeGenerator->addRouteForAction('POST', '/build/dropusers', function(ServerRequestInterface $request) {
            /** @var DatabaseController $ctrl */
            $ctrl = $this->container->get(DatabaseController::class);
            return $ctrl->dropUsers();
            });
        $routeGenerator->addRouteForAction('POST', '/build/droptables', function(ServerRequestInterface $request) {
            /** @var DatabaseController $ctrl */
            $ctrl = $this->container->get(DatabaseController::class);
            return $ctrl->dropTables();
            });
        $routeGenerator->addRouteForAction('POST', '/build/make', function(ServerRequestInterface $request) {
            /** @var DatabaseController $ctrl */
            $ctrl = $this->container->get(DatabaseController::class);
            return $ctrl->make();
            });
        $routeGenerator->addRouteForAction('POST', '/build/convert', function(ServerRequestInterface $request) {
            /** @var DatabaseController $ctrl */
            $ctrl = $this->container->get(DatabaseController::class);
            return $ctrl->convert();
            });
####################################
        /** @var RouterInterface $router */
        $router = $this->container->get(RouterInterface::class);
        $router->exchangeRoutes($routeGenerator);

        return $router->process($request, $handler) ;
    }

}
