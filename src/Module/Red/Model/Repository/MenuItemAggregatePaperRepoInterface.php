<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Repository;

use Model\Repository\RepoReadonlyInterface;
use Red\Model\Entity\MenuItemAggregatePaperInterface;

/**
 *
 * @author pes2704
 */
interface MenuItemAggregatePaperRepoInterface  extends RepoReadonlyInterface {

    /**
     *
     * @param type $langCodeFk
     * @param type $uidFk
     * @return MenuItemAggregatePaperInterface|null
     */
    public function get($langCodeFk, $uidFk): ?MenuItemAggregatePaperInterface;

    /**
     *
     * @param type $id
     * @return MenuItemAggregatePaperInterface|null
     */
    public function getById($id): ?MenuItemAggregatePaperInterface;

    /**
     *
     * @param type $prettyUri
     * @return MenuItemAggregatePaperInterface|null
     */
    public function getByPrettyUri($prettyUri): ?MenuItemAggregatePaperInterface;
}
