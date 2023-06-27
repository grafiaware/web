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
interface MenuItemAssetInterface extends PersistableEntityInterface {

    public function getMenuItemIdFk();

    public function getFilepath();

    public function getMimeType();

    public function getEditorLoginName();

    public function getCreated();

    public function getUpdated();

    public function setMenuItemIdFk($menuItemIdFk): MenuItemAssetInterface;

    public function setFilepath($filepath): MenuItemAssetInterface;

    public function setMimeType($mimeType): MenuItemAssetInterface;

    public function setEditorLoginName($editorLoginName): MenuItemAssetInterface;

    public function setCreated($created): MenuItemAssetInterface;

    public function setUpdated($created): MenuItemAssetInterface;
}
