<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Entity;

use Model\Entity\PersistableEntityAbstract;

/**
 * Description of Menutem
 *
 * @author pes2704
 */
class MenuItem extends PersistableEntityAbstract implements MenuItemInterface {

    private $uidFk;
    private $langCodeFk;
    private $apiModuleFk;
    private $apiGeneratorFk;
    private $id;
    private $title;
    private $prettyuri;
    private $active;

    public function getUidFk() {
        return $this->uidFk;
    }

    public function getLangCodeFk() {
        return $this->langCodeFk;
    }

    public function getApiModuleFk() {
        return $this->apiModuleFk;
    }
    
    public function getApiGeneratorFk() {
        return $this->apiGeneratorFk;
    }
    
    public function getId() {
        return $this->id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getPrettyuri() {
        return $this->prettyuri;
    }

    public function getActive() {
        return $this->active;
    }

    public function setUidFk($uidFk): MenuItemInterface {
        $this->uidFk = $uidFk;
        return $this;
    }

    public function setLangCodeFk($lang): MenuItemInterface {
        $this->langCodeFk = $lang;
        return $this;
    }
    
    public function setApiModuleFk($module): MenuItemInterface {
        $this->apiModuleFk = $module;
        return $this;
    }
    
    public function setApiGeneratorFk($generator): MenuItemInterface {
        $this->apiGeneratorFk = $generator;
        return $this;
    }

    public function setId($id): MenuItemInterface {
        $this->id = $id;
        return $this;
    }

    public function setTitle($title): MenuItemInterface {
        $this->title = $title;
        return $this;
    }

    public function setPrettyuri($prettyuri): MenuItemInterface {
        $this->prettyuri = $prettyuri;
        return $this;
    }

    public function setActive($active): MenuItemInterface {
        $this->active = $active;
        return $this;
    }
}
