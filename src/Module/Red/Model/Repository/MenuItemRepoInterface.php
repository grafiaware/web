<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Repository;

use Model\Repository\RepoInterface;
use Model\Repository\RepoAssotiatedOneInterface;

use Red\Model\Entity\MenuItemInterface;

/**
 *
 * @author pes2704
 */
interface MenuItemRepoInterface extends RepoInterface, RepoAssotiatedOneInterface  {

    /**
     * Hledá podle primárního klíče a podle kontextu.
     *
     * @param type $langCodeFk
     * @param type $uidFk
     * @return MenuItemInterface|null
     */
    public function get($langCodeFk, $uidFk): ?MenuItemInterface;

    /**
     * Hledá pouze podle primárního klíče bez použití kontextu.
     *
     * @param type $langCodeFk
     * @param type $uidFk
     * @return MenuItemInterface|null
     */
    public function getOutOfContext($langCodeFk, $uidFk): ?MenuItemInterface;

    /**
     *
     * @param type $id
     * @return MenuItemInterface|null
     */
    public function getById($id): ?MenuItemInterface;

    /**
     *
     * @param type $prettyUri
     * @return MenuItemInterface|null
     */
    public function getByPrettyUri($prettyUri): ?MenuItemInterface;

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
     * @param type $uidFk
     * @return iterable
     */
    public function findAllLanguageVersions($uidFk): iterable;

    /**
     *
     * @param string $langCodeFk
     * @param string $text
     * @return MenuItemInterface array of
     */
    public function findByPaperFulltextSearch($langCodeFk, $text);

}
