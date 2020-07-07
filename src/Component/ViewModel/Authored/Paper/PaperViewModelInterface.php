<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Authored\Paper;

use Component\ViewModel\Authored\AuthoredViewModelInterface;
use Model\Entity\PaperAggregateInterface;

/**
 *
 * @author pes2704
 */
interface PaperViewModelInterface extends AuthoredViewModelInterface {

    /**
     * Metoda nemá parametr. Vrací menuItemAggregate odpovídající položce menu, zapsané v databázi jako komponenta se se jménem komponenty zadaným metodou setComponentName($componentName).
     *
     * @return PaperAggregateInterface|null
     */
    public function getPaperAggregate(): ?PaperAggregateInterface;

}
