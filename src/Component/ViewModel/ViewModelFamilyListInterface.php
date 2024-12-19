<?php
namespace Component\ViewModel;

use Component\ViewModel\ViewModelListInterface;
use Component\ViewModel\RouteSegment\FamilyRouteSegmentInterface;

/**
 *
 * @author pes2704
 */
interface ViewModelFamilyListInterface extends ViewModelListInterface {
   public function setFamilyRouteSegment(FamilyRouteSegmentInterface $familyRouteSegment): void;    
}
