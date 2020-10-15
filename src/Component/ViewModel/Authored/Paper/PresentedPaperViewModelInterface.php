<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Authored\Paper;

use Component\ViewModel\Authored\Paper\PaperViewModelInterface;

use Model\Entity\PaperAggregateInterface;
use Model\Entity\MenuItemInterface;


/**
 *
 * @author pes2704
 */
interface PresentedPaperViewModelInterface extends PaperViewModelInterface {

    /**
     * Vrací paper odpovídajíví prezentované položce menu. Řídí se hodnotami vlastností objektu PresentationStatus.
     *
     * @return PaperAggregateInterface
     */
    public function getPaperAggregate(): ?PaperAggregateInterface;


}
