<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Entity;

/**
 * Description of Component
 *
 * @author pes2704
 */
class Component extends EntityAbstract implements ComponentInterface {

    private $name;
    private $uidFk;

    public function getName() {
        return $this->name;
    }

    public function getUidFk() {
        return $this->uidFk;
    }

    public function setName($name): ComponentInterface {
        $this->name = $name;
        return $this;
    }

    public function setUidFk($uid_fk): ComponentInterface {
        $this->uidFk = $uid_fk;
        return $this;
    }
}
