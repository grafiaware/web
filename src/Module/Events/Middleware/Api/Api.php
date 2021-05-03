<?php
namespace Events\Middleware\Api;

use Pes\Middleware\AppMiddlewareAbstract;
use Pes\Container\Container;

use Pes\Router\RouteSegmentGenerator;
use Pes\Router\RouterInterface;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Container\{
    ApiContainerConfigurator, HierarchyContainerConfigurator, DbUpgradeContainerConfigurator, LoginContainerConfigurator, MailContainerConfigurator
};

use Module\Events\Middleware\Api\Controller\{EventController, VisitorDataController};

class Api extends AppMiddlewareAbstract implements MiddlewareInterface {

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
                (new ApiContainerConfigurator())->configure(
                    (new HierarchyContainerConfigurator())->configure(
                       (new DbUpgradeContainerConfigurator())->configure(
                            (new Container(
                                    (new LoginContainerConfigurator())->configure(
                                        (new MailContainerConfigurator())->configure(
                                            new Container($this->getApp()->getAppContainer())
                                        )
                                    )
                                )
                            )
                        )
                    )
                );

####################################
        /** @var RouteSegmentGenerator $routeGenerator */
        $routeGenerator = $this->container->get(RouteSegmentGenerator::class);

        $routeGenerator->addRouteForAction('POST', "/event/v1/enroll", function(ServerRequestInterface $request) {
            /** @var EventController $ctrl */
            $ctrl = $this->container->get(EventController::class);
            return $ctrl->enroll($request);
        });
        $routeGenerator->addRouteForAction('POST', '/event/v1/visitor', function(ServerRequestInterface $request) {
            /** @var VisitorDataController $ctrl */
            $ctrl = $this->container->get(VisitorDataController::class);
            return $ctrl->visitor($request);
        });
        $routeGenerator->addRouteForAction('POST', '/event/v1/uploadvisitorfile', function(ServerRequestInterface $request) {
            /** @var VisitorDataController $ctrl */
            $ctrl = $this->container->get(VisitorDataController::class);
            return $ctrl->uploadTxtDocuments($request);
        });
        $routeGenerator->addRouteForAction('POST', '/event/v1/visitorpost', function(ServerRequestInterface $request) {
            /** @var VisitorDataController $ctrl */
            $ctrl = $this->container->get(VisitorDataController::class);
            return $ctrl->postVisitorData($request);
        });
        $routeGenerator->addRouteForAction('POST', '/event/v1/sendvisitorpost', function(ServerRequestInterface $request) {
            /** @var VisitorDataController $ctrl */
            $ctrl = $this->container->get(VisitorDataController::class);
            return $ctrl->sendVisitorDataPost($request);
        });
####################################
        /** @var $router RouterInterface */
        $router = $this->container->get(RouterInterface::class);
        $router->exchangeRoutes($routeGenerator);

        return $router->process($request, $handler) ;
    }
}


