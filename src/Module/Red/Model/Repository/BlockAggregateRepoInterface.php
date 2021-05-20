<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Repository;

use Red\Model\Entity\BlockAggregateMenuItemInterface;

/**
 *
 * @author pes2704
 */
interface BlockAggregateRepoInterface extends BlockRepoInterface {

    /**
     * Vrací ComponentAggregate - agrugát Component a MenuItem. Parametr $langCode je pouze použit pro výběr MenuItem.
     * @param type $langCode
     * @param type $name
     * @return BlockAggregateMenuItemInterface|null
     */
    public function getAggregate($langCode, $name): ?BlockAggregateMenuItemInterface;

}
