<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\View\Authored\Paper;

use Component\View\Authored\Paper\AuthoredComponentInterface;

/**
 *
 * @author pes2704
 */
interface ItemComponentInterface extends AuthoredComponentInterface {

    public function setItemParams($langCodeFk, $uidFk): ItemComponentInterface;
}
