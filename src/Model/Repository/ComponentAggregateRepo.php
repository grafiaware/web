<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Dao\ComponentDao;
use Model\Hydrator\HydratorInterface;

use Model\Repository\MenuItemRepo;
use Model\Entity\ComponentAggregateInterface;
use Model\Entity\ComponentAggregate;
use Model\Hydrator\ComponentChildHydrator;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class ComponentAggregateRepo extends ComponentRepo implements ComponentAggregateRepoInterface, RepoReadonlyInterface {

    public function __construct(ComponentDao $componentDao, HydratorInterface $componentHydrator,
            MenuItemRepo $menuItemRepo, ComponentChildHydrator $componentMenuItemHydrator) {
        parent::__construct($componentDao, $componentHydrator);
        $this->registerOneToOneAssotiation('menuItem', ['lang_code_fk', 'uid_fk'], $menuItemRepo);
        $this->registerHydrator($componentMenuItemHydrator);
    }

    /**
     * Vrací ComponentAggregate - agregát Component a MenuItem. Parametr $langCode je pouze použit pro výběr MenuItem.
     * @param type $langCode
     * @param type $name
     * @return ComponentAggregateInterface|null
     */
    public function getAggregate($langCode, $name): ?ComponentAggregateInterface {
        $index = $name;
        if (!isset($this->collection[$index])) {
            $row = $this->dao->get($name);
            if ($row) {
                $row['lang_code_fk'] = $langCode;
                $this->recreateEntity($index, $row);
            }
        }
        return $this->collection[$index] ?? null;
    }

    protected function createEntity() {
        return new ComponentAggregate();
    }
}
