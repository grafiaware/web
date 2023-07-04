<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Entity;

use Model\Entity\PersistableEntityAbstract;

use Red\Model\Entity\MenuItemAssetInterface;

/**
 * Description of Enroll
 *
 * @author pes2704
 */
class MenuItemAsset extends PersistableEntityAbstract implements MenuItemAssetInterface {

    private $menuItemIdFk;
    private $filepath;
    private $mimeType;
    private $editorLoginName;
    private $created;
    private $updated;

    public function getMenuItemIdFk() {
        return $this->menuItemIdFk;
    }

    public function getFilepath() {
        return $this->filepath;
    }

    public function getMimeType() {
        return $this->mimeType;
    }

    public function getEditorLoginName() {
        return $this->editorLoginName;
    }

    public function getCreated() {
        return $this->created;
    }

    public function getUpdated() {
        return $this->updated;
    }

    public function setMenuItemIdFk($menuItemIdFk): MenuItemAssetInterface {
        $this->menuItemIdFk = $menuItemIdFk;
        return $this;
    }

    public function setFilepath($filepath): MenuItemAssetInterface {
        $this->filepath = $filepath;
        return $this;
    }

    public function setMimeType($mimeType): MenuItemAssetInterface  {
        $this->mimeType = $mimeType;
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

    public function setUpdated($updated): MenuItemAssetInterface {
        $this->updated = $updated;
        return $this;
    }
}
