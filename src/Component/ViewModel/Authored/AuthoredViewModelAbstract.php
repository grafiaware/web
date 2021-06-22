<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Authored;

use Component\ViewModel\StatusViewModelAbstract;

/**
 * Description of AuthoredViewModelAbstract
 *
 * @author pes2704
 */
abstract class AuthoredViewModelAbstract extends StatusViewModelAbstract implements AuthoredViewModelInterface {

    /**
     *
     * @return bool
     */
    public function presentOnlyPublished() {
        return ! $this->isArticleEditable();  //negace
    }

}
