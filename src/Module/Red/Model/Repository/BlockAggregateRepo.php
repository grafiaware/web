<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Repository;

use Model\Repository\RepoReadonlyInterface;
use Model\Hydrator\HydratorInterface;

use Red\Model\Dao\BlockDao;
use Red\Model\Repository\MenuItemRepo;
use Red\Model\Entity\BlockAggregateMenuItemInterface;
use Red\Model\Entity\BlockAggregateMenuItem;
use Red\Model\Hydrator\BlockChildHydrator;
use Red\Model\Entity\MenuItemInterface;
/**
 * Description of Menu
 *
 * @author pes2704
 */
class BlockAggregateRepo extends BlockRepo implements RepoReadonlyInterface {

    public function __construct(BlockDao $blockDao, HydratorInterface $blockHydrator,
            MenuItemRepo $menuItemRepo, BlockChildHydrator $componentMenuItemHydrator) {
        parent::__construct($blockDao, $blockHydrator);
        $this->registerOneToOneAssociation(MenuItemInterface::class, ['lang_code_fk', 'uid_fk'], $menuItemRepo);
        $this->registerHydrator($componentMenuItemHydrator);
    }

    /**
     * Vrací BlockAggregate - agregát Block a MenuItem. Parametr $langCode je pouze použit pro výběr MenuItem.
     * @param type $langCode
     * @param type $name
     * @return BlockAggregateMenuItemInterface|null
     */
//    public function get($langCode, $name): ?BlockAggregateMenuItemInterface {
//        $index = $this->indexFromKeyParams($name);
//        if (!isset($this->collection[$index])) {
//            $row = $this->dataManager->get($name);
//            if ($row) {
//                $row['lang_code_fk'] = $langCode;
//                $this->recreateEntity($index, $row);
//            }
//        }
//        return $this->collection[$index] ?? null;
//    }

    protected function createEntity() {
        return new BlockAggregateMenuItem();
    }
}
