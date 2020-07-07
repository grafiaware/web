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
use Model\Entity\ComponentAggregate;
use Model\Entity\ComponentAggregateInterface;
use Model\Hydrator\ComponentChildHydrator;
use Model\Repository\Association\AssotiationOneToOneFactory;

use Model\Repository\Association\AssociationFactoryInterface;

use Model\Repository\Exception\UnableRecreateEntityException;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class ComponentAggregateRepo extends ComponentRepo implements ComponentRepoInterface, RepoPublishedOnlyModeInterface, RepoReadonlyInterface {

    use RepoPublishedOnlyModeTrait;

    public function __construct(ComponentDao $componentDao, HydratorInterface $componentHydrator,
            MenuItemRepo $menuItemRepo, ComponentChildHydrator $componentMenuItemHydrator) {
        parent::__construct($componentDao, $componentHydrator);
        $this->registerOneToOneAssotiation('menuItem', ['lang_code_fk', 'uid_fk'], $menuItemRepo);
        $this->registerHydrator($componentMenuItemHydrator);
    }

    public function get($langCode, $name): ?ComponentAggregateInterface {

    }

    protected function createEntity() {
        return new componentAggregate();
    }
}
