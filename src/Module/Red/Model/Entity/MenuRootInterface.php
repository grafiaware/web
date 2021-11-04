<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Entity;
use Model\Entity\EntityInterface;

/**
 *
 * @author pes2704
 */
interface MenuRootInterface extends EntityInterface {
    public function getName();

    public function getUidFk();

    public function setName($name);

    public function setUidFk($uid_fk);
}
