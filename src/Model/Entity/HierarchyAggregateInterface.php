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
interface HierarchyAggregateInterface {
    public function getUid();
    public function getDepth();
    public function getLeftNode();
    public function getRightNode();
    public function getParentUid();
    public function getMenuItem(): MenuItemInterface;

    public function setUid($uid): HierarchyAggregateInterface;
    public function setDepth($depth): HierarchyAggregateInterface;
    public function setLeftNode($leftNode): HierarchyAggregateInterface;
    public function setRightNode($rightNode): HierarchyAggregateInterface;
    public function setParentUid($parentUid): HierarchyAggregateInterface;
    public function setMenuItem(MenuItemInterface $menuItem): HierarchyAggregateInterface;
}
