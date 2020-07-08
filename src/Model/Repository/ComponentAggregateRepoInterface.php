<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Entity\ComponentAggregateInterface;

/**
 *
 * @author pes2704
 */
interface ComponentAggregateRepoInterface extends ComponentRepoInterface {

    /**
     * Vrací ComponentAggregate - agrugát Component a MenuItem. Parametr $langCode je pouze použit pro výběr MenuItem.
     * @param type $langCode
     * @param type $name
     * @return ComponentAggregateInterface|null
     */
    public function getAggregate($langCode, $name): ?ComponentAggregateInterface;

}
