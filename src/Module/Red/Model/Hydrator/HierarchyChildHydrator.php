<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Hydrator;

use Model\Hydrator\RowHydratorInterface;
use Model\Entity\EntityInterface;
use Model\RowData\RowDataInterface;

use Red\Model\Entity\HierarchyAggregateInterface;
use Red\Model\Entity\MenuItemInterface;

/**
 * Description of PaperPaperContentAggregateHydrator
 *
 * @author pes2704
 */
class HierarchyChildHydrator implements RowHydratorInterface {

    /**
     * Nastaví do agregátu contents, pokud existuje. Contents jsou závislé na kontextu a tedy mohou být null (neaktivní nebo neaktuální content) a pole může být prázdné
     *
     * @param HierarchyAggregateInterface $hierarchyAggregate
     * @param type $rowData
     */
    public function hydrate(EntityInterface $hierarchyAggregate, RowDataInterface $rowData) {
        /** @var HierarchyAggregateInterface $hierarchyAggregate */
        $hierarchyAggregate->setMenuItem($rowData->offsetGet(MenuItemInterface::class));
    }

    /**
     *
     * @param HierarchyAggregateInterface $hierarchyAggregate
     * @param type $rowData
     */
    public function extract(EntityInterface $hierarchyAggregate, RowDataInterface $rowData) {
        /** @var HierarchyAggregateInterface $hierarchyAggregate */
        $rowData->offsetSet(MenuItemInterface::class, $hierarchyAggregate->getMenuItem());
    }

}
