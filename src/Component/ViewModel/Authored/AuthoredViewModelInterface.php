<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Authored;

use Component\ViewModel\StatusViewModelInterface;

/**
 *
 * @author pes2704
 */
interface AuthoredViewModelInterface extends StatusViewModelInterface {

    public function setItemId($menuItemId);

    /**
     * Informuje zda prezentace je v editovatelném režimu a současně prezentovaný obsah je editovatelný přihlášeným uživatelem.
     *
     * @return bool
     */
    public function userCanEdit(): bool;

}
