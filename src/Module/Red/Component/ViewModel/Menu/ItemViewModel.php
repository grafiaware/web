<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\ViewModel\Menu;

use Component\ViewModel\ViewModelAbstract;
use Red\Model\Entity\HierarchyAggregateInterface;
use Component\View\ComponentInterface;
use Component\ViewModel\ViewModelInterface;

/**
 * Description of ItemViwModel
 *
 * @author pes2704
 */
class ItemViewModel extends ViewModelAbstract implements ItemViewModelInterface {
    
    private $realDepth;
    private $isOnPath;
    private $isLeaf;
    
    public function setOnPath($isOnPath) {
        $this->isOnPath = $isOnPath;
    }
    
    public function setLeaf($isLeaf) {
        $this->isLeaf = $isLeaf;
    }
    
    public function setRealDepth($realDepth) {
        $this->realDepth = $realDepth;
    }

    public function getRealDepth() {
        return $this->realDepth;
    }

    public function isOnPath() {
        return $this->isOnPath;
    }

    public function isLeaf() {
        return $this->isLeaf;
    }
}
