<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Hydrator\RowHydratorInterface;
use Model\RowData\RowDataInterface;

use Red\Model\Entity\MenuItemAggregatePaperInterface;
use Red\Model\Entity\PaperInterface;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class MenuItemChildPaperHydrator implements RowHydratorInterface {

    /**
     *
     * @param MenuItemAggregatePaperInterface $menuItemPaperAggregate
     * @param type $rowData
     */
    public function hydrate(EntityInterface $menuItemPaperAggregate, RowDataInterface $rowData) {
        /** @var MenuItemAggregatePaperInterface $menuItemPaperAggregate */
        $menuItemPaperAggregate
            ->setPaper($rowData->offsetGet(PaperInterface::class));
    }

    /**
     *
     * @param MenuItemAggregatePaperInterface $paperAggregate
     * @param type $rowData
     */
    public function extract(EntityInterface $paperAggregate, RowDataInterface $rowData) {
        /** @var MenuItemAggregatePaperInterface $paperAggregate */
        $rowData->offsetSet(PaperInterface::class, $paperAggregate->getPaper());
    }

}
