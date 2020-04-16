<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Authored\Paper;

use Component\ViewModel\Authored\Paper\PaperViewModelInterface;

use Model\Entity\ComponentInterface;
use Model\Entity\MenuNodeInterface;
use Model\Entity\PaperInterface;

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
    public function getComponent();

    /**
     * Metoda nemá parametr. Vrací položku menu odpovídající komponentě se jménem zadaným metodou setComponentName($componentName).
     * @return MenuNodeInterface
     */
    public function getMenuNode();

    /**
     * Metoda nemá parametr. Vrací paper odpovídající položce menu, zapsané v databázi jako komponenta se se jménem komponenty zadaným metodou setComponentName($componentName).
     *
     * @return PaperInterface
     */
    public function getPaper();
}
