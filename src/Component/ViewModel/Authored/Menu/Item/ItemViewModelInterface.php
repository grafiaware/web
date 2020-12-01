<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Authored\Menu\Item;

use Model\Entity\HierarchyAggregateInterface;

/**
 *
 * @author pes2704
 */
interface ItemViewModelInterface {

    public function setInnerHtml($innerHtml): void;
    public function setPasteUid($uid);

    public function isOnPath();
    public function isLeaf();
    public function isPresented();
    public function isPasteMode();
    public function isCutted();
    public function isReadonly();
    public function getInnerHtml();
    public function getPasteUid();
    /**
     * @return HierarchyAggregateInterface
     */
    public function getMenuNode();
}
