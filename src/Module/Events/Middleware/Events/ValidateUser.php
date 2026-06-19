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
use Events\Middleware\Events\Controler\SynchroControler;
use Events\Service\ValidatingServiceInterface;
use Events\Service\ValidatingService;



use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusFlashRepo;

use Status\Model\Entity\Security;
use Status\Model\Entity\SecurityInterface;




/**
 * Description of EventsAccess
 *
 * @author pes2704
 */
class ValidateUser extends AppMiddlewareAbstract implements MiddlewareInterface {
    
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
            // Nový kontejner nastaví jako kontejner aplikace - pro middleware Events
            $this->getApp()->setAppContainer($this->container);     
        ###############################             
          
//      todle tady nebude      
//        $loginSyncControler = $this->container->get(LoginSyncControler::class);
//        /** @var LoginSyncControler $loginSyncControler */
//        $loginSyncControler->actualizeLogin();
//        //----------------
                    
            
            /** @var  ValidatingServiceInterface $serviceValidating */
            $serviceValidating = $this->container->get(ValidatingService::class);
            $serviceValidating->validateUser($request);           
        
               
        return $handler->handle($request);
        
    }
                          
    
}
