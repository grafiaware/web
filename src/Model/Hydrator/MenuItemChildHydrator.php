<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Entity\MenuItemPaperAggregateInterface;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class MenuItemChildHydrator implements HydratorInterface {

    /**
     *
     * @param MenuItemPaperAggregateInterface $menuItemPaperAggregate
     * @param type $row
     */
    public function hydrate(EntityInterface $menuItemPaperAggregate, &$row) {
        /** @var MenuItemPaperAggregateInterface $menuItemPaperAggregate */
        $menuItemPaperAggregate
            ->setPaper($row['paper']);
    }

    /**
     *
     * @param MenuItemPaperAggregateInterface $paperAggregate
     * @param type $row
     */
    public function extract(EntityInterface $paperAggregate, &$row) {
        /** @var MenuItemPaperAggregateInterface $paperAggregate */
        $row['paper'] = $paperAggregate->getPaper();
    }

}
