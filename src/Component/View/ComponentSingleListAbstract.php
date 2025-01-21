<?php
namespace Component\View;
use Component\View\ComponentSingleInterface;
use Component\View\ComponentListAbstract;
use Component\ViewModel\SingleInterface;

use Component\ViewModel\RouteSegment\SingleRouteSegment;
use TypeError;
/**
 * Description of ComponentFamilyListAbstract
 *
 * @author pes2704
 */
abstract class ComponentSingleListAbstract extends ComponentListAbstract implements ComponentSingleInterface {
    public function createSingleRouteSegment(string $prefix, string $parentName) {
        $singleRouteSegmant = new SingleRouteSegment($prefix, $parentName);
        $listViewModel = $this->getListViewModel();
        if ($listViewModel instanceof SingleInterface) {
            $listViewModel->setSingleRouteSegment($singleRouteSegmant);
        } else {
            $comCls = get_class($this);
            $cls = SingleInterface::class;
            $vmCls = get_class($listViewModel);
            throw new TypeError("View model list komponenty $comCls musí být typu $cls a je typu $vmCls.");
        }


        $prototypeViewModel = $this->viewPrototype->getItemViewModel();
        if ($prototypeViewModel instanceof SingleInterface) {
            $prototypeViewModel->setSingleRouteSegment($singleRouteSegmant);
        }
    } 
}
