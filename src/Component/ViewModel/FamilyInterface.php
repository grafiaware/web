<?php
namespace Component\ViewModel;
use Component\ViewModel\ViewModelFamilyInterface;
use Component\ViewModel\ViewModelItemInterface;

use Component\ViewModel\RouteSegment\FamilyRouteSegmentInterface;

/**
 *
 * @author pes2704
 */
interface FamilyInterface {
    
    const CARDINALITY_0_1 = "zero or one";
    const CARDINALITY_1_1 = "exactly one";
    const CARDINALITY_0_N = "zero or more";
    const CARDINALITY_1_N = "one or more";
    
    public function setFamilyRouteSegment(FamilyRouteSegmentInterface $familyRouteSegment): void;
    public function getFamilyRouteSegment(): ?FamilyRouteSegmentInterface;
}
