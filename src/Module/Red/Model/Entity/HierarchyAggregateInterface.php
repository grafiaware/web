<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Entity;

use Red\Model\Entity\MenuItemInterface;

/**
 *
 * @author pes2704
 */
interface HierarchyAggregateInterface extends HierarchyInterface {
    public function getMenuItem(): MenuItemInterface;

    public function setMenuItem(MenuItemInterface $menuItem): HierarchyAggregateInterface;
}
