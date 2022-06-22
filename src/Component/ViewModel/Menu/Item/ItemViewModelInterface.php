<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Menu\Item;

use Red\Model\Entity\HierarchyAggregateInterface;
use Component\ViewModel\ViewModelInterface;
use Component\View\ComponentInterface;

/**
 *
 * @author pes2704
 */
interface ItemViewModelInterface extends ViewModelInterface {

    public function setChild(ComponentInterface $childComponent): void;

    public function isOnPath();
    public function isLeaf();
    public function isPresented();
    public function isPasteMode();
    public function isCutted();
    public function getChild(): ?ComponentInterface;
    public function getRealDepth();
    public function isMenuEditable();

    /**
     * @return HierarchyAggregateInterface
     */
    public function getHierarchyAggregate();
}
