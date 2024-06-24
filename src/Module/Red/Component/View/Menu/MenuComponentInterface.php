<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\View\Menu;

use Component\View\ComponentCompositeInterface;

/**
 *
 * @author pes2704
 */
interface MenuComponentInterface extends ComponentCompositeInterface {

    const TOGGLE_EDIT_MENU_BUTTON = 'toggleEditMenuButton';
    const MENU = 'menu';

    public function setRenderersNames($levelRendererName, $levelRendererEditableName): MenuComponentInterface;    
}
