<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Repository\MenuItemRepo;

use Model\Entity\MenuItem;
use Model\Entity\MenuItemInterface;

use Model\Dao\MenuItemDao;
use Model\Hydrator\HydratorInterface;

use Model\Repository\PaperAggregateRepo;
use Model\Repository\PaperContentRepo;
use Model\Entity\MenuItemPaperAggregateInterface;
use Model\Entity\MenuItemPaperAggregate;
use Model\Hydrator\MenuItemChildHydrator;
use Model\Entity\PaperPaperContentsAggregate;
use Model\Hydrator\PaperChildHydrator;

use Model\Repository\Exception\UnableRecreateEntityException;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class MenuItemAggregateRepo extends MenuItemRepo implements RepoInterface, MenuItemRepoInterface {

    use AggregateRepoTrait;

    private $paperAggregateRepo;
    private $menuItemPaperHydrator;

    public function __construct(MenuItemDao $menuItemDao, HydratorInterface $menuItemHydrator,
            PaperAggregateRepo $paperAggregateRepo, MenuItemChildHydrator $menuItemPaperHydrator) {
        parent::__construct($menuItemDao, $menuItemHydrator);
        $this->paperAggregateRepo = $paperAggregateRepo;
        $this->menuItemPaperHydrator = $menuItemPaperHydrator;
        $this->childRepositories[] = $this->paperAggregateRepo;
    }

    /**
     *
     * @param array $row
     * @return string index
     */
    protected function recreateEntity($index, $row) {
        if ($row) {
            $row['paper'] = $this->paperAggregateRepo->getByFk($row['id']);

            $menuItemPaperAggregate = new MenuItemPaperAggregate();
            $this->hydrator->hydrate($menuItemPaperAggregate, $row);
            $this->menuItemPaperHydrator->hydrate($menuItemPaperAggregate, $row);
            $menuItemPaperAggregate->setPersisted();
            $this->collection[$index] = $menuItemPaperAggregate;
        }
    }

    // __destruct je v trait

}
