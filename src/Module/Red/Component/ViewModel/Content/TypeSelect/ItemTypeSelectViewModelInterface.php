<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\ViewModel\Content\TypeSelect;

use Red\Component\ViewModel\Content\MenuItemViewModelInterface;

/**
 *
 * @author pes2704
 */
interface ItemTypeSelectViewModelInterface extends MenuItemViewModelInterface {

    public function getTypeTransitions();

}
