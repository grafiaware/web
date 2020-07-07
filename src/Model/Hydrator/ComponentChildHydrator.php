<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Hydrator;

use Model\Entity\EntityInterface;
use Model\Entity\ComponentAggregateInterface;

/**
 * Description of PaperHydrator
 *
 * @author pes2704
 */
class ComponentChildHydrator implements HydratorInterface {

    /**
     *
     * @param ComponentAggregateInterface $componentAggregate
     * @param type $row
     */
    public function hydrate(EntityInterface $componentAggregate, &$row) {
        /** @var ComponentAggregateInterface $componentAggregate */
        $componentAggregate
            ->setMenuItem($row['MenuItem']);
    }

    /**
     *
     * @param ComponentAggregateInterface $componentAggregate
     * @param array $row
     */
    public function extract(EntityInterface $componentAggregate, &$row) {
        /** @var ComponentAggregateInterface $componentAggregate */
        $row['MenuItem'] = $componentAggregate->getMenuItem();
    }

}
