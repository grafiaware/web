<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Authored\Paper;

use Model\Entity\PaperAggregateInterface;
use Model\Entity\MenuItemInterface;


/**
 *
 * @author pes2704
 */
interface ItemPaperViewModelInterface extends PaperViewModelInterface {

    public function setItemId($menuItemId);

    /**
     * Vrací paper odpovídajíví prezentované položce menu. Řídí se hodnotami vlastností objektu PresentationStatus.
     *
     * @return PaperAggregateInterface
     */
    public function getPaperAggregate(): ?PaperAggregateInterface;


}
