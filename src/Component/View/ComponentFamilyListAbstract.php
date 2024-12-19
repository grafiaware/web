<?php
namespace Component\View;
use Component\View\ComponentFamilyInterface;
use Component\View\ComponentListAbstract;
use Component\ViewModel\ViewModelFamilyListInterface;
use Component\ViewModel\ViewModelFamilyItemInterface;

use Component\ViewModel\RouteSegment\FamilyRouteSegment;
use TypeError;
/**
 * Description of ComponentFamilyListAbstract
 *
 * @author pes2704
 */
abstract class ComponentFamilyListAbstract extends ComponentListAbstract implements ComponentFamilyInterface {
    
    public function createFamilyRouteSegment(string $parentName, string $parentId, string $childName) {
        $familyRouteSegment = new FamilyRouteSegment($parentName, $parentId, $childName);
        $listViewModel = $this->getListViewModel();
        if ($listViewModel instanceof ViewModelFamilyListInterface) {
            $listViewModel->setFamilyRouteSegment($familyRouteSegment);
        } else {
            $comCls = get_class($this);
            $cls = ViewModelFamilyListInterface::class;
            $vmCls = get_class($listViewModel);
            throw new TypeError("View model list komponenty $comCls musí být typu $cls a je typu $vmCls.");
        }


        $prototypeViewModel = $this->viewPrototype->getItemViewModel();
        if ($prototypeViewModel instanceof ViewModelFamilyItemInterface) {
            $prototypeViewModel->setFamilyRouteSegment($familyRouteSegment);
        } else {
            $comCls = get_class($this);
            $cls = ViewModelFamilyItemInterface::class;
            $vmCls = get_class($prototypeViewModel);
            throw new TypeError("View model prototype komponenty $comCls musí být typu $cls a je typu $vmCls.");
        }
    } 
}
