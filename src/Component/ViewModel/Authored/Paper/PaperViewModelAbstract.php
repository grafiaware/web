<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Authored\Paper;

use Component\ViewModel\Authored\AuthoredViewModelInterface;

use Model\Repository\StatusSecurityRepo;
use Model\Repository\StatusPresentationRepo;
use Model\Repository\StatusFlashRepo;
use Model\Repository\LanguageRepo;
use Model\Repository\MenuRepo;
use Model\Repository\MenuRootRepo;

use Model\Repository\ComponentRepo;
use Model\Repository\PaperRepo;

use Component\ViewModel\Authored\AuthoredViewModelAbstract;

/**
 * Description of PaperViewModelAnstract
 *
 * @author pes2704
 */
abstract class PaperViewModelAbstract extends AuthoredViewModelAbstract implements AuthoredViewModelInterface {

    /**
     * @var PaperRepo
     */
    protected $paperRepo;

    /**
     * @var ComponentRepo
     */
    protected $componentRepo;

    public function __construct(
            StatusPresentationRepo $presentationStatusRepo,
            StatusFlashRepo $flashStatusRepo,
            LanguageRepo $languageRepo,
            MenuRepo $menuRepo,
            MenuRootRepo $menuRootRepo,
            PaperRepo $paperRepo,
            ComponentRepo $componentRepo
            ) {
        parent::__construct($presentationStatusRepo, $flashStatusRepo, $languageRepo, $menuRepo, $menuRootRepo);
        $this->paperRepo = $paperRepo;
        $this->componentRepo = $componentRepo;
    }

}
