<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Entity;

/**
 *
 * @author pes2704
 */
interface BlockAggregateMenuItemInterface extends BlockInterface {

    public function getLangCode();

    public function setLangCode($langCode): BlockAggregateMenuItemInterface;

    /**
     * Vrací MenuItemInterface nebo null - komponenta obsahuje item, kerý není aktivní nebo aktuální
     * @return \Model\Entity\MenuItemInterface|null
     */
    public function getMenuItem(): ?MenuItemInterface;

    public function setMenuItem(MenuItemInterface $menuItem): BlockAggregateMenuItemInterface;
}
