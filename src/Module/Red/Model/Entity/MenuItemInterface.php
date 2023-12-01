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
interface MenuItemInterface extends PersistableEntityInterface {

    public function getUidFk();
    public function getLangCodeFk();
    public function getApiModuleFk();
    public function getApiGeneratorFk();
    public function getId();
    public function getTitle();
    public function getPrettyuri();
    public function getActive();

    public function setUidFk($uidFk): MenuItemInterface;
    public function setLangCodeFk($lang): MenuItemInterface;
    public function setApiModuleFk($type): MenuItemInterface;
    public function setApiGeneratorFk($type): MenuItemInterface;
    public function setId($id): MenuItemInterface;
    public function setTitle($title): MenuItemInterface;
    public function setPrettyuri($prettyuri): MenuItemInterface;
    public function setActive($active): MenuItemInterface;

}
