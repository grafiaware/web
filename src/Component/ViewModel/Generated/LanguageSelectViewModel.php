<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Generated;

use Component\ViewModel\StatusViewModelAbstract;

use Model\Repository\StatusSecurityRepo;
use Model\Repository\StatusPresentationRepo;
use Model\Repository\StatusFlashRepo;

use Model\Repository\LanguageRepo;

/**
 * Description of LanguageSelectViewModel
 *
 * @author pes2704
 */
class LanguageSelectViewModel extends StatusViewModelAbstract implements LanguageSelectViewModelInterface {

    private $languageRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo,
            StatusFlashRepo $statusFlashRepo,
            LanguageRepo $languageRepo) {
        parent::__construct($statusSecurityRepo, $statusPresentationRepo, $statusFlashRepo);
        $this->languageRepo = $languageRepo;
    }

    public function getLanguages() {
        return $this->languageRepo->find();
    }

    public function getPresentedLangCode() {
        return $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
    }
}
