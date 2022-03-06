<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Generated;

use Component\ViewModel\MenuItemViewModel;

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
        $itemType = $this->getMenuItem()->getTypeFk();
        $typeTransitions = [
            'root' => '',
            'empty' => ['static', 'paper', 'article', 'multipage'],
            'redirect' => '',
            'static' => '',
            'paper' => '',
            'article' => '',
            'trash' => '',
            'generated' => ''
        ];
        if (array_key_exists($itemType, $typeTransitions)) {
            return $typeTransitions[$itemType];
        } else {
            throw new \UnexpectedValueException("No transitions for menu item type '$itemType'.");
        }
    }

    public function getIterator(): \Traversable {
        $this->appendData(
                [
                    'menuItem' => $this->getMenuItem(),
                    'typeTransitions' => $this->getTypeTransitions()
                ]
                );
        return parent::getIterator();
    }
}
