<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Repository;

use Model\Repository\RepoAbstract;

use Model\Entity\PersistableEntityInterface;
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
     * @param type $itemId
     * @param type $loginName
     * @return ItemActionInterface|null
     */
    public function get($itemId, $loginName): ?ItemActionInterface {
        return $this->getEntity($itemId, $loginName);
    }
    
    /**
     * 
     * @param type $itemId
     * @return ItemActionInterface|null
     */
    public function getByItemId($itemId): ?ItemActionInterface {
        return $this->getEntityByUnique(['item_id'=>$itemId]);  
    }
    
    /**
     * 
     * @param type $itemId
     * @return type
     */
    public function findByLoginName($itemId) {
        return $this->findEntities("item_id=:item_id", ['item_id'=>$itemId]);
    }
    
    /**
     * 
     * @param type $loginName
     * @return ItemActionInterface[]
     */
    public function findByOtherLoginName($loginName) {
        return $this->findEntities("editor_login_name<>:editor_login_name", ['editor_login_name'=>$loginName]);
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

    protected function indexFromEntity(ItemActionInterface $itemAction) {
        return $itemAction->getItemId().$itemAction->getEditorLoginName();
    }

    protected function indexFromRow($row) {
        return $row['item_id'].$row['editor_login_name'];
    }
}
