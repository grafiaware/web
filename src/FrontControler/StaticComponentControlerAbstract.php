<?php
namespace FrontControler;

use FrontControler\ComponentControlerAbstract;

use Psr\Http\Message\ServerRequestInterface;

// konfigurace
use Site\ConfigurationCache;

// enum
use Red\Model\Enum\AuthoredTypeEnum;
use Access\Enum\RoleEnum;
use Access\Enum\AccessActionEnum;

// view model
use Component\ViewModel\StaticItemViewModel;

use Component\View\StaticItemComponent;
use Component\View\StaticItemComponentInterface;

//use Pes\Debug\Timer;

/**
 * Description of ComponentControler
 *
 * @author pes2704
 */
abstract class StaticComponentControlerAbstract extends ComponentControlerAbstract {

    protected function getActionPermissions(): array {
        return [
            RoleEnum::AUTHENTICATED => [AccessActionEnum::GET => true],
            RoleEnum::ANONYMOUS => [AccessActionEnum::GET => true]
        ];
    }
    
    ### action metody ###############
    
    public function static(ServerRequestInterface $request, $menuItemId) {
        if($this->isAllowed(AccessActionEnum::GET)) {
            /** @var StaticItemComponentInterface $view */
            $view = $this->container->get(StaticItemComponent::class);
        } else {
            $view =  $this->getNonPermittedContentView(AccessActionEnum::GET, AuthoredTypeEnum::PAPER);
        }
        return $this->createStringOKResponseFromView($view);            
    }
}
