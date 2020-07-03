<?php
namespace Component\ViewModel\Authored\Paper;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Model\Entity\HierarchyNodeInterface;

use Model\Entity\MenuItemPaperAggregate;
use Model\Entity\MenuItemPaperAggregateInterface;


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
    public function getMenuItemPaperAggregate(): ?MenuItemPaperAggregateInterface {
        $langCode = $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
        $uid = $this->statusPresentationRepo->get()->getMenuItem()->getUidFk();


        return $this->menuItemAggregateRepo->get($langCode, $uid)
                    ??
                    (new MenuItemPaperAggregate())->setMenuItemIdFk($uid)->setLangCode($langCode);
    }
}
