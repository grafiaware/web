<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\MenuItem\TypeSelect;

use Component\ViewModel\MenuItem\MenuItemViewModelInterface;

/**
 *
 * @author pes2704
 */
interface ItemTypeSelectViewModelInterface extends MenuItemViewModelInterface {

    public function getTypeTransitions();

}
