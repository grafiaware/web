<?php
namespace FrontControler;

use FrontControler\PresentationFrontControlerAbstract;

use Psr\Http\Message\ServerRequestInterface;

use Access\Enum\AccessActionEnum;

use Pes\View\View;
use Pes\View\Renderer\ImplodeRenderer;
use Pes\Text\Html;

/**
 * Description of ComponentControlerAbstract
 * 
 * 
 *
 * @author pes2704
 */
class ComponentControlerAbstract extends PresentationFrontControlerAbstract implements ComponentControlerInterface {
    /**
     * Vrací response s obsahem vygenerovaným komponentou. Komponentu metoda získá z kontejneru se jménem služby daným parametrem $name metody. 
     * Parametr metody je získán jako část routy, t.j. URL, proto se jedná o string, který lze zapsat jako část URL. Nelze tedy použít přímo jméno třídy komponenty. 
     * Je nutné v kontejneru vytvořit alias ke třídě komponenty a tuto metodu volat se jménem alias.
     * 
     * Používá kontejner injektovaný do FrontControlerAbstract.
     * 
     * @param ServerRequestInterface $request
     * @param type $name
     * @return type
     */
    public function component(ServerRequestInterface $request, $name) {
        if($this->isAllowed(AccessActionEnum::GET)) {
            if($this->container->has($name)) {   // musí být definován alias name => jméno třídy komponentu
                $view = $this->container->get($name);
            } else {
                $view = $this->errorView($request, "Component $name is not defined in container.");                    
            }
        } else {
            $view =  $this->getNonPermittedContentView(AccessActionEnum::GET);
        }
        return $this->createStringOKResponseFromView($view);
    }
    
    ### error view ###
    protected function errorView(ServerRequestInterface $request, $message = '') {
        $view = $this->container->get(View::class);
        $view->setData([Html::tag('div', ['style'=>'display: none;' ], $message)]);
        $view->setRenderer(new ImplodeRenderer());
        return $view;
    }         
    
}
