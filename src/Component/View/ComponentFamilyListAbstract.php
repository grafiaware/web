<?php
namespace Component\View;
use Component\View\ComponentFamilyInterface;
use Component\View\ComponentListAbstract;
use Component\ViewModel\FamilyInterface;

use Component\ViewModel\RouteSegment\FamilyRouteSegment;
use Component\ViewModel\RouteSegment\FamilyRouteSegmentInterface;
use TypeError;
/**
 * Description of ComponentFamilyListAbstract
 *
 * @author pes2704
 */
abstract class ComponentFamilyListAbstract extends ComponentListAbstract implements ComponentFamilyInterface {
    
    public function createFamilyRouteSegment(string $prefix, string $parentName, string $parentId, string $childName): FamilyRouteSegmentInterface {
        $familyRouteSegment = new FamilyRouteSegment($prefix, $parentName, $parentId, $childName);
        if(isset($this->listViewModel)) {
            if ($this->listViewModel instanceof FamilyInterface) {
                $this->listViewModel->setFamilyRouteSegment($familyRouteSegment);
            } else {
                $comCls = get_class($this);
                $cls = FamilyInterface::class;
                $vmCls = get_class($this->listViewModel);
                throw new TypeError("View model list komponenty $comCls musí být typu $cls a je typu $vmCls.");
            }
        }

        $prototypeViewModel = $this->viewPrototype->getItemViewModel();
        if ($prototypeViewModel instanceof FamilyInterface) {
            $prototypeViewModel->setFamilyRouteSegment($familyRouteSegment);
        }
        return $familyRouteSegment;
    } 
}
