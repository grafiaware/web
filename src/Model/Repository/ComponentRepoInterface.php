<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Entity\ComponentInterface;

/**
 *
 * @author pes2704
 */
interface ComponentRepoInterface {
    /**
     *
     * @param type $name
     * @return ComponentInterface|null
     */
    public function get($name):?ComponentInterface;

    public function add(EntityInterface $entity);

    public function remove(EntityInterface $entity);

}
