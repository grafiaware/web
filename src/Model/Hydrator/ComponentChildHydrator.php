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
 * Description of ComponentChildHydrator
 *
 * @author pes2704
 */
class ComponentChildHydrator implements HydratorInterface {

    /**
     * Nastaví do agregátu MenuItem, pokud existuje. MenuItem je závislý na kontextu a tedy může být null (neaktivní nebo neaktuální menu item)
     *
     * @param ComponentAggregateInterface $componentAggregate
     * @param type $row
     */
    public function hydrate(EntityInterface $componentAggregate, &$row) {
        /** @var ComponentAggregateInterface $componentAggregate */
        if (isset($row['menuItem'])) {
        $componentAggregate
            ->setMenuItem($row['menuItem']);
        }
    }

    /**
     *
     * @param ComponentAggregateInterface $componentAggregate
     * @param array $row
     */
    public function extract(EntityInterface $componentAggregate, &$row) {
        /** @var ComponentAggregateInterface $componentAggregate */
        $row['menuItem'] = $componentAggregate->getMenuItem();
    }

}
