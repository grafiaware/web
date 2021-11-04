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

use Red\Model\Entity\BlockInterface;

/**
 * Description of MenuItemHydrator
 *
 * @author pes2704
 */
class BlockHydrator implements HydratorInterface {

    /**
     *
     * @param BlockInterface $block
     * @param type $rowData
     */
    public function hydrate(EntityInterface $block, RowDataInterface $rowData) {
        /** @var BlockInterface $block */
        $block
                ->setName($rowData->offsetGet('name'))
                ->setUidFk($rowData->offsetGet('uid_fk'))
            ;
    }

    /**
     *
     * @param BlockInterface $block
     * @param type $rowData
     */
    public function extract(EntityInterface $block, RowDataInterface $rowData) {
        /** @var BlockInterface $block */
        $rowData->offsetSet('name', $block->getName());
    }
}
