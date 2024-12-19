<?php
namespace Component\ViewModel\RouteSegment;

/**
 *
 * @author pes2704
 */
interface FamilyRouteSegmentInterface {
    
    public function hasFamily(): bool;
    
    public function getParentName(): string;
    
    public function getParentId(): string;
    
    public function getItemName(): string;
    
    public function getFamilyRouteSegment(): string;
    
        }
