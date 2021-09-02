<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Repository;

use Model\Repository\RepoAbstract;

use Model\Entity\EntityInterface;
use Red\Model\Entity\ItemActionInterface;
use Red\Model\Entity\ItemAction;
use Red\Model\Dao\ItemActionDao;
use Red\Model\Hydrator\ItemActionHydrator;

use Model\Repository\Exception\UnableRecreateEntityException;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class ItemActionRepo extends RepoAbstract implements ItemActionRepoInterface {


    public function __construct(ItemActionDao $itemActionDao, ItemActionHydrator $itemActionHydrator) {
        $this->dao = $itemActionDao;
        $this->registerHydrator($itemActionHydrator);
    }

    /**
     *
     * @param type $type_fk
     * @param type $item_id
     * @return ItemActionInterface|null
     */
    public function get($type_fk, $item_id): ?ItemActionInterface {
        $index = $type_fk . $item_id;
        if (!isset($this->collection[$index])) {
            $this->recreateEntity($index, $this->dao->get($type_fk, $item_id));
        }
        return $this->collection[$index] ?? NULL;
    }

    public function findAll() {
        $selected = [];
        foreach ($this->dao->findAll() as $itemActionsRow) {
            $index = $this->indexFromRow($itemActionsRow);
            if (!isset($this->collection[$index])) {
                $this->recreateEntity($index, $itemActionsRow);
            }
            $selected[] = $this->collection[$index];
        }
        return $selected;
    }

    public function add(ItemActionInterface $itemAction) {
        $index = $this->indexFromEntity($itemAction);
        $this->addEntity($itemAction, $index);
    }

    public function remove(ItemActionInterface $itemAction) {
        $index = $this->indexFromEntity($itemAction);
        $this->removeEntity($itemAction, $index);
    }

    protected function createEntity() {
        return new ItemAction();
    }

    protected function indexFromEntity(ItemActionInterface $itemAction) {
        return $itemAction->getTypeFk().$itemAction->getItemId();
    }

    protected function indexFromRow($row) {
        return $row['type_fk'].$row['item_id'];
    }


}
