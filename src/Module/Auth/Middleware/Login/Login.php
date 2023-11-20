<?php
namespace Auth\Middleware\Login;

use Auth\Middleware\Login\Controller\LoginLogoutController;
use Auth\Middleware\Login\Controller\AuthStaticControler;
use Auth\Middleware\Login\Controller\RegistrationController;
use Auth\Middleware\Login\Controller\ConfirmController;
use Auth\Middleware\Login\Controller\PasswordController;
use Auth\Middleware\Login\Controller\AuthController;

use Pes\Middleware\AppMiddlewareAbstract;
use Pes\Container\Container;

use Container\AuthContainerConfigurator;
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

    /**
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return Response
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {

        $this->container =
            (new AuthContainerConfigurator())->configure(
                (new AuthDbContainerConfigurator())->configure(
                    (new MailContainerConfigurator())->configure(
                        (new Container($this->getApp()->getAppContainer()))
                    )
                )
            );

        /** @var RouteSegmentGenerator $routeGenerator */
        $routeGenerator = $this->container->get(RouteSegmentGenerator::class);

        // LoginLogoutController
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

        // AuthStaticControler
        $routeGenerator->addRouteForAction('GET', '/auth/v1/static/:staticName', function(ServerRequestInterface $request, $staticName) {
            /** @var AuthStaticControler $ctrl */
            $ctrl = $this->container->get(AuthStaticControler::class);
            return $ctrl->static($request, $staticName);
            });            
            
        // RegistrationController
        $routeGenerator->addRouteForAction('POST', '/auth/v1/register', function(ServerRequestInterface $request) {
            /** @var RegistrationController $ctrl */
            $ctrl = $this->container->get(RegistrationController::class);
            return $ctrl->register($request);
            });
        $routeGenerator->addRouteForAction('POST', '/auth/v1/register1', function(ServerRequestInterface $request) {
            /** @var RegistrationController $ctrl */
            $ctrl = $this->container->get(RegistrationController::class);
            return $ctrl->register1($request);
            });
        $routeGenerator->addRouteForAction('GET', '/auth/v1/registerapplication/:loginname', function(ServerRequestInterface $request, $loginname) {
            /** @var RegistrationController $ctrl */
            $ctrl = $this->container->get(RegistrationController::class);
            return $ctrl->registerapplication($request, $loginname);
            });
            
        // ConfirmController
        $routeGenerator->addRouteForAction('GET', '/auth/v1/confirm/:uid', function(ServerRequestInterface $request, $uid) {
            /** @var ConfirmController $ctrl */
            $ctrl = $this->container->get(ConfirmController::class);
            return $ctrl->confirm($request, $uid);
            });
            
        // PasswordController    
        $routeGenerator->addRouteForAction('POST', '/auth/v1/forgottenpassword', function(ServerRequestInterface $request) {
            /** @var PasswordController $ctrl */
            $ctrl = $this->container->get(PasswordController::class);
            return $ctrl->forgottenPassword($request);
            });
        $routeGenerator->addRouteForAction('POST', '/auth/v1/changepassword', function(ServerRequestInterface $request) {
            /** @var PasswordController $ctrl */
            $ctrl = $this->container->get(PasswordController::class);
            return $ctrl->changePassword($request);
            });
       
        //AuthController        
        $routeGenerator->addRouteForAction('POST', '/auth/v1/credentials/:loginnamefk', function(ServerRequestInterface $request, $loginnamefk) {
            /** @var AuthController $ctrl */
            $ctrl = $this->container->get(AuthController::class);
            return $ctrl->updateCredentials($request, $loginnamefk);
        });
        
        $routeGenerator->addRouteForAction('POST', '/auth/v1/role', function(ServerRequestInterface $request) {
            /** @var AuthController $ctrl */
            $ctrl = $this->container->get(AuthController::class);
            return $ctrl->addRole($request);
        });
        $routeGenerator->addRouteForAction('POST', '/auth/v1/role/:role', function(ServerRequestInterface $request, $role) {
            /** @var AuthController $ctrl */
            $ctrl = $this->container->get(AuthController::class);
            return $ctrl->updateRole($request, $role);
        });
        $routeGenerator->addRouteForAction('POST', '/auth/v1/role/:role/remove', function(ServerRequestInterface $request, $role ) {
            /** @var AuthController $ctrl */
            $ctrl = $this->container->get(AuthController::class);
            return $ctrl->removeRole($request, $role);
        });             
        
        /** @var $router RouterInterface */
        $router = $this->container->get(RouterInterface::class);
        $router->exchangeRoutes($routeGenerator);

        return $router->process($request, $handler) ;
    }
}


