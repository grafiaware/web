<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Entity\HierarchyNodeInterface;

/**
 * Description of MenuNodeHydrator
 *
 * @author pes2704
 */
class HierarchyNodeHydrator implements HydratorInterface {

    /**
     *
     * @param HierarchyNodeInterface $menuNode
     * @param type $row
     */
    public function hydrate(EntityInterface $menuNode, &$row) {
        /** @var HierarchyNodeInterface $menuNode */
        $menuNode
            ->setUid($row['uid'])
            ->setDepth($row['depth'])
            ->setLeftNode($row['left_node'])
            ->setRightNode($row['right_node'])
            ->setParentUid($row['parent_uid'])
        ;
    }

    /**
     *
     * @param HierarchyNodeInterface $menuNode
     * @param type $row
     */
    public function extract(EntityInterface $menuNode, &$row) {
        /** @var HierarchyNodeInterface $menuNode */
        $row['uid'] = $menuNode->getUid();
        $row['left_node'] = $menuNode->getLeftNode();
        $row['right_node'] = $menuNode->getRightNode();
        $row['parent_uid'] = $menuNode->getParentUid();
        // depth je generovaná vlastnost
    }
}
