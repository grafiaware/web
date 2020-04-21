<?php
namespace Middleware\Login;

use Middleware\Login\Controller\LoginLogoutController;

use Pes\Middleware\AppMiddlewareAbstract;
use Pes\Container\Container;

use Container\LoginContainerConfigurator;

use Pes\Action\Registry;
use Pes\Action\Action;
use Pes\Action\Resource;
use Pes\Router\RouterInterface;
use Pes\Router\MethodEnum;
use Pes\Router\UrlPatternValidator;

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

        $this->request = $request;

        $this->container =
            (new LoginContainerConfigurator())->configure(
                    (new Container($this->getApp()->getAppContainer()))
                );

        $registry = new Registry(new MethodEnum(), new UrlPatternValidator());

        $registry->register(new Action(new Resource('POST', '/auth/v1/logout'), function(ServerRequestInterface $request) {
            /** @var LoginLogoutController $ctrl */
            $ctrl = $this->container->get(LoginLogoutController::class);
            return $ctrl->logout(request);
        }));
        $registry->register(new Action(new Resource('POST', '/auth/v1/login'), function(ServerRequestInterface $request) {
            /** @var LoginLogoutController $ctrl */
            $ctrl = $this->container->get(LoginLogoutController::class);
            return $ctrl->login(request);
        }));

        /** @var $router RouterInterface */
        $router = $this->container->get(RouterInterface::class);

        /** @var Action $action */
        foreach ($registry as $action) {
            $router->addRoute($action->getResource(), $action->getActionCallable());
        }

        return $router->process($request, $handler) ;
    }
}


