<?php
namespace Component\View;
use Component\View\ComponentItemAbstract;
use Component\View\ComponentFamilyInterface;

/**
 * Description of ComponentFamilyItemAbstract
 *
 * @author pes2704
 */
abstract class ComponentFamilyItemAbstract extends ComponentItemAbstract implements ComponentFamilyInterface {
    
        // metoda tady je pro ComonentControler->familyDataItem()
    public function createFamilyRouteSegment(string $parentName, string $parentId, string $childName) {
        $viewModel = $this->viewPrototype->getItemViewModel();
        if ($viewModel instanceof FamilyInterface) {
            $viewModel->setFamilyRouteSegment(new FamilyRouteSegment($parentName, $parentId, $childName));
        } else {
            $comCls = get_class($this);
            $cls = FamilyInterface::class;
            $vmCls = get_class($viewModel);
            throw new TypeError("View model komponenty $comCls musí být typu $cls a je typu $vmCls.");
        }
    } 
}
