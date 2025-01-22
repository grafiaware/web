<?php
namespace Component\View;
use Component\View\ComponentFamilyInterface;
use Component\View\ComponentMultiAbstract;
use Component\ViewModel\FamilyInterface;

use Component\ViewModel\RouteSegment\FamilyRouteSegment;
use Component\ViewModel\RouteSegment\FamilyRouteSegmentInterface;
use TypeError;
/**
 * Description of ComponentFamilyListAbstract
 *
 * @author pes2704
 */
abstract class ComponentFamilyMultiAbstract extends ComponentMultiAbstract implements ComponentFamilyInterface {
    
    public function createFamilyRouteSegment(string $prefix, string $parentName, string $parentId, string $childName): FamilyRouteSegmentInterface {
        if (isset($this->multiViewModel)) {
            if ($this->multiViewModel instanceof FamilyInterface) {
                $familyRouteSegment = new FamilyRouteSegment($prefix, $parentName, $parentId, $childName);
                $this->multiViewModel->setFamilyRouteSegment($familyRouteSegment);
            } else {
                $comCls = get_class($this);
                $cls = FamilyInterface::class;
                $vmCls = get_class($this->multiViewModel);
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
