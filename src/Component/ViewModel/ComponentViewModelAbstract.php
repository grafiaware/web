<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel;

use Model\Repository\{
    StatusPresentationRepo, StatusFlashRepo, LanguageRepo, MenuRepo, MenuRootRepo, MenuItemRepo
};

/**
 * Description of ComponentViewModelAbstract
 *
 * @author pes2704
 */
class ComponentViewModelAbstract implements ComponentViewModelInterface {

    /**
     * @var StatusPresentationRepo
     */
    protected $statusPresentationRepo;

    /**
     * @var StatusFlashRepo
     */
    protected $statusFlashRepo;

    /**
     * @var LanguageRepo
     */
    protected $languageRepo;

    /**
     * @var MenuRepo
     */
    protected $menuRepo;

    /**
     * @var MenuRootRepo
     */
    protected $menuRootRepo;

    /**
     * @var MenuItemRepo
     */
    protected $menuItemRepo;

    public function __construct(
            StatusPresentationRepo $statusPresentationRepo,
            StatusFlashRepo $statusFlashRepo,
            LanguageRepo $languageRepo,
            MenuRepo $menuRepo,
            MenuRootRepo $menuRootRepo,
            MenuItemRepo $menuItemRepo
            ) {
        $this->statusPresentationRepo = $statusPresentationRepo;
        $this->statusFlashRepo = $statusFlashRepo;
        $this->languageRepo = $languageRepo;
        $this->menuRepo = $menuRepo;
        $this->menuRootRepo = $menuRootRepo;
        $this->menuItemRepo = $menuItemRepo;

    }

    public function getStatusPresentationRepo(): StatusPresentationRepo {
        return $this->statusPresentationRepo;
    }

    public function getStatusFlashRepo(): StatusFlashRepo {
        return $this->statusFlashRepo;
    }

    public function getLanguageRepo(): LanguageRepo {
        return $this->languageRepo;
    }

    public function getMenuRepo(): MenuRepo {
        return $this->menuRepo;
    }

    public function getMenuRootRepo(): MenuRootRepo {
        return $this->menuRootRepo;
    }

    public function getMenuItemRepo(): MenuItemRepo {
        return $this->menuItemRepo;
    }

}
