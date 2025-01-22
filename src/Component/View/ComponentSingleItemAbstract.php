<?php
namespace Component\View;
use Component\View\ComponentSingleInterface;
use Component\View\ComponentItemAbstract;
use Component\ViewModel\SingleInterface;

use Component\ViewModel\RouteSegment\SingleRouteSegment;
use Component\ViewModel\RouteSegment\SingleRouteSegmentInterface;
use TypeError;
/**
 * Description of ComponentFamilyListAbstract
 *
 * @author pes2704
 */
abstract class ComponentSingleItemAbstract extends ComponentItemAbstract implements ComponentSingleInterface {
    public function createSingleRouteSegment(string $prefix, string $parentName): SingleRouteSegmentInterface {
        $singleRouteSegment = new SingleRouteSegment($prefix, $parentName);
        if (isset($this->itemViewModel)) {
            if ($this->itemViewModel instanceof SingleInterface) {
                $this->itemViewModel->setSingleRouteSegment($singleRouteSegment);
            } else {
                $comCls = get_class($this);
                $cls = SingleInterface::class;
                $vmCls = get_class($listViewModel);
                throw new TypeError("View model list komponenty $comCls musí být typu $cls a je typu $vmCls.");
            }
        }
        return $singleRouteSegment;
    } 
}
