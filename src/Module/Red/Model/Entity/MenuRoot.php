<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Entity;

use Model\Entity\PersistableEntityAbstract;

/**
 * Description of MenuRoot
 *
 * @author pes2704
 */
class MenuRoot extends PersistableEntityAbstract implements MenuRootInterface {

    private $name;
    private $uidFk;

    public function getName() {
        return $this->name;
    }

    public function getUidFk() {
        return $this->uidFk;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function setUidFk($uid_fk) {
        $this->uidFk = $uid_fk;
        return $this;
    }
}
