<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View\Menu;

/**
 *
 * @author pes2704
 */
interface MenuComponentInterface  {
    public function setRenderersNames($levelWrapRendererName): MenuComponentInterface;
    public function setMenuRootName($menuRootName): MenuComponentInterface;
    public function withTitleItem($withTitle=false): MenuComponentInterface;
}
