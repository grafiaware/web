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
    public function get($type_fk, $item_id): ?ItemActionInterface;
    public function findAll();
    public function add(ItemActionInterface $itemAction);
    public function remove(ItemActionInterface $itemAction);
}
