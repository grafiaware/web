<?php
namespace Component\ViewModel;
use Component\ViewModel\ViewModelFamilyInterface;
use Component\ViewModel\ViewModelItemInterface;

use Component\ViewModel\RouteSegment\FamilyRouteSegmentInterface;

/**
 *
 * @author pes2704
 */
interface ViewModelFamilyItemInterface extends ViewModelItemInterface {
    public function setFamilyRouteSegment(FamilyRouteSegmentInterface $familyRouteSegment): void;

}
