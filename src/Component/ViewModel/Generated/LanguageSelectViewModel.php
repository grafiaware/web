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
use Red\Model\Repository\ItemActionRepo;

use Red\Model\Repository\LanguageRepo;

/**
 * Description of LanguageSelectViewModel
 *
 * @author pes2704
 */
class LanguageSelectViewModel extends StatusViewModel implements LanguageSelectViewModelInterface {

    private $languageRepo;

    public function __construct(
            StatusSecurityRepo $statusSecurityRepo,
            StatusPresentationRepo $statusPresentationRepo,
            StatusFlashRepo $statusFlashRepo,
            ItemActionRepo $itemActionRepo,
            LanguageRepo $languageRepo) {
        parent::__construct($statusSecurityRepo, $statusPresentationRepo, $statusFlashRepo, $itemActionRepo);
        $this->languageRepo = $languageRepo;
    }

    public function getLanguages() {
        return $this->languageRepo->findAll();
    }

    public function getPresentedLangCode() {
        return $this->statusPresentationRepo->get()->getLanguage()->getLangCode();
    }

    public function getIterator(): \Traversable {
        $this->appendData(
                [
                    'languages' => $this->getLanguages(),
                    'presentedLangCode' => $this->getPresentedLangCode()
                ]
                );
        return parent::getIterator();
    }
}
