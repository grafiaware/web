<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel;

use Component\ViewModel\ViewModelInterface;

/**
 *
 * @author pes2704
 */
interface ViewModelChildListInterface extends ViewModelInterface {  
    
    public function setParentId($id);
    
    public function hasParentId(): bool;
    
    public function getParentId();
}
