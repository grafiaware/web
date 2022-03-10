<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\MenuItem\Authored\Paper;

use Component\ViewModel\MenuItem\Authored\AuthoredViewModelInterface;

use Red\Model\Entity\PaperAggregatePaperContentInterface;

/**
 *
 * @author pes2704
 */
interface PaperViewModelInterface extends AuthoredViewModelInterface {

    /**
     * Vrací PaperAggregate, pokud existuje a je aktivní (zveřejněný) nebo prezentace je v editačním režimu.
     *
     * @return PaperAggregatePaperContentInterface|null
     */
    public function getPaper(): ?PaperAggregatePaperContentInterface;

}
