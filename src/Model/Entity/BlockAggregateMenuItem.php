<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Entity;

/**
 * Description of Article
 *
 * @author pes2704
 */
class BlockAggregateMenuItem extends Block implements BlockAggregateMenuItemInterface {

    private $langCode;

    /**
     * @var MenuItemInterface
     */
    private $menuItem;

    public function getLangCode() {
        return $this->langCode;
    }

    public function setLangCode($langCode): BlockAggregateMenuItemInterface {
        $this->langCode = $langCode;
        return $this;
    }

    /**
     * Vrací MenuItemInterface nebo null - komponenta obsahuje item, kerý není aktivní nebo aktuální
     * @return \Model\Entity\MenuItemInterface|null
     */
    public function getMenuItem(): ?MenuItemInterface {
        return $this->menuItem;
    }

    public function setMenuItem(MenuItemInterface $menuItem): BlockAggregateMenuItemInterface {
        $this->menuItem = $menuItem;
        return $this;
    }

}
