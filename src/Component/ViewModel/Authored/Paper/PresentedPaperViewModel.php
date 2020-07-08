<?php
namespace Component\ViewModel\Authored\Paper;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Model\Entity\PaperAggregateInterface;


/**
 * Description of PresentedPaperViewModel
 *
 * @author pes2704
 */
class PresentedPaperViewModel extends PaperViewModelAbstract implements PresentedPaperViewModelInterface {

    /**
     * Vrací paper odpovídající prezentované položce menu. Řídí se hodnotami vlastností objektu PresentationStatus.
     *
     * @return MenuItemPaperAggregateInterface|null
     */
    public function getPaperAggregate(): ?PaperAggregateInterface {
        $menuItemId = $this->statusPresentationRepo->get()->getMenuItem()->getId();
        return $this->paperAggregateRepo->get($menuItemId);
    }
}
