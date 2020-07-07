<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Entity\MenuItemInterface;

/**
 *
 * @author pes2704
 */
interface MenuItemRepoInterface extends RepoInterface, RepoAssotiatedOneInterface  {

    /**
     *
     * @param type $langCodeFk
     * @param type $uidFk
     * @return MenuItemInterface|null
     */
    public function get($langCodeFk, $uidFk): ?MenuItemInterface;

    /**
     *
     * @param MenuItemInterface $menuItem
     */
    public function add(MenuItemInterface $menuItem);

    /**
     *
     * @param MenuItemInterface $menuItem
     */
    public function remove(MenuItemInterface $menuItem);

    /**
     *
     * @param string $langCodeFk
     * @param string $text
     * @return MenuItemInterface array of
     */
    public function findByPaperFulltextSearch($langCodeFk, $text);

}
