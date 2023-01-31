<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Web\Component\ViewModel\Content\TypeSelect;

use Web\Component\ViewModel\Content\MenuItemViewModelInterface;

/**
 *
 * @author pes2704
 */
interface ItemTypeSelectViewModelInterface extends MenuItemViewModelInterface {

    public function getTypeTransitions();

}
