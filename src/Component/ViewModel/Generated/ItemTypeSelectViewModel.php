<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Generated;

use Component\ViewModel\ComponentViewModelAbstract;

use Model\Repository\StatusPresentationRepo;
use Model\Repository\MenuItemTypeRepo;

use Model\Entity\MenuItemTypeInterface;
use Model\Entity\MenuItemInterface;
/**
 * Description of LanguageSelect
 *
 * @author pes2704
 */
class ItemTypeSelectViewModel implements ItemTypeSelectViewModelInterface {

    /**
     * @var StatusPresentationRepo
     */
    protected $statusPresentationRepo;

    /**
     * @var MenuItemTypeRepo
     */
    private $menuItemTypeRepo;

    public function __construct(
            StatusPresentationRepo $statusPresentationRepo,
            MenuItemTypeRepo $menuItemTypeRepo
            ) {
        $this->statusPresentationRepo = $statusPresentationRepo;
        $this->menuItemTypeRepo = $menuItemTypeRepo;
    }

    /**
     *
     * @return MenuItemTypeInterface array of
     */
    public function getTypes() {
        return $this->menuItemTypeRepo->findAll();
    }


    /**
     * Vrací menuItem odpovídající prezentované položce menu. Řídí se hodnotami vlastností objektu PresentationStatus.
     *
     * @return MenuItemInterface|null
     */
    public function getMenuItem(): ?MenuItemInterface {
        return $this->statusPresentationRepo->get()->getMenuItem();
    }
}
