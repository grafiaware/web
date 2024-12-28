<?php
namespace Component\View;
use Component\View\ComponentItemAbstract;
use Component\View\ComponentFamilyInterface;
use Component\ViewModel\FamilyInterface;
use Component\ViewModel\RouteSegment\FamilyRouteSegment;

/**
 * Description of ComponentFamilyItemAbstract
 *
 * @author pes2704
 */
abstract class ComponentFamilyItemAbstract extends ComponentItemAbstract implements ComponentFamilyInterface {
    
        // metoda tady je pro ComonentControler->familyDataItem()
    public function createFamilyRouteSegment(string $prefix, string $parentName, string $parentId, string $childName) {
        if ($this->itemViewModel instanceof FamilyInterface) {
            $this->itemViewModel->setFamilyRouteSegment(new FamilyRouteSegment($prefix, $parentName, $parentId, $childName));
        } else {
            $comCls = get_class($this);
            $cls = FamilyInterface::class;
            $vmCls = get_class($viewModel);
            throw new TypeError("View model komponenty $comCls musí být typu $cls a je typu $vmCls.");
        }
    } 
}
