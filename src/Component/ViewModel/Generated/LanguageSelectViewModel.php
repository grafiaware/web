<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Generated;

use StatusModel\StatusPresentationModel;

/**
 * Description of LanguageSelectViewModel
 *
 * @author pes2704
 */
class LanguageSelectViewModel extends StatusPresentationModel implements GeneratedViewModelInterface {

    public function getLanguages() {
        return $this->languageRepo->find();
    }
}
