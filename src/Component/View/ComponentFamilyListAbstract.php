<?php
namespace Component\View;
use Component\View\ComponentFamilyInterface;
use Component\View\ComponentListAbstract;
use Component\ViewModel\FamilyInterface;

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
        if ($listViewModel instanceof FamilyInterface) {
            $listViewModel->setFamilyRouteSegment($familyRouteSegment);
        } else {
            $comCls = get_class($this);
            $cls = FamilyInterface::class;
            $vmCls = get_class($listViewModel);
            throw new TypeError("View model list komponenty $comCls musí být typu $cls a je typu $vmCls.");
        }


        $prototypeViewModel = $this->viewPrototype->getItemViewModel();
        if ($prototypeViewModel instanceof FamilyInterface) {
            $prototypeViewModel->setFamilyRouteSegment($familyRouteSegment);
        }
    } 
}
