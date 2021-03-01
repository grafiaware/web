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
use Model\Entity\MenuItemAggregatePaper;
use Model\Hydrator\MenuItemChildHydrator;
use Model\Entity\PaperInterface;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class MenuItemAggregateRepo extends MenuItemRepo implements MenuItemRepoInterface, RepoReadonlyInterface {

    public function __construct(MenuItemDao $menuItemDao, HydratorInterface $menuItemHydrator,
            PaperAggregateRepo $paperAggregateRepo, MenuItemChildHydrator $menuItemPaperHydrator) {
        parent::__construct($menuItemDao, $menuItemHydrator);
        $this->registerOneToOneAssotiation(PaperInterface::class, 'id', $paperAggregateRepo);
        $this->registerHydrator($menuItemPaperHydrator);
    }

    protected function createEntity() {
        return new MenuItemAggregatePaper();
    }
}
