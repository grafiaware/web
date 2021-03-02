<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Dao\BlockDao;
use Model\Hydrator\HydratorInterface;

use Model\Repository\MenuItemRepo;
use Model\Entity\BlockAggregateMenuItemInterface;
use Model\Entity\BlockAggregateMenuItem;
use Model\Hydrator\BlockChildHydrator;
use Model\Entity\MenuItemInterface;
/**
 * Description of Menu
 *
 * @author pes2704
 */
class BlockAggregateRepo extends BlockRepo implements BlockAggregateRepoInterface, RepoReadonlyInterface {

    public function __construct(BlockDao $componentDao, HydratorInterface $componentHydrator,
            MenuItemRepo $menuItemRepo, BlockChildHydrator $componentMenuItemHydrator) {
        parent::__construct($componentDao, $componentHydrator);
        $this->registerOneToOneAssociation(MenuItemInterface::class, ['lang_code_fk', 'uid_fk'], $menuItemRepo);
        $this->registerHydrator($componentMenuItemHydrator);
    }

    /**
     * Vrací ComponentAggregate - agregát Component a MenuItem. Parametr $langCode je pouze použit pro výběr MenuItem.
     * @param type $langCode
     * @param type $name
     * @return BlockAggregateMenuItemInterface|null
     */
    public function getAggregate($langCode, $name): ?BlockAggregateMenuItemInterface {
        $index = $this->indexFromKeyParams($name);
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
        return new BlockAggregateMenuItem();
    }

    protected function indexFromKeyParams($name) {
        return $name;
    }
}
