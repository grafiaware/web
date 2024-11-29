<?php
namespace Events\Middleware\Events\Controler;

use FrontControler\PresentationFrontControlerAbstract;

use Psr\Http\Message\ServerRequestInterface;

// enum
use Access\Enum\RoleEnum;
use Access\Enum\AccessActionEnum;

// renderery
use Pes\View\Renderer\ImplodeRenderer;

use Component\View\ComponentCompositeInterface;
use Component\View\ComponentCollectionInterface;
use Component\ViewModel\ViewModelInterface;
use Component\ViewModel\ViewModelItemInterface;
use Component\ViewModel\ViewModelChildListInterface;

use Pes\Text\Html;

use LogicException;

####################
//use Pes\Debug\Timer;
use Pes\View\View;

/**
 * Description of ComponentControler
 *
 * @author pes2704
 */
class ComponentControler extends PresentationFrontControlerAbstract {

    const LIST_COMPONENT_NAME_POSTFIX = 'List';
    
    protected function getActionPermissions(): array {
        
        // je jen jeden ConponentControler, proto mají VISITOR i REPRESENTATIVE stejná oprávnění ke všem komponentům
        return [
            RoleEnum::AUTHENTICATED => [AccessActionEnum::GET => true],
            RoleEnum::ANONYMOUS => [AccessActionEnum::GET => true]
        ];
    }
    
    ### action metody ###############

    public function serviceComponent(ServerRequestInterface $request, $name) {
        if($this->isAllowed(AccessActionEnum::GET)) {
            if (array_key_exists($name, ConfigurationCache::layoutController()['contextServiceMap'])) {
                $service = reset(ConfigurationCache::layoutController()['contextServiceMap'][$name]) ?? null;
                if($this->container->has($service)) {
                    $view = $this->container->get($service);
                } else {
                    $view = $this->errorView($request, "Component $service is not defined (configured) in container.");                    
                }
            } else {
                $view = $this->errorView($request, "Component $name undefined in configuration of context service map.");
            }
        } else {
            $view =  $this->getNonPermittedContentView(AccessActionEnum::GET);
        }
        return $this->createStringOKResponseFromView($view);
    }

    public function componentList(ServerRequestInterface $request, $name) {
        $listName = $name."List";
        if($this->isAllowed(AccessActionEnum::GET)) {
            if($this->container->has($listName)) {   // musí být definován alias name => jméno třídy komponentu
                $component = $this->container->get($listName);
            } else {
                $component = $this->errorView($request, "Component $listName is not defined (configured) or have no alias in container.");                    
            }
        } else {
            $component =  $this->getNonPermittedContentView(AccessActionEnum::GET);
        }
        return $this->createStringOKResponseFromView($component);
    }

    public function component(ServerRequestInterface $request, $name, $id) {
        if($this->isAllowed(AccessActionEnum::GET)) {
            if($this->container->has($name)) {   // musí být definován alias name => jméno třídy komponentu
                $component = $this->container->get($name);
                /** @var ComponentCompositeInterface $component */
                $viewModel = $component->getData();
                /** @var ViewModelInterface $viewModel */
                if ($viewModel instanceof ViewModelItemInterface) {
                    $viewModel->setItemId($id);
                } else {
                    throw new LogicException("ViewModel komponenty ". get_class($component)." není požadovaného typu ViewModelItemInterface");
                }
            } else {
                $component = $this->errorView($request, "Component $name is not defined (configured) or have no alias in container.");                    
            }
        } else {
            $component =  $this->getNonPermittedContentView(AccessActionEnum::GET);
        }           
        return $this->createStringOKResponseFromView($component);
    }

    public function subComponentList(ServerRequestInterface $request, $name, $parentId) {
        $listName = $name."List";
        if($this->isAllowed(AccessActionEnum::GET)) {
            if($this->container->has($listName)) {   // musí být definován alias name => jméno třídy komponentu
                $component = $this->container->get($listName);
                /** @var ComponentCompositeInterface $component */
                $viewModel = $component->getData();
                /** @var ViewModelInterface $viewModel */
                if ($viewModel instanceof ViewModelChildListInterface) {
                    $viewModel->setParentId($parentId);
                }
            } else {
                $component = $this->errorView($request, "Component $name is not defined (configured) or have no alias in container.");                    
            }
        } else {
            $component =  $this->getNonPermittedContentView(AccessActionEnum::GET);
        }           
        return $this->createStringOKResponseFromView($component);
    }
    
###################
    private function errorView(ServerRequestInterface $request, $message = '') {
        $view = $this->container->get(View::class);
        $view->setData([Html::tag('div', ['style'=>'display: none;' ], $message)]);
        $view->setRenderer(new ImplodeRenderer());
        return $view;
    }    
}
