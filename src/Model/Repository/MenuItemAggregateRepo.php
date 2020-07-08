<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Repository\MenuItemRepo;

use Model\Dao\MenuItemDao;
use Model\Hydrator\HydratorInterface;

use Model\Repository\PaperAggregateRepo;
use Model\Entity\MenuItemPaperAggregate;
use Model\Hydrator\MenuItemChildHydrator;
use Model\Repository\Association\AssotiationOneToOneFactory;

use Model\Repository\Association\AssociationFactoryInterface;

use Model\Repository\Exception\UnableRecreateEntityException;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class MenuItemAggregateRepo extends MenuItemRepo implements MenuItemRepoInterface, RepoReadonlyInterface {

    public function __construct(MenuItemDao $menuItemDao, HydratorInterface $menuItemHydrator,
            PaperAggregateRepo $paperAggregateRepo, MenuItemChildHydrator $menuItemPaperHydrator) {
        parent::__construct($menuItemDao, $menuItemHydrator);
        $this->registerOneToOneAssotiation('paper', 'id', $paperAggregateRepo);
        $this->registerHydrator($menuItemPaperHydrator);
    }

    protected function createEntity() {
        return new MenuItemPaperAggregate();
    }
}
