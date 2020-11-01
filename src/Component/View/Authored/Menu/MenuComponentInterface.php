<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View\Authored\Menu;

use Component\View\Authored\AuthoredComponentInterface;

/**
 *
 * @author pes2704
 */
interface MenuComponentInterface extends AuthoredComponentInterface {
    public function setRenderersNames($levelWrapRendererName, $itemRendererName): MenuComponentInterface;
    public function setMenuRootName($menuRootName): MenuComponentInterface;
    public function withTitleItem($withTitle=false): MenuComponentInterface;
}
