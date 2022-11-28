<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Entity;

use Model\Entity\EntityAbstract;

use Red\Model\Entity\MenuItemAssetInterface;

/**
 * Description of Enroll
 *
 * @author pes2704
 */
class MenuItemAsset extends EntityAbstract implements MenuItemAssetInterface {

    private $id;
    private $menuItemIdFk;
    private $filepath;
    private $editorLoginName;
    private $created;

    public function getId() {
        return $this->id;
    }

    public function getMenuItemIdFk() {
        return $this->menuItemIdFk;
    }

    public function getFilepath() {
        return $this->filepath;
    }

    public function getEditorLoginName() {
        return $this->editorLoginName;
    }

    public function getCreated() {
        return $this->created;
    }

    public function setId($id): MenuItemAssetInterface {
        $this->id = $id;
        return $this;
    }

    public function setMenuItemIdFk($menuItemIdFk): MenuItemAssetInterface {
        $this->menuItemIdFk = $menuItemIdFk;
        return $this;
    }

    public function setFilepath($filepath): MenuItemAssetInterface {
        $this->filepath = $filepath;
        return $this;
    }

    public function setEditorLoginName($editorLoginName): MenuItemAssetInterface {
        $this->editorLoginName = $editorLoginName;
        return $this;
    }

    public function setCreated($created): MenuItemAssetInterface {
        $this->created = $created;
        return $this;
    }
}
