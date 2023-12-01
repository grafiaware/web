<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Entity;

use Model\Entity\PersistableEntityInterface;

/**
 *
 * @author pes2704
 */
interface MenuItemApiInterface extends PersistableEntityInterface {
    public function getModule();
    public function setModule($module);
    public function getGenerator();
    public function setGenerator($generator);
}
