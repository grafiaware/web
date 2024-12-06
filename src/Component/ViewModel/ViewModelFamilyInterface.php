<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel;

/**
 *
 * @author pes2704
 */
interface ViewModelFamilyInterface {  
    public function setFamily(string $parentName, string $parentId, string $childName);
    
    public function hasFamily(): bool;
    
    public function getParentName(): string;
    public function getParentId(): string;
    public function getItemName(): string;
    
    public function getFamilyRouteSegment(): string;    
}
