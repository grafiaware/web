<?php
namespace Component\ViewModel\Authored\Paper;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Model\Entity\MenuNodeInterface;

use Model\Entity\Paper;
use Model\Entity\PaperInterface;


/**
 * Description of PresentedPaperViewModel
 *
 * @author pes2704
 */
class PresentedPaperViewModel extends PaperViewModelAbstract implements PresentedPaperViewModelInterface {

    /**
     * Vrací prezentovanou položku menu. Řídí se hodnotami vlastností objektu PresentationStatus.
     *
     * @return MenuNodeInterface|null
     */
    public function getMenuNode(): ?MenuNodeInterface {
        $presentationStatus = $this->getStatusPresentation();
        $this->menuRepo->setOnlyPublishedMode($this->presentOnlyPublished());
        return $this->menuRepo->get($presentationStatus->getLanguage()->getLangCode(), $presentationStatus->getItemUid());
    }

    /**
     * Vrací paper odpovídající prezentované položce menu. Řídí se hodnotami vlastností objektu PresentationStatus.
     *
     * @return PaperInterface|null
     */
    public function getPaper(): ?PaperInterface {
        $menuNode = $this->getMenuNode();
        return isset($menuNode)
                    ? ($this->paperRepo->get($menuNode->getMenuItem()->getId()) ?? (new Paper())->setMenuItemIdFk($menuNode->getMenuItem()->getId())->setLangCode($this->getPresentationStatus()->getLanguage()))
                    : null;
    }
}
