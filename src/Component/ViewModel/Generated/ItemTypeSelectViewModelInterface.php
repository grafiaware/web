<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Generated;

use Component\ViewModel\ViewModelInterface;

use Model\Entity\MenuItemInterface;

/**
 *
 * @author pes2704
 */
interface ItemTypeSelectViewModelInterface extends ViewModelInterface {

    public function getTypeTransitions();

    /**
     * Vrací menuItem odpovídající prezentované položce menu. Řídí se hodnotami vlastností objektu PresentationStatus.
     *
     * @return MenuItemInterface|null
     */
    public function getMenuItem(): ?MenuItemInterface ;
}
