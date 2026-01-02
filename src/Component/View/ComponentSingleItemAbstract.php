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
        if (isset($this->itemViewModel)) {
            if ($this->itemViewModel instanceof SingleInterface) {
                $singleRouteSegment = new SingleRouteSegment($prefix, $parentName);
                $this->itemViewModel->setSingleRouteSegment($singleRouteSegment);
            } else {
                $comCls = get_class($this);
                $cls = SingleInterface::class;
                $vmCls = get_class($this->itemViewModel);
                throw new TypeError("View model list komponenty $comCls musí být typu $cls a je typu $vmCls.");
            }
        }
        return $singleRouteSegment;
    } 
}
