<?php
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
