<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Entity;

/**
 *
 * @author pes2704
 */
interface ComponentAggregateInterface {

    public function getLangCode();

    public function setLangCode($langCode): ComponentAggregateInterface;

    public function getMenuItem(): MenuItemInterface;

    public function setMenuItem(MenuItemInterface $menuItem): ComponentAggregateInterface;
}
