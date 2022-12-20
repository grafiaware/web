<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Repository;

use Red\Model\Repository\MenuItemRepo;

use Red\Model\Dao\MenuItemDao;
use Model\Hydrator\RowHydratorInterface;
use Model\Repository\RepoReadonlyInterface;

use Red\Model\Repository\PaperAggregateContentsRepo;
use Red\Model\Entity\MenuItemAggregatePaper;
use Red\Model\Hydrator\MenuItemChildPaperHydrator;
use Red\Model\Entity\PaperInterface;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class MenuItemAggregatePaperRepo extends MenuItemRepo implements MenuItemRepoInterface, RepoReadonlyInterface {

    public function __construct(MenuItemDao $menuItemDao, RowHydratorInterface $menuItemHydrator,
            PaperAggregateContentsRepo $paperAggregateRepo, MenuItemChildPaperHydrator $menuItemPaperHydrator) {
        parent::__construct($menuItemDao, $menuItemHydrator);
        $this->registerOneToOneAssociation(PaperInterface::class, 'id', $paperAggregateRepo);
        $this->registerHydrator($menuItemPaperHydrator);
    }

    protected function createEntity() {
        return new MenuItemAggregatePaper();
    }
}
