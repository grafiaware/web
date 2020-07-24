<?php
namespace Middleware\Login;

use Middleware\Login\Controller\LoginLogoutController;

use Pes\Middleware\AppMiddlewareAbstract;
use Pes\Container\Container;

use Container\LoginContainerConfigurator;
use Container\DbOldContainerConfigurator;

use Model\Repository\StatusSecurityRepo;

use StatusManager\StatusSecurityManagerInterface;

use Pes\Router\RouteSegmentGenerator;
use Pes\Router\RouterInterface;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;


class Login extends AppMiddlewareAbstract implements MiddlewareInterface {

    private $container;

    /**
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return Response
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {

        $this->container =
            (new LoginContainerConfigurator())->configure(
                (new DbOldContainerConfigurator())->configure(
                    (new Container($this->getApp()->getAppContainer()))
                )
            );

        /** @var RouteSegmentGenerator $routeGenerator */
        $routeGenerator = $this->container->get(RouteSegmentGenerator::class);

        $routeGenerator->addRouteForAction('POST', '/auth/v1/logout', function(ServerRequestInterface $request) {
            /** @var LoginLogoutController $ctrl */
            $ctrl = $this->container->get(LoginLogoutController::class);
            return $ctrl->logout($request);
            });
        $routeGenerator->addRouteForAction('POST', '/auth/v1/login', function(ServerRequestInterface $request) {
            /** @var LoginLogoutController $ctrl */
            $ctrl = $this->container->get(LoginLogoutController::class);
            return $ctrl->login($request);
            });

        /** @var $router RouterInterface */
        $router = $this->container->get(RouterInterface::class);
        $router->exchangeRoutes($routeGenerator);


        return $router->process($request, $handler) ;
    }
}


