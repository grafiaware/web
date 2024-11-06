<?php
namespace Consent\Middleware\ConsentLogger;

use Auth\Middleware\Login\Controler\LoginLogoutControler;
use Auth\Middleware\Login\Controler\AuthStaticControler;
use Auth\Middleware\Login\Controler\RegistrationControler;
use Auth\Middleware\Login\Controler\ConfirmControler;
use Auth\Middleware\Login\Controler\PasswordControler;
use Auth\Middleware\Login\Controler\AuthControler;

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

        // LoginLogoutControler
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


