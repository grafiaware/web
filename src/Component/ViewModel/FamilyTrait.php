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

    /**
     * 
     * @param FamilyRouteSegmentInterface $familyRouteSegment
     * @return void
     */
    public function setFamilyRouteSegment(FamilyRouteSegmentInterface $familyRouteSegment): void {
        $this->familyRouteSegment = $familyRouteSegment;
    }
    
    /**
     * 
     * @return FamilyRouteSegmentInterface|null
     */
    public function getFamilyRouteSegment(): ?FamilyRouteSegmentInterface {
        return $this->familyRouteSegment;
    }
    
    public function __clone() {
        $this->familyRouteSegment = clone $this->familyRouteSegment;
    }
    
}
