<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\ViewModel\Content\StaticItem;

use Component\ViewModel\ViewModelInterface;

use Red\Model\Entity\MenuItemInterface;
use Red\Model\Entity\StaticItemInterface;

/**
 *
 * @author pes2704
 */
interface StaticItemViewModelInterface extends ViewModelInterface {

    /**
     * Vrací menuItem odpovídající prezentované položce menu. Řídí se hodnotami vlastností objektu PresentationStatus.
     *
     * @return MenuItemInterface|null
     */
    public function getMenuItem(): ?MenuItemInterface ;
    
    /**
     * 
     * @return string|null
     */
    public function getStaticTemplatePath(): ?string;
}
