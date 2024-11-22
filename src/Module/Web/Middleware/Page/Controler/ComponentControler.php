<?php
namespace Web\Middleware\Page\Controler;

use FrontControler\PresentationFrontControlerAbstract;

use Psr\Http\Message\ServerRequestInterface;

// konfigurace
use Site\ConfigurationCache;

use Access\Enum\RoleEnum;
use Access\Enum\AccessActionEnum;

// renderery
use Pes\View\Renderer\ImplodeRenderer;

####################

use Pes\Text\Html;

####################
//use Pes\Debug\Timer;
use Pes\View\View;

/**
 * Description of ComponentControler
 *
 * @author pes2704
 */
class ComponentControler extends PresentationFrontControlerAbstract {

    protected function getActionPermissions(): array {
        
        // je jen jeden ConponentControler, proto mají VISITOR i REPRESENTATIVE stejná oprávnění ke všem komponentům
        return [
            RoleEnum::AUTHENTICATED => [AccessActionEnum::GET => true],
            RoleEnum::ANONYMOUS => [AccessActionEnum::GET => true]
        ];
    }
    
    ### action metody ###############

    public function component(ServerRequestInterface $request, $name) {
        if($this->isAllowed(AccessActionEnum::GET)) {
            if($this->container->has($name)) {   // musí být definován alias name => jméno třídy komponentu
                $view = $this->container->get($name);
            } else {
                $view = $this->errorView($request, "Component $name is not defined (configured) or have no alias in container.");                    
            }
        } else {
            $view =  $this->getNonPermittedContentView(AccessActionEnum::GET);
        }
        return $this->createStringOKResponseFromView($view);
    }
    
###################
    private function errorView(ServerRequestInterface $request, $message = '') {
        $view = $this->container->get(View::class);
        $view->setData([Html::tag('div', ['style'=>'display: none;' ], $message)]);
        $view->setRenderer(new ImplodeRenderer());
        return $view;
    }    
}
