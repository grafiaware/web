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
class ComponentAggregate extends Component implements ComponentAggregateInterface {

    private $langCode;

    /**
     * @var MenuItemInterface
     */
    private $menuItem;

    public function getLangCode() {
        return $this->langCode;
    }

    public function setLangCode($langCode): ComponentAggregateInterface {
        $this->langCode = $langCode;
        return $this;
    }

    /**
     *
     * @return MenuItemInterface
     */
    public function getMenuItem(): MenuItemInterface {
        return $this->menuItem;
    }

    public function setMenuItem(MenuItemInterface $menuItem): ComponentAggregateInterface {
        $this->menuItem = $menuItem;
        return $this;
    }

}
