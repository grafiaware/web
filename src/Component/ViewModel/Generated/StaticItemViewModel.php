<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Generated;

use Component\ViewModel\StatusViewModel;

use Status\Model\Repository\StatusSecurityRepo;
use Status\Model\Repository\StatusPresentationRepo;
use Status\Model\Repository\StatusFlashRepo;

use Red\Model\Repository\MenuItemTypeRepo;

use Red\Model\Entity\MenuItemTypeInterface;
use Red\Model\Entity\MenuItemInterface;
/**
 * Description of LanguageSelect
 *
 * @author pes2704
 */
class StaticItemViewModel extends StatusViewModel implements StaticItemViewModelInterface {

    /**
     * @var MenuItemTypeRepo
     */
    private $menuItemTypeRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo,
            StatusFlashRepo $statusFlashRepo,
            MenuItemTypeRepo $menuItemTypeRepo
            ) {
        parent::__construct($statusSecurityRepo, $statusPresentationRepo, $statusFlashRepo);
        $this->menuItemTypeRepo = $menuItemTypeRepo;
    }

    /**
     * Vrací menuItem odpovídající prezentované položce menu. Řídí se hodnotami vlastností objektu PresentationStatus.
     *
     * @return MenuItemInterface|null
     */
    public function getMenuItem(): ?MenuItemInterface {
        return $this->statusPresentationRepo->get()->getMenuItem();
    }

    public function getIterator(): \Traversable {
        $this->appendData(
                [
                    'menuItem' => $this->getMenuItem()
                ]
                );
        return parent::getIterator();
    }
}
