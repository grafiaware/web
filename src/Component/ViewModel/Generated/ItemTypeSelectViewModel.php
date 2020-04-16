<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Generated;

use StatusModel\StatusPresentationModel;

use Model\Repository\StatusSecurityRepo;
use Model\Repository\StatusPresentationRepo;
use Model\Repository\StatusFlashRepo;
use Model\Repository\LanguageRepo;
use Model\Repository\MenuRepo;
use Model\Repository\MenuRootRepo;
use Model\Repository\LanguageRepo;

use Model\Repository\MenuItemTypeRepo;

use Model\Entity\MenuItemTypeInterface;

/**
 * Description of LanguageSelect
 *
 * @author pes2704
 */
class ItemTypeSelectViewModel extends StatusPresentationModel implements GeneratedViewModelInterface {

    /**
     *
     * @var MenuItemTypeRepo
     */
    private $menuItemTypeRepo;

    public function __construct(
            StatusSecurityRepo $securityStatusRepo,
            StatusPresentationRepo $presentationStatusRepo,
            StatusFlashRepo $flashStatusRepo,
            MenuRepo $menuRepo,
            MenuRootRepo $menuRootRepo,
            LanguageRepo $languageRepo,
            MenuItemTypeRepo $menuItemTypeRepo
            ) {
        parent::__construct($presentationStatusRepo, $flashStatusRepo, $languageRepo, $menuRepo, $menuRootRepo);
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
