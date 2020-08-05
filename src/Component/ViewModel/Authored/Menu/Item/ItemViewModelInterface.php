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
    public function isOnPath();
    public function isLeaf();
    public function getIsPresented();
    public function isRestored();
    public function getReadonly();
    public function getInnerHtml();

    /**
     * @return HierarchyNodeInterface
     */
    public function getMenuNode();
}
