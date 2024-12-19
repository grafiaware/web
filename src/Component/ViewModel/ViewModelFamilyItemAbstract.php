<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel;

use Component\ViewModel\ViewModelFamilyItemInterface;

use Component\ViewModel\ViewModelItemAbstract;
use Component\ViewModel\RouteSegment\FamilyRouteSegmentInterface;

/**
 * Description of ViewModelAbstract
 *
 * @author pes2704
 */
abstract class ViewModelFamilyItemAbstract extends ViewModelItemAbstract implements ViewModelFamilyItemInterface {
    
    /**
     * 
     * @var FamilyRouteSegmentInterface
     */
    protected $familyRouteSegment;

    public function setFamilyRouteSegment(FamilyRouteSegmentInterface $familyRouteSegment): void {
        $this->familyRouteSegment = $familyRouteSegment;
    }
}
