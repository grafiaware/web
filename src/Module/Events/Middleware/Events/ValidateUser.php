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
use Events\Service\ValidateServiceInterface;
use Events\Service\ValidateService;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusFlashRepo;




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
            
            
        //kdyz neni prihlaseny neni v events.login tabulce a ja ok, tak zapsat do events login tabulky    
            
            
        /** @var  ValidateServiceInterface $serviceValidate */
        $serviceValidate = $this->container->get(ValidateService::class);
        $serviceValidate->validateUser($request);    
        
        
        
        
        return $handler->handle($request);
        
    }
        
//        switch ($jakToAsiDopadlo) {
//            case 'validUser':
//               $this->addFlashMessage("Přihlašený je validní uživatel v single_login.",  FlashSeverityEnum::SUCCESS);
//                break;
//            case 'invalidUser':
//                $this->addFlashMessage("Přihlašený není validní uživatel v single_login.",  FlashSeverityEnum::ERROR);
//                break;
//            case 'noUser':
//                $this->addFlashMessage("Nikdo není přihlášen.",  FlashSeverityEnum::ERROR );               
//                break;
//        }       
        
        
        
//            /** @var SynchroControler $synchroControler */
//        $synchroControler = $this->container->get(SynchroControler::class);
//        $synchroControler->validUser($request);
//   
        
      
    
    
    
   
    
    
}