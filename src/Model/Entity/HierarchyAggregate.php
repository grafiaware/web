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
 * Description of MenuNode
 *
 * @author pes2704
 */
class HierarchyAggregate extends EntityAbstract implements HierarchyAggregateInterface {

    private $leftNode;
    private $rightNode;
// readonly
    private $uid;
    private $depth;
    private $parentUid;

    /**
     * @var MenuItemInterface
     */
    private $menuItem;

    public function getLeftNode() {
        return $this->leftNode;
    }

    public function getRightNode() {
        return $this->rightNode;
    }

    public function getUid() {
        return $this->uid;
    }

    public function getDepth() {
        return $this->depth;
    }

    public function getParentUid() {
        return $this->parentUid;
    }

    public function getMenuItem(): MenuItemInterface {
        return $this->menuItem;
    }

    public function setLeftNode($leftNode): HierarchyAggregateInterface {
        $this->leftNode = $leftNode;
        return $this;
    }

    public function setRightNode($rightNode): HierarchyAggregateInterface {
        $this->rightNode = $rightNode;
        return $this;
    }

    public function setUid($hierarchyUid): HierarchyAggregateInterface {
        $this->uid = $hierarchyUid;
        return $this;
    }

    public function setDepth($depth): HierarchyAggregateInterface {
        $this->depth = $depth;
        return $this;
    }

    public function setParentUid($parentUid): HierarchyAggregateInterface {
        $this->parentUid = $parentUid;
        return $this;
    }


    public function setMenuItem(MenuItemInterface $menuItem): HierarchyAggregateInterface {
        $this->menuItem = $menuItem;
        return $this;
    }
}
