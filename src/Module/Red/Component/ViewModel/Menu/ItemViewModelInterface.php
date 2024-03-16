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
interface ItemViewModelInterface extends ViewModelInterface {

    public function appendDriver(ViewModelInterface $driverViewmodel): void;
    public function getDriver(): ?ViewModelInterface;
    
//    public function appendLevel(ComponentInterface $childComponent): void;
//    public function getLevel(): ?ComponentInterface;
    
    public function isOnPath();
    public function isLeaf();

    public function getRealDepth();
    /**
     * @return HierarchyAggregateInterface
     */
//    public function getHierarchyAggregate();
}
