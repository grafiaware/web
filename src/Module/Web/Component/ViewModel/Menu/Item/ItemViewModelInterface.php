<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Web\Component\ViewModel\Menu\Item;

use Red\Model\Entity\HierarchyAggregateInterface;
use Web\Component\ViewModel\ViewModelInterface;
use Web\Component\View\ComponentInterface;

/**
 *
 * @author pes2704
 */
interface ItemViewModelInterface extends ViewModelInterface {

    public function hydrateChild(ComponentInterface $childComponent): void;

    public function isOnPath();
    public function isLeaf();
    public function isPresented();
    public function isRoot();
    public function isCutted();

    public function isPasteMode();
    public function isMenuEditable();

    public function getChild(): ?ComponentInterface;
    public function getRealDepth();
    /**
     * @return HierarchyAggregateInterface
     */
    public function getHierarchyAggregate();
}
