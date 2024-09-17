<?php
namespace Consent\Middleware\ConsentLogger;

use Auth\Middleware\Login\Controller\LoginLogoutController;
use Auth\Middleware\Login\Controller\AuthStaticControler;
use Auth\Middleware\Login\Controller\RegistrationController;
use Auth\Middleware\Login\Controller\ConfirmController;
use Auth\Middleware\Login\Controller\PasswordController;
use Auth\Middleware\Login\Controller\AuthController;

use Pes\Middleware\AppMiddlewareAbstract;
use Pes\Container\Container;

use Container\ConsentContainerConfigurator;

use Pes\Router\RouteSegmentGenerator;
use Pes\Router\RouterInterface;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Consent\Middleware\ConsentLogger\Controler\LogControler;

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

        // LoginLogoutController
        $routeGenerator->addRouteForAction('POST', '/consent/v1/log', function(ServerRequestInterface $request) {
            /** @var LogControler $ctrl */
            $ctrl = $this->container->get(LogControler::class);
            return $ctrl->logConsent($request);
            });
        
        /** @var $router RouterInterface */
        $router = $this->container->get(RouterInterface::class);
        $router->exchangeRoutes($routeGenerator);

        return $router->process($request, $handler) ;
    }
}


