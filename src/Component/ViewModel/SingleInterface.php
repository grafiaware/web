<?php
namespace Component\ViewModel;

use Component\ViewModel\RouteSegment\SingleRouteSegmentInterface;

/**
 *
 * @author pes2704
 */
interface SingleInterface {
    
    const CARDINALITY_0_1 = "zero or one";
    const CARDINALITY_1_1 = "exactly one";
    const CARDINALITY_0_N = "zero or more";
    const CARDINALITY_1_N = "one or more";
    
    public function setSingleRouteSegment(SingleRouteSegmentInterface $singleRouteSegment): void;
    public function getSingleRouteSegment(): ?SingleRouteSegmentInterface;
}
