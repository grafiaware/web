<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Authored\Paper;

use Component\ViewModel\Authored\AuthoredViewModelInterface;
use Model\Entity\HierarchyNodeInterface;
use Model\Entity\MenuItemPaperAggregateInterface;

/**
 *
 * @author pes2704
 */
interface PaperViewModelInterface extends AuthoredViewModelInterface {

    /**
     * Vrací paper.
     *
     * @return MenuItemPaperAggregateInterface
     */
    public function getMenuItemPaperAggregate();

}
