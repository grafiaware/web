<?php
namespace Component\ViewModel\Authored\Paper;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Model\Entity\PaperAggregateInterface;
use Model\Entity\MenuItemInterface;

/**
 * Description of PresentedPaperViewModel
 *
 * @author pes2704
 */
class PresentedPaperViewModel extends PaperViewModelAbstract implements PresentedPaperViewModelInterface {

    public function getPresentedMenuItem(): \Model\Entity\MenuItemInterface {
        return $this->statusPresentationRepo->get()->getMenuItem();
    }


    /**
     * Vrací paper odpovídající prezentované položce menu. Řídí se hodnotami vlastností objektu PresentationStatus.
     *
     * @return MenuItemPaperAggregateInterface|null
     */
    public function getPaperAggregate(): ?PaperAggregateInterface {
        $menuItemId = $this->getPresentedMenuItem()->getId();
        return $this->paperAggregateRepo->getByReference($menuItemId);
    }
}
