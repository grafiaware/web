<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Service\ItemCreator;


/**
 *
 * @author pes2704
 */
interface ItemCreatorInterface {
    public function initialize($menuItemIdFk): void;
}