<?php
namespace Events\Middleware\Events\Controler;

use FrontControler\ComponentControlerAbstract;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
// enum
use Access\Enum\RoleEnum;
use Access\Enum\AccessActionEnum;

// renderery

use Component\View\ComponentItemInterface;
use Component\View\ComponentListInterface;
use Component\View\ComponentFamilyInterface;
use Component\View\ComponentSingleInterface;

use Component\ViewModel\ViewModelInterface;
use Component\ViewModel\ViewModelItemInterface;
use Component\ViewModel\ViewModelFamilyListInterface;
use Component\ViewModel\FamilyInterface;
use Pes\Text\Html;

use Exception;
use LogicException;

//use Pes\Debug\Timer;

/**
 * Description of ComponentControler
 *
 * @author pes2704
 */
class ComponentEventsControler extends ComponentControlerAbstract {

    const LIST_COMPONENT_NAME_POSTFIX = 'List';
    const ROUTE_PREFIX = "events/v1";


    protected function getActionPermissions(): array {
        
        // je jen jeden ConponentControler, proto mají VISITOR i REPRESENTATIVE stejná oprávnění ke všem komponentům
        return [
            RoleEnum::AUTHENTICATED => [AccessActionEnum::GET => true],
            RoleEnum::ANONYMOUS => [AccessActionEnum::GET => true]
        ];
    }
    
    ### component metody dědí z abstract ###############

#### data component metody ##################
    
    /**
     * Vrací response s obsahem vygenerovaným datovou komponentou zobrazující seznam (list). 
     * Komponentu metoda získá z kontejneru se jménem služby daným parametrem $name metody, ke kterému se připojí přípona "List". 
     * Parametr metody je získán jako část routy, t.j. URL, proto se jedná o string, který lze zapsat jako část URL. Nelze tedy použít přímo jméno třídy komponenty. 
     * Je nutné v kontejneru vytvořit alias ke třídě komponenty se jménem složeným z příslušné části routy a řetězce "List".
     * 
     * @param ServerRequestInterface $request
     * @param string $parentName
     * @return ResponseInterface
     */
    public function dataList(ServerRequestInterface $request, $parentName): ResponseInterface {
        if($this->isAllowed(AccessActionEnum::GET)) {
            $listName = $parentName."List";
            if($this->container->has($listName)) {   // musí být definován alias name => jméno třídy komponentu
                $component = $this->container->get($listName);
                if ($component instanceof ComponentSingleInterface) {
                    $component->createSingleRouteSegment(self::ROUTE_PREFIX, $parentName);
                }                
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
                if ($component instanceof ComponentSingleInterface) {
                    $routeSegment = $component->createSingleRouteSegment(self::ROUTE_PREFIX, $name);
                    $routeSegment->setChildId($id);
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
                if ($component instanceof ComponentFamilyInterface) {
                    $routeSegment = $component->createFamilyRouteSegment(self::ROUTE_PREFIX, $parentName, $parentId, $childName);
                    $routeSegment->setChildId($id);
                }
            } else {
                $component = $this->errorView($request, "Component $serviceName is not defined (configured) or have no alias in container.");                    
            }
        } else {
            $component =  $this->getNonPermittedContentView(AccessActionEnum::GET);
        }           
        return $this->createStringOKResponseFromView($component);
    }

}
