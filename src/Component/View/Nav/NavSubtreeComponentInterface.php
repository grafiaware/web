<?php

namespace Component\View\Nav;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author pes2704
 */
interface NavSubtreeComponentInterface {
    public function setMenuRootName($menuRootName): NavSubtreeComponentInterface;
    public function withTitleItem($withTitle=false): NavSubtreeComponentInterface;
}
