<?php
namespace Component\View;
use Component\View\ComponentItemAbstract;
use Component\View\ComponentFamilyInterface;
use Component\ViewModel\FamilyInterface;
use Component\ViewModel\RouteSegment\FamilyRouteSegment;
use Component\ViewModel\RouteSegment\FamilyRouteSegmentInterface;
/**
 * Description of ComponentFamilyItemAbstract
 *
 * @author pes2704
 */
abstract class ComponentFamilyItemAbstract extends ComponentItemAbstract implements ComponentFamilyInterface {
    
        // metoda tady je pro ComonentControler->familyDataItem()
    public function createFamilyRouteSegment(string $prefix, string $parentName, string $parentId, string $childName): FamilyRouteSegmentInterface {
        if (isset($this->itemViewModel)) {
            if ($this->itemViewModel instanceof FamilyInterface) {
                $familyRouteSegment = new FamilyRouteSegment($prefix, $parentName, $parentId, $childName);
                $this->itemViewModel->setFamilyRouteSegment($familyRouteSegment);
            } else {
                $comCls = get_class($this);
                $cls = FamilyInterface::class;
                $vmCls = get_class($this->itemViewModel);
                throw new TypeError("View model komponenty $comCls musí být typu $cls a je typu $vmCls.");
            }
        }
        return $familyRouteSegment;
    } 
}
