<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Entity;

use Model\Entity\PersistableEntityInterface;

/**
 *
 * @author pes2704
 */
interface HierarchyInterface extends PersistableEntityInterface {
    public function getUid();
    public function getDepth();
    public function getLeftNode();
    public function getRightNode();
    public function getParentUid();

    public function setUid($uid): HierarchyInterface;
    public function setDepth($depth): HierarchyInterface;
    public function setLeftNode($leftNode): HierarchyInterface;
    public function setRightNode($rightNode): HierarchyInterface;
    public function setParentUid($parentUid): HierarchyInterface;
}
