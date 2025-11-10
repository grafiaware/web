<?php
namespace Auth\Middleware\Login\Controler;

use FrontControler\ComponentControlerAbstract;

use Psr\Http\Message\ServerRequestInterface;

// enum
use Access\Enum\RoleEnum;
use Access\Enum\AccessActionEnum;

//use Pes\Debug\Timer;

/**
 * Description of ComponentControler
 *
 * @author pes2704
 */
class ComponentControler extends ComponentControlerAbstract {

    protected function getActionPermissions(): array {
        
        // prakticky má oprávnění každý - "GET" controler 
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
