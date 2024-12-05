<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel;

use Component\ViewModel\ViewModelListInterface;

/**
 *
 * @author pes2704
 */
interface ViewModelChildInterface {  
    
    public function setParentId(string $parentId);
    
    public function hasParentId(): bool;
    
    public function getParentId(): string;
}
