<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Repository;

use Model\Repository\RepoInterface;

use Red\Model\Entity\ItemActionInterface;

/**
 *
 * @author pes2704
 */
interface ItemActionRepoInterface extends RepoInterface {
    
    /**
     * 
     * @param type $itemId
     * @param type $loginName
     * @return ItemActionInterface|null
     */
    public function get($itemId, $loginName): ?ItemActionInterface;
    
    /**
     * Vyhledá polžky se zadaným loginname.
     * 
     * @param type $loginName
     * @return ItemActionInterface[]
     */
    public function findByLoginName($loginName);
    
    /**
     * Vyhledá položky s JINÝM než zadaným loginname
     * 
     * @param type $loginName
     * @return ItemActionInterface[]
     */
    public function findWithAnotherLoginName($loginName);
    
    /**
     * Vrací položjy se zadanžm item id - sloupec item id je unique, nesmí bát více editorů jedné položjy současně
     * 
     * @param type $itemId
     * @return ItemActionInterface|null
     */
    public function getByItemId($itemId): ?ItemActionInterface;
    
    /**
     *
     * @return ItemActionInterface[]
     */
    public function findAll(): array;
    public function add(ItemActionInterface $itemAction);
    public function remove(ItemActionInterface $itemAction);
}
