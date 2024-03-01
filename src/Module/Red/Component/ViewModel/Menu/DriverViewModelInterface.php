<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\ViewModel\Menu;

use Red\Model\Entity\HierarchyAggregateInterface;
use Component\ViewModel\ViewModelInterface;
use Component\View\ComponentInterface;

/**
 *
 * @author pes2704
 */
interface DriverViewModelInterface extends ViewModelInterface {

    public function isActive(); 
    public function isCutted();
    public function isPasteMode();
    public function isMenuEditable();
    
    public function getPageHref();
    public function getRedApiUri();
    public function getTitle();    
}
