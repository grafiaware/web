<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View\Menu;

use Component\View\Menu\LevelComponentInterface;

/**
 *
 * @author pes2704
 */
interface MenuComponentInterface extends LevelComponentInterface {

    const TOGGLE_EDIT_MENU_BUTTON = 'toggleEditMenuButton';
    const MENU = 'menu';

    public function setRenderersNames($menuWrapRendererName, $levelWrapRendererName, $itemRendererName, $itemEditableRendererName): MenuComponentInterface;
    public function setMenuRootName($menuRootName): MenuComponentInterface;
    public function withTitleItem($withTitle=false): MenuComponentInterface;
}
