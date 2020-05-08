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
 *
 * @author pes2704
 */
interface ComponentViewModelInterface {
    
    public function getStatusPresentationRepo(): StatusPresentationRepo;

    public function getStatusFlashRepo(): StatusFlashRepo;

    public function getLanguageRepo(): LanguageRepo;

    public function getMenuRepo(): MenuRepo;

    public function getMenuRootRepo(): MenuRootRepo;

    public function getMenuItemRepo(): MenuItemRepo;
}
