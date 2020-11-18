<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Authored\Paper;

use Component\ViewModel\Authored\AuthoredViewModelInterface;
use Model\Entity\PaperAggregateInterface;
use Model\Entity\MenuItemInterface;

/**
 *
 * @author pes2704
 */
interface PaperViewModelInterface extends AuthoredViewModelInterface {

    /**
     *
     * @return MenuItemInterface
     */
    public function getMenuItem(): ?MenuItemInterface;

    /**
     * Vrací informaci o komponentě pro vložení informativního textu do výsledného html.
     * @return string
     */
    public function getInfo();

    /**
     * Vrací PaperAggregate příslušný k menuItem. MenuItem poskytuje metoda konponenty getMenuItem().
     * Pokud PaperAggregate dosud neexistuje (není persitován, není vrácen z repository) vytvoří nový objekt PaperAggregate.
     *
     * @return PaperAggregateInterface|null
     */
    public function getPaperAggregate(): ?PaperAggregateInterface;
}
