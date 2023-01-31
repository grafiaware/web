<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\ViewModel\Generated;

use Red\Component\ViewModel\ViewModelInterface;

/**
 *
 * @author pes2704
 */
interface LanguageSelectViewModelInterface extends ViewModelInterface {
    public function getLanguages();
    public function getPresentedLangCode();
}
