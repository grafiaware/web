<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Entity;

use Model\Entity\PersistableEntityAbstract;

/**
 * Description of Article
 *
 * @author pes2704
 */
class Statical extends PersistableEntityAbstract implements PaperInterface {

    private $id;
    private $menuItemIdFk;
    private $path;
    private $folded;

    public function getId() {
        return $this->id;
    }

    public function getMenuItemIdFk() {
        return $this->menuItemIdFk;
    }

    public function getPath() {
        return $this->path;
    }

    public function getFolded() {
        return $this->folded;
    }

    public function setId($id): PaperInterface {
        $this->id = $id;
        return $this;
    }

    public function setMenuItemIdFk($uidFk): PaperInterface {
        $this->menuItemIdFk = $uidFk;
        return $this;
    }

    public function setPath($path) {
        $this->path = $path;
        return $this;
    }

    public function setFolded($folded) {
        $this->folded = $folded;
        return $this;
    }
}
