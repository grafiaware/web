<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Hydrator;

use Model\Hydrator\HydratorInterface;

use Model\Entity\EntityInterface;
use Model\RowData\RowDataInterface;

use Red\Model\Entity\HierarchyAggregateInterface;

/**
 * Description of MenuNodeHydrator
 *
 * @author pes2704
 */
class HierarchyNodeHydrator implements HydratorInterface {

    /**
     *
     * @param HierarchyAggregateInterface $menuNode
     * @param type $rowData
     */
    public function hydrate(EntityInterface $menuNode, RowDataInterface $rowData) {
        /** @var HierarchyAggregateInterface $menuNode */
        $menuNode
            ->setUid($rowData->offsetGet('uid'))
            ->setDepth($rowData->offsetGet('depth'))
            ->setLeftNode($rowData->offsetGet('left_node'))
            ->setRightNode($rowData->offsetGet('right_node'))
            ->setParentUid($rowData->offsetGet('parent_uid'))
        ;
    }

    /**
     *
     * @param HierarchyAggregateInterface $menuNode
     * @param type $rowData
     */
    public function extract(EntityInterface $menuNode, RowDataInterface $rowData) {
        /** @var HierarchyAggregateInterface $menuNode */
        $rowData->offsetSet('uid', $menuNode->getUid());
        $rowData->offsetSet('left_node', $menuNode->getLeftNode());
        $rowData->offsetSet('right_node', $menuNode->getRightNode());
        $rowData->offsetSet('parent_uid', $menuNode->getParentUid());
        // depth je generovaná vlastnost
    }
}
