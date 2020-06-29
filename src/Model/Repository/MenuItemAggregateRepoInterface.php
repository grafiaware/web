<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Entity\MenuItemPaperAggregateInterface;

/**
 *
 * @author pes2704
 */
interface MenuItemAggregateRepoInterface  extends MenuItemRepoInterface {
    public function get($langCodeFk, $uidFk): ?MenuItemPaperAggregateInterface;

}
