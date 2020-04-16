<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Authored;

use StatusModel\StatusPresentationModelInterface;

/**
 *
 * @author pes2704
 */
interface AuthoredViewModelInterface extends StatusPresentationModelInterface {
    /**
     * Prezentuj položky v editovatelné podobě
     * @return bool
     */
    public function userEdit();

    /**
     * Prezentuj pouze publikované položky
     * @return bool
     */
    public function presentOnlyPublished();

}
