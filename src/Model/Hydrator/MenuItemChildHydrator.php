<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Entity\MenuItemAggregatePaperInterface;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class MenuItemChildHydrator implements HydratorInterface {

    /**
     *
     * @param MenuItemAggregatePaperInterface $menuItemPaperAggregate
     * @param type $row
     */
    public function hydrate(EntityInterface $menuItemPaperAggregate, &$row) {
        /** @var MenuItemAggregatePaperInterface $menuItemPaperAggregate */
        $menuItemPaperAggregate
            ->setPaper($row['paper']);
    }

    /**
     *
     * @param MenuItemAggregatePaperInterface $paperAggregate
     * @param type $row
     */
    public function extract(EntityInterface $paperAggregate, &$row) {
        /** @var MenuItemAggregatePaperInterface $paperAggregate */
        $row['paper'] = $paperAggregate->getPaper();
    }

}
