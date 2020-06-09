<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Entity;

use Model\Entity\MenuItemInterface;

/**
 *
 * @author pes2704
 */
interface HierarchyNodeInterface {
    public function getUid();
    public function getDepth();
    public function getLeftNode();
    public function getRightNode();
    public function getParentUid();
    public function getMenuItem(): MenuItemInterface;

    public function setUid($uid): HierarchyNodeInterface;
    public function setDepth($depth): HierarchyNodeInterface;
    public function setLeftNode($leftNode): HierarchyNodeInterface;
    public function setRightNode($rightNode): HierarchyNodeInterface;
    public function setParentUid($parentUid): HierarchyNodeInterface;
    public function setMenuItem(MenuItemInterface $menuItem): HierarchyNodeInterface;
}
