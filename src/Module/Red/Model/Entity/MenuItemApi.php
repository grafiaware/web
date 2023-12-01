<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Entity;

use Model\Entity\PersistableEntityAbstract;

/**
 * Description of MenuItemType
 *
 * @author pes2704
 */
class MenuItemApi extends PersistableEntityAbstract implements MenuItemApiInterface {

    private $module;
    private $generator;

    public function getModule() {
        return $this->module;
    }

    public function setModule($module) {
        $this->module = $module;
        return $this;
    }
    
    public function getGenerator() {
        return $this->generator;
    }
    
    public function setGenerator($generator) {
        $this->generator = $generator;
    }
}
