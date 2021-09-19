<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Entity;

use Model\Entity\EntityAbstract;

/**
 * Description of Menutem
 *
 * @author pes2704
 */
class MenuItem extends EntityAbstract implements MenuItemInterface {

    private $uidFk;
    private $langCodeFk;
    private $type;
    private $id;
    private $title;
    private $prettyuri;
    private $active;

    private $keyAttribute = ['uid_fk', 'lang_code_fk'];

    public function getKeyAttribute() {
        return $this->keyAttribute;
    }

    public function getUidFk() {
        return $this->uidFk;
    }

    public function getLangCodeFk() {
        return $this->langCodeFk;
    }

    public function getTypeFk() {
        return $this->type;
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

    public function setType($type): MenuItemInterface {
        $this->type = $type;
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
