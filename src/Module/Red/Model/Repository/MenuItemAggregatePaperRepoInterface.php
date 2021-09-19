<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Repository;

use Red\Model\Entity\MenuItemAggregatePaperInterface;

/**
 *
 * @author pes2704
 */
interface MenuItemAggregatePaperRepoInterface  extends MenuItemRepoInterface {
    public function get($langCodeFk, $uidFk): ?MenuItemAggregatePaperInterface;

}
