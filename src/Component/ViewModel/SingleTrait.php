<?php
namespace Component\ViewModel;

use Component\ViewModel\RouteSegment\SingleRouteSegmentInterface;

/**
 *
 * @author pes2704
 */
trait SingleTrait {
    
    /**
     * 
     * @var SingleRouteSegmentInterface
     */
    private $singleRouteSegment;

    /**
     * 
     * @param SingleRouteSegmentInterface $singleRouteSegment
     * @return void
     */
    public function setSingleRouteSegment(FamilyRouteSegmentInterface $singleRouteSegment): void {
        $this->singleRouteSegment = $singleRouteSegment;
    }
    
    /**
     * 
     * @return SingleRouteSegmentInterface|null
     */
    public function getSingleRouteSegment(): ?FamilyRouteSegmentInterface {
        return $this->singleRouteSegment;
    }

}
