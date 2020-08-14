<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Authored\Menu\Item;

use Model\Entity\HierarchyNodeInterface;

/**
 *
 * @author pes2704
 */
interface ItemViewModelInterface {

    public function setInnerHtml($innerHtml): void;
    public function setModeCommand($mode);

    public function isOnPath();
    public function isLeaf();
    public function isPresented();
    public function isCutted();
    public function isReadonly();
    public function getInnerHtml();
    public function getModeCommand();
    /**
     * @return HierarchyNodeInterface
     */
    public function getMenuNode();
}
