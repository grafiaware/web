<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Entity;

use Model\Entity\EntityAbstract;

/**
 *
 * Description of MenuNode
 *
 * @author pes2704
 */
class Hierarchy extends EntityAbstract implements HierarchyInterface {

    private $leftNode;
    private $rightNode;
// readonly
    private $uid;
    private $depth;
    private $parentUid;

    private $keyAttribute = 'uid';

    public function getKeyAttribute() {
        return $this->keyAttribute;
    }

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

    public function setLeftNode($leftNode): HierarchyInterface {
        $this->leftNode = $leftNode;
        return $this;
    }

    public function setRightNode($rightNode): HierarchyInterface {
        $this->rightNode = $rightNode;
        return $this;
    }

    public function setUid($hierarchyUid): HierarchyInterface {
        $this->uid = $hierarchyUid;
        return $this;
    }

    public function setDepth($depth): HierarchyInterface {
        $this->depth = $depth;
        return $this;
    }

    public function setParentUid($parentUid): HierarchyInterface {
        $this->parentUid = $parentUid;
        return $this;
    }
}
