<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\ViewModel\Content\TypeSelect;

use Red\Component\ViewModel\Content\MenuItemViewModel;

use Red\Model\Entity\MenuItemTypeInterface;

/**
 * Description of LanguageSelect
 *
 * @author pes2704
 */
class ItemTypeSelectViewModel extends MenuItemViewModel implements ItemTypeSelectViewModelInterface {

    /**
     * @return MenuItemTypeInterface array of
     */
    public function getTypeTransitions() {
        return ['paper', 'article', 'multipage', 'static', 'eventcontent'];
    }

    public function getIterator(): \Traversable {
        $this->appendData(
                [
                    'menuItem' => $this->getStatus()->getPresentedMenuItem(),
                    'typeTransitions' => $this->getTypeTransitions()
                ]
                );
        return parent::getIterator();
    }
}
