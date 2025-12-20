<?php
namespace Events\Middleware\Events;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

use Pes\Middleware\AppMiddlewareAbstract;

use Pes\Container\Container;

use Container\StaticItemContainerConfigurator;
use Container\EventsContainerConfigurator;
use Container\EventsModelContainerConfigurator;
use Container\EventsDbContainerConfigurator;
use Container\MailContainerConfigurator;

use Events\Middleware\Events\Controler\LoginSyncControler;

/**
 * Description of EventsAccess
 *
 * @author pes2704
 */
class EventsLoginSync extends AppMiddlewareAbstract implements MiddlewareInterface {
    
    private $container;    
    
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        
        $this->container =
            (new EventsContainerConfigurator())->configure(
                (new StaticItemContainerConfigurator())->configure(                    
                    (new EventsModelContainerConfigurator())->configure(
                        (new EventsDbContainerConfigurator())->configure(
                            (new MailContainerConfigurator())->configure(
                                new Container($this->getApp()->getAppContainer())
                            )
                        )
                    )
                )
            );        
        #### kontejner pro Events #####
        #
            // Nový kontejner nastaví jako kontejner aplikace - pro middleware Events
            $this->getApp()->setAppContainer($this->container);
        #
        ###############################       
            
        $loginSyncControler = $this->container->get(LoginSyncControler::class);
        /** @var LoginSyncControler $loginSyncControler */
        $loginSyncControler->actualizeLogin();
        return $handler->handle($request);
    }
}