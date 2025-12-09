<?php
namespace Red\Middleware\Redactor\Controler;

use FrontControler\ComponentControlerAbstract;

use Psr\Http\Message\ServerRequestInterface;

// konfigurace
use Site\ConfigurationCache;

// enum
use Red\Model\Enum\AuthoredTypeEnum;
use Access\Enum\RoleEnum;
use Access\Enum\AccessActionEnum;

// view model
use Red\Component\ViewModel\Content\StaticItem\StaticItemViewModel;

use Red\Component\View\Content\StaticItem\StaticItemComponent;
use Red\Component\View\Content\StaticItem\StaticItemComponentInterface;

//use Pes\Debug\Timer;

/**
 * Description of ComponentControler
 *
 * @author pes2704
 */
class StaticControler extends ComponentControlerAbstract {

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
