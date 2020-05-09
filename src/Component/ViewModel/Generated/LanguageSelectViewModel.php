<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Generated;

use Model\Repository\LanguageRepo;
use Model\Repository\StatusPresentationRepo;

/**
 * Description of LanguageSelectViewModel
 *
 * @author pes2704
 */
class LanguageSelectViewModel implements LanguageSelectViewModelInterface {

    private $languageRepo;

    private $statusPresentationRepo;

    public function __construct(LanguageRepo $languageRepo, StatusPresentationRepo $statusPresentationRepo) {
        $this->languageRepo = $languageRepo;
        $this->statusPresentationRepo = $statusPresentationRepo;
    }

    public function getLanguages() {
        return $this->languageRepo->find();
    }

    public function getPresentedLangCode() {
        return $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
    }
}
