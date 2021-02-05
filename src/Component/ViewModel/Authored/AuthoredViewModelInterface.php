<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Authored;

use Component\ViewModel\ViewModelInterface;

/**
 *
 * @author pes2704
 */
interface AuthoredViewModelInterface extends ViewModelInterface {
    /**
     * Prezentuj položky v editovatelné podobě
     * @return bool
     */
    public function isArticleEditable();

    /**
     * Prezentuj pouze publikované položky
     * @return bool
     */
    public function presentOnlyPublished();

    public function getFlashCommand($key);

    public function getPostFlashCommand($key);
}
