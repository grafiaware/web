<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Entity;

use Model\Entity\EntityAbstract;

use Red\Model\Entity\MenuItemInterface;

/**
 *
 * Description of MenuNode
 *
 * @author pes2704
 */
class HierarchyAggregate extends Hierarchy implements HierarchyAggregateInterface {

    /**
     * @var MenuItemInterface
     */
    private $menuItem;

    public function getMenuItem(): MenuItemInterface {
        return $this->menuItem;
    }

    public function setMenuItem(MenuItemInterface $menuItem): HierarchyAggregateInterface {
        $this->menuItem = $menuItem;
        return $this;
    }
}
