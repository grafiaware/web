<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Entity;

use Model\Entity\PersistableEntityAbstract;

/**
 * Description of Component
 *
 * @author pes2704
 */
class Block extends PersistableEntityAbstract implements BlockInterface {

    private $name;
    private $uidFk;

    public function getName() {
        return $this->name;
    }

    public function getUidFk() {
        return $this->uidFk;
    }

    public function setName($name): BlockInterface {
        $this->name = $name;
        return $this;
    }

    public function setUidFk($uid_fk): BlockInterface {
        $this->uidFk = $uid_fk;
        return $this;
    }
}
