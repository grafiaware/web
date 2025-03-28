<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\ViewModel\Generated;

use Component\ViewModel\ViewModelAbstract;
use Component\ViewModel\StatusViewModelInterface;

use Red\Model\Repository\LanguageRepo;

/**
 * Description of LanguageSelectViewModel
 *
 * @author pes2704
 */
class LanguageSelectViewModel extends ViewModelAbstract implements LanguageSelectViewModelInterface {

    private $status;

    private $languageRepo;

    public function __construct(
            StatusViewModelInterface $status,
            LanguageRepo $languageRepo) {
        $this->status = $status;
        $this->languageRepo = $languageRepo;
    }

    public function getLanguages() {
        return $this->languageRepo->findAll();
    }

    public function getPresentedLangCode() {
        return $this->status->getPresentedLanguageCode();
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
