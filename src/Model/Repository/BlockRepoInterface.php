<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Entity\BlockInterface;

/**
 *
 * @author pes2704
 */
interface BlockRepoInterface {
    /**
     *
     * @param type $name
     * @return BlockInterface|null
     */
    public function get($name):?BlockInterface;

    public function add(EntityInterface $entity);

    public function remove(EntityInterface $entity);

}
