<?php
namespace Events\Middleware\Events\Controler;

use FrontControler\PresentationFrontControlerAbstract;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
// enum
use Access\Enum\RoleEnum;
use Access\Enum\AccessActionEnum;

// renderery
use Pes\View\Renderer\ImplodeRenderer;

use Component\View\ComponentItemInterface;
use Component\View\ComponentListInterface;
use Component\View\ComponentFamilyInterface;
use Component\ViewModel\ViewModelInterface;
use Component\ViewModel\ViewModelItemInterface;
use Component\ViewModel\ViewModelFamilyListInterface;
use Component\ViewModel\FamilyInterface;
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
    const ROUTE_PREFIX = "events/v1";


    protected function getActionPermissions(): array {
        
        // je jen jeden ConponentControler, proto mají VISITOR i REPRESENTATIVE stejná oprávnění ke všem komponentům
        return [
            RoleEnum::AUTHENTICATED => [AccessActionEnum::GET => true],
            RoleEnum::ANONYMOUS => [AccessActionEnum::GET => true]
        ];
    }
    
    ### component metody ###############

    /**
     * Vrací response s obsahem určeným ke vložení na místo proměnné v šabloně layoutu. V konfiguraci (ConfigurationCache::layoutController()) 
     * jsou mapy context<->service (tj. jméno proměnné<->jméno služby). Obsah je vygenerován komponentou, komponentu metoda získá z kontejneru. 
     * Jméno služby kontejneru t.j. jméno třídy komponenty získá z konfigurace - z mapy context-service.
     * 
     * @param ServerRequestInterface $request
     * @param string $name
     * @return ResponseInterface
     */
    public function serviceComponent(ServerRequestInterface $request, $name): ResponseInterface {
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
    
    /**
     * Vrací response s obsahem vygenerovaným komponentou. Komponentu metoda získá z kontejneru se jménem služby daným parametrem $name metody. 
     * Parametr metody je získán jako část routy, t.j. URL, proto se jedná o string, který lze zapsat jako část URL. Nelze tedy použít přímo jméno třídy komponenty. 
     * Je nutné v kontejneru vytvořit alias ke třídě komponenty a tuto metodu volat se jménem alias.
     * 
     * @param ServerRequestInterface $request
     * @param string $name Jméno služby konteneru (obvykle alias k jménu tídy komponenty)
     * @return ResponseInterface
     */
    public function component(ServerRequestInterface $request, $name): ResponseInterface {
        if($this->isAllowed(AccessActionEnum::GET)) {
            if($this->container->has($name)) {   // musí být definován alias name => jméno třídy komponentu
                $component = $this->container->get($name);
            } else {
                $component = $this->errorView($request, "Component $name is not defined (configured) or have no alias in container.");                    
            }
        } else {
            $component =  $this->getNonPermittedContentView(AccessActionEnum::GET);
        }
        return $this->createStringOKResponseFromView($component);
    }
    
#### data component metody ##################
    
    /**
     * Vrací response s obsahem vygenerovaným datovou komponentou zobrazující seznam (list). 
     * Komponentu metoda získá z kontejneru se jménem služby daným parametrem $name metody, ke kterému se připojí přípona "List". 
     * Parametr metody je získán jako část routy, t.j. URL, proto se jedná o string, který lze zapsat jako část URL. Nelze tedy použít přímo jméno třídy komponenty. 
     * Je nutné v kontejneru vytvořit alias ke třídě komponenty se jménem složeným z příslušné části routy a řetězce "List".
     * 
     * @param ServerRequestInterface $request
     * @param type $name
     * @return ResponseInterface
     */
    public function dataList(ServerRequestInterface $request, $name): ResponseInterface {
        if($this->isAllowed(AccessActionEnum::GET)) {
            $listName = $name."List";
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

    /**
     * Vrací response s obsahem vygenerovaným datovou komponentou zobrazující jednu položku (item). 
     * Komponentu metoda získá z kontejneru se jménem služby daným parametrem $name metody.
     * Parametr metody je získán jako část routy, t.j. URL, proto se jedná o string, který lze zapsat jako část URL. Nelze tedy použít přímo jméno třídy komponenty. 
     * Je nutné v kontejneru vytvořit alias ke třídě komponenty se jménem odpovídajícím příslušné části routy.
     * 
     * @param ServerRequestInterface $request
     * @param type $name
     * @param type $id
     * @return ResponseInterface
     * @throws LogicException
     */
    public function dataItem(ServerRequestInterface $request, $name, $id): ResponseInterface {
        if($this->isAllowed(AccessActionEnum::GET)) {
            if($this->container->has($name)) {   // musí být definován alias name => jméno třídy komponentu
                $component = $this->container->get($name);
                /** @var ComponentItemInterface $component */
                $viewModel = $component->getItemViewModel();
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
    
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $parentName
     * @param type $parentId
     * @param type $childName
     * @return ResponseInterface
     */
    public function familyDataList(ServerRequestInterface $request, $parentName, $parentId, $childName): ResponseInterface {
        if($this->isAllowed(AccessActionEnum::GET)) {
            $serviceName = $parentName."Family".$childName."List";
            if($this->container->has($serviceName)) {   // musí být definován alias name => jméno třídy komponentu
                $component = $this->container->get($serviceName);
                /** @var ComponentListInterface $component */
                if ($component instanceof ComponentFamilyInterface) {
                    $component->createFamilyRouteSegment(self::ROUTE_PREFIX, $parentName, $parentId, $childName);
                }
            } else {
                $component = $this->errorView($request, "Component $serviceName is not defined (configured) or have no alias in container.");                    
            }
        } else {
            $component =  $this->getNonPermittedContentView(AccessActionEnum::GET);
        }           
        return $this->createStringOKResponseFromView($component);
    }
    
    /**
     * 
     * @param ServerRequestInterface $request
     * @param type $parentName
     * @param type $parentId
     * @param type $childName
     * @param type $id
     * @return ResponseInterface
     */
    public function familyDataItem(ServerRequestInterface $request, $parentName, $parentId, $childName, $id): ResponseInterface {
        if($this->isAllowed(AccessActionEnum::GET)) {
            $serviceName = $parentName."Family".$childName;
            if($this->container->has($serviceName)) {   // musí být definován alias name => jméno třídy komponentu
                $component = $this->container->get($serviceName);
                /** @var ViewModelItemInterface $viewModel */
                $viewModel = $component->getItemViewModel();                
                $viewModel->setItemId($id);
                if ($component instanceof ComponentFamilyInterface) {
                    $component->createFamilyRouteSegment(self::ROUTE_PREFIX, $parentName, $parentId, $childName);
                }
            } else {
                $component = $this->errorView($request, "Component $serviceName is not defined (configured) or have no alias in container.");                    
            }
        } else {
            $component =  $this->getNonPermittedContentView(AccessActionEnum::GET);
        }           
        return $this->createStringOKResponseFromView($component);
    }
    
###################
    private function errorView(ServerRequestInterface $request, $message = '') {
        $view = $this->container->get(View::class);
        $view->setData([Html::tag('div', ['style'=>'display: block;' ], $message)]);
//        $view->setData([Html::tag('div', ['style'=>'display: none;' ], $message)]);
        $view->setRenderer(new ImplodeRenderer());
        return $view;
    }    
}
