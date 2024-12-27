<?php
namespace Component\ViewModel;

use Component\ViewModel\RouteSegment\FamilyRouteSegmentInterface;

/**
 *
 * @author pes2704
 */
trait FamilyTrait {
    
    /**
     * 
     * @var FamilyRouteSegmentInterface
     */
    private $familyRouteSegment;

    public function setFamilyRouteSegment(FamilyRouteSegmentInterface $familyRouteSegment): void {
        $this->familyRouteSegment = $familyRouteSegment;
    }
    
    public function getFamilyRouteSegment(): ?FamilyRouteSegmentInterface {
        return $this->familyRouteSegment;
    }
    
}
