<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Authored\Paper;

use Component\ViewModel\Authored\Paper\PaperViewModelInterface;

use Model\Entity\ComponentInterface;
use Model\Entity\HierarchyNodeInterface;
use Model\Entity\MenuItemPaperAggregateInterface;

/**
 *
 * @author pes2704
 */
interface NamedPaperViewModelInterface extends PaperViewModelInterface {

    public function setComponentName($componentName);

    /**
     * Vrací entitu komponenty.
     * @return ComponentInterface
     */
    public function getComponent(): ComponentInterface;

    /**
     * Metoda nemá parametr. Vrací paper odpovídající položce menu, zapsané v databázi jako komponenta se se jménem komponenty zadaným metodou setComponentName($componentName).
     *
     * @return MenuItemPaperAggregateInterface
     */
    public function getMenuItemPaperAggregate(): MenuItemPaperAggregateInterface;
}
