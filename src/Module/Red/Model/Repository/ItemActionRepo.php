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
        $this->dataManager = $itemActionDao;
        $this->registerHydrator($itemActionHydrator);
    }

    /**
     *
     * @param type $typeFk
     * @param type $itemId
     * @return ItemActionInterface|null
     */
    public function get($typeFk, $itemId): ?ItemActionInterface {
        $key = $this->getPrimaryKeyTouples(['type_fk'=>$typeFk, 'item_id'=>$itemId]);
        return $this->getEntity($key);
    }

    /**
     *
     * @return ItemActionInterface[]
     */
    public function findAll(): array {
        return $this->findEntities();
    }

    public function add(ItemActionInterface $itemAction) {
        $this->addEntity($itemAction);
    }

    public function remove(ItemActionInterface $itemAction) {
        $this->removeEntity($itemAction);
    }

    protected function createEntity() {
        return new ItemAction();
    }

    protected function indexFromKeyParams($typeFk, $itemId) {
        return $typeFk.$itemId;

    }
    protected function indexFromEntity(ItemActionInterface $itemAction) {
        return $itemAction->getTypeFk().$itemAction->getItemId();
    }

    protected function indexFromRow($row) {
        return $row['type_fk'].$row['item_id'];
    }


}
