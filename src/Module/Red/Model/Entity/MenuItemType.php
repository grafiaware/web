<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Entity;

use Model\Entity\EntityAbstract;

/**
 * Description of MenuItemType
 *
 * @author pes2704
 */
class MenuItemType extends EntityAbstract implements MenuItemTypeInterface {

    private $type;

    private $keyAttribute = 'type';

    public function getKeyAttribute() {
        return $this->keyAttribute;
    }

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
        return $this;
    }

}
