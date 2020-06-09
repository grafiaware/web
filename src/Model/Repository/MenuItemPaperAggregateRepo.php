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

use Model\Repository\PaperHeadlineRepo;
use Model\Repository\PaperContentRepo;
use Model\Entity\MenuItemPaperAggregateInterface;
use Model\Entity\MenuItemPaperAggregate;
use Model\Hydrator\MenuItemPaperAggregateHydrator;


use Model\Repository\Exception\UnableRecreateEntityException;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class MenuItemPaperAggregateRepo extends MenuItemRepo implements RepoInterface, MenuItemRepoInterface {

    use AggregateRepoTrait;

    private $paperHeadlineRepo;
    private $paperContentRepo;
    private $aggregateHydrator;

    public function __construct(MenuItemDao $menuItemDao, HydratorInterface $menuItemHydrator, PaperHeadlineRepo $paperHeadlineRepo, PaperContentRepo $paperContentRepo, MenuItemPaperAggregateHydrator $menuItemPaperHydrator) {
        parent::__construct($menuItemDao, $menuItemHydrator);
        $this->paperHeadlineRepo = $paperHeadlineRepo;
        $this->paperContentRepo = $paperContentRepo;
        $this->aggregateHydrator = $menuItemPaperHydrator;
    }

    /**
     *
     * @param array $row
     * @return string index
     */
    protected function recreateEntity($index, $row) {
        if ($row) {
            $menuItemPaperAggregate = new MenuItemPaperAggregate();
            $this->hydrator->hydrate($menuItemPaperAggregate, $row);
            $menuItemIdFk = $menuItemPaperAggregate->getId();
            $row =  [
                        'headline' => $this->paperHeadlineRepo->get($menuItemIdFk),
                        'contents' => $this->paperContentRepo->findByMenuItemIdFk($menuItemIdFk)
                    ];
            $this->aggregateHydrator->hydrate($menuItemPaperAggregate, $row);

            $menuItemPaperAggregate->setPersisted();
            $this->collection[$index] = $menuItemPaperAggregate;
        }
    }

    // __destruct je v trait

}
