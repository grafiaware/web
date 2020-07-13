<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Generated;

use Component\ViewModel\ComponentViewModelAbstract;

use Model\Repository\MenuItemTypeRepo;

use Model\Entity\MenuItemTypeInterface;

/**
 * Description of LanguageSelect
 *
 * @author pes2704
 */
class ItemTypeSelectViewModel implements ItemTypeSelectViewModelInterface {

    /**
     * @var MenuItemTypeRepo
     */
    private $menuItemTypeRepo;

    public function __construct(
            MenuItemTypeRepo $menuItemTypeRepo
            ) {
        $this->menuItemTypeRepo = $menuItemTypeRepo;
    }

    /**
     *
     * @return MenuItemTypeInterface array of
     */
    public function getTypes() {
        return $this->menuItemTypeRepo->findAll();
    }


}
