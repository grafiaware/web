<?php
namespace Component\ViewModel;

use Component\ViewModel\ViewModelFamilyListInterface;
use Component\ViewModel\ViewModelAbstract;
use Component\ViewModel\RouteSegment\FamilyRouteSegmentInterface;

/**
 * Description of ViewModelChildListAbstract
 *
 * @author pes2704
 */
abstract class ViewModelFamilyListAbstract extends ViewModelAbstract implements ViewModelFamilyListInterface {
    
    /**
     * 
     * @var FamilyRouteSegmentInterface
     */
    protected $familyRouteSegment;

    public function setFamilyRouteSegment(FamilyRouteSegmentInterface $familyRouteSegment): void {
        $this->familyRouteSegment = $familyRouteSegment;
    }
}
