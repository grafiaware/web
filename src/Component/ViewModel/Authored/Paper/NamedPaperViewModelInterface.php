<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Authored\Paper;

use Component\ViewModel\Authored\Paper\PaperViewModelInterface;

use Model\Entity\ComponentAggregateInterface;

/**
 *
 * @author pes2704
 */
interface NamedPaperViewModelInterface extends PaperViewModelInterface {

    public function setComponentName($componentName);

    /**
     *
     * @return ComponentAggregateInterface|null
     */
    public function getComponentAggregate(): ?ComponentAggregateInterface;
}
