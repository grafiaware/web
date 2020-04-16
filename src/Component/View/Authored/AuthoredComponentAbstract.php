<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View\Authored;

use Pes\View\View;

use Component\View\ComponentAbstract;

/**
 * Description of AuthoredComponentAbstract
 *
 * @author pes2704
 */
abstract class AuthoredComponentAbstract extends ComponentAbstract implements AuthoredComponentInterface {

    /**
     * @var bool
     */
    protected $editable;

    public function seEditable($editable) {
        $this->editable = $editable;
    }
}
