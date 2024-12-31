<?php
namespace Auth\Middleware\Login;

use Auth\Middleware\Login\Controler\AuthStaticControler;
use Auth\Middleware\Login\Controler\ComponentControler;

use Auth\Middleware\Login\Controler\LoginLogoutControler;
use Auth\Middleware\Login\Controler\RegistrationControler;
use Auth\Middleware\Login\Controler\ConfirmControler;
use Auth\Middleware\Login\Controler\PasswordControler;
use Auth\Middleware\Login\Controler\AuthControler;

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
    private $routeGenerator;

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

        /** @var RouteSegmentGenerator $this->routeGenerator */
        $this->routeGenerator = $this->container->get(RouteSegmentGenerator::class);
        
        // TEST MAIL RegistrationControler
        $this->routeGenerator->addRouteForAction('POST', '/auth/v1/testmail', function(ServerRequestInterface $request) {
            /** @var RegistrationControler $ctrl */
            $ctrl = $this->container->get(RegistrationControler::class);
            return $ctrl->testMail($request);
            });        
             
        //  MAIL RegistrationControler  = při nastavení reprezentanta(zástupce) firmy
        $this->routeGenerator->addRouteForAction('POST', '/auth/v1/mailCompletRegistrationRepre',  function(ServerRequestInterface $request) {
            /** @var RegistrationControler $ctrl */
            $ctrl = $this->container->get(RegistrationControler::class);
            return $ctrl->sendMailCompletRegistrationRepre($request);
            });             
             
             
             
        #### AuthStaticControler ####
        $this->routeGenerator->addRouteForAction('GET', '/auth/v1/static/:staticName', function(ServerRequestInterface $request, $staticName) {
            /** @var AuthStaticControler $ctrl */
            $ctrl = $this->container->get(AuthStaticControler::class);
            return $ctrl->static($request, $staticName);
            });     
            
        #### ComponentControler ####
        $this->routeGenerator->addRouteForAction('GET', '/auth/v1/component/:name', function(ServerRequestInterface $request, $name) {
            /** @var ComponentControler $ctrl */
            $ctrl = $this->container->get(ComponentControler::class);
            $comp = $ctrl->component($request, $name);
            return $comp;
            });

        // LoginLogoutControler
        $this->routeGenerator->addRouteForAction('POST', '/auth/v1/logout', function(ServerRequestInterface $request) {
            /** @var LoginLogoutControler $ctrl */
            $ctrl = $this->container->get(LoginLogoutControler::class);
            return $ctrl->logout($request);
            });
        $this->routeGenerator->addRouteForAction('POST', '/auth/v1/login', function(ServerRequestInterface $request) {
            /** @var LoginLogoutControler $ctrl */
            $ctrl = $this->container->get(LoginLogoutControler::class);
            return $ctrl->login($request);
            });       
            
        // RegistrationControler
        $this->routeGenerator->addRouteForAction('POST', '/auth/v1/register', function(ServerRequestInterface $request) {
            /** @var RegistrationControler $ctrl */
            $ctrl = $this->container->get(RegistrationControler::class);
            return $ctrl->register($request);
            });
        $this->routeGenerator->addRouteForAction('POST', '/auth/v1/register1', function(ServerRequestInterface $request) {
            /** @var RegistrationControler $ctrl */
            $ctrl = $this->container->get(RegistrationControler::class);
            return $ctrl->register1($request);
            });
        $this->routeGenerator->addRouteForAction('GET', '/auth/v1/registerapplication/:loginname', function(ServerRequestInterface $request, $loginname) {
            /** @var RegistrationControler $ctrl */
            $ctrl = $this->container->get(RegistrationControler::class);
            return $ctrl->registerapplication($request, $loginname);
            });
            
        // ConfirmControler
        $this->routeGenerator->addRouteForAction('GET', '/auth/v1/confirm/:uid', function(ServerRequestInterface $request, $uid) {
            /** @var ConfirmControler $ctrl */
            $ctrl = $this->container->get(ConfirmControler::class);
            return $ctrl->confirm($request, $uid);
            });
            
        // PasswordControler    
        $this->routeGenerator->addRouteForAction('POST', '/auth/v1/forgottenpassword', function(ServerRequestInterface $request) {
            /** @var PasswordControler $ctrl */
            $ctrl = $this->container->get(PasswordControler::class);
            return $ctrl->forgottenPassword($request);
            });
        $this->routeGenerator->addRouteForAction('POST', '/auth/v1/changepassword', function(ServerRequestInterface $request) {
            /** @var PasswordControler $ctrl */
            $ctrl = $this->container->get(PasswordControler::class);
            return $ctrl->changePassword($request);
            });
       
        //AuthControler        
        $this->routeGenerator->addRouteForAction('POST', '/auth/v1/credentials/:loginnamefk', function(ServerRequestInterface $request, $loginnamefk) {
            /** @var AuthControler $ctrl */
            $ctrl = $this->container->get(AuthControler::class);
            return $ctrl->updateCredentials($request, $loginnamefk);
        });
        
        $this->routeGenerator->addRouteForAction('POST', '/auth/v1/role', function(ServerRequestInterface $request) {
            /** @var AuthControler $ctrl */
            $ctrl = $this->container->get(AuthControler::class);
            return $ctrl->addRole($request);
        });
        $this->routeGenerator->addRouteForAction('POST', '/auth/v1/role/:role', function(ServerRequestInterface $request, $role) {
            /** @var AuthControler $ctrl */
            $ctrl = $this->container->get(AuthControler::class);
            return $ctrl->updateRole($request, $role);
        });
        $this->routeGenerator->addRouteForAction('POST', '/auth/v1/role/:role/remove', function(ServerRequestInterface $request, $role ) {
            /** @var AuthControler $ctrl */
            $ctrl = $this->container->get(AuthControler::class);
            return $ctrl->removeRole($request, $role);
        });             
        
        /** @var $router RouterInterface */
        $router = $this->container->get(RouterInterface::class);
        $router->exchangeRoutes($this->routeGenerator);

        return $router->process($request, $handler) ;
    }
}


