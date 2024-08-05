<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\ViewModel\Content\Authored\Paper;

use Red\Component\ViewModel\Content\Authored\AuthoredViewModelInterface;

use Red\Model\Entity\PaperAggregatePaperSectionInterface;

/**
 *
 * @author pes2704
 */
interface PaperViewModelInterface extends AuthoredViewModelInterface {

    /**
     * Vrací PaperAggregate, pokud existuje a je aktivní (zveřejněný) nebo prezentace je v editačním režimu.
     *
     * @return PaperAggregatePaperSectionInterface|null
     */
    public function getPaper(): ?PaperAggregatePaperSectionInterface;
    public function isSectionPasteMode(): bool;
}
