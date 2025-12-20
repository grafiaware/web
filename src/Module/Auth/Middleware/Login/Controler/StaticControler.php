<?php
namespace Auth\Middleware\Login\Controler;

use FrontControler\StaticComponentControlerAbstract;

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
 * Description of StaticControler
 *
 * @author pes2704
 */
class StaticControler extends StaticComponentControlerAbstract {

    protected function getActionPermissions(): array {
        return [
            RoleEnum::AUTHENTICATED => [AccessActionEnum::GET => true],
            RoleEnum::ANONYMOUS => [AccessActionEnum::GET => true]
        ];
    }
    
    ### action metody ###############
    
    public function static(ServerRequestInterface $request, $menuItemId) {
        if($this->isAllowed(AccessActionEnum::GET)) {
            /** @var StaticItemViewModel $viewModel */
            $viewModel = $this->container->get(StaticItemViewModel::class);   
            $viewModel->setMenuItemId($menuItemId);
            /** @var StaticItemComponentInterface $view */
            $view = $this->container->get(StaticItemComponent::class);
        } else {
            $view =  $this->getNonPermittedContentView(AccessActionEnum::GET, AuthoredTypeEnum::PAPER);
        }
        return $this->createStringOKResponseFromView($view);            
    }
}
