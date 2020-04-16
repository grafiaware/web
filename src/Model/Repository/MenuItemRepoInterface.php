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
interface MenuItemRepoInterface extends RepoInterface {

    public function setOnlyPublishedMode($onlyPublished = true): void ;

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
     * @param string $langCodeFk
     * @param string $text
     * @param bool $active Nepovinný parametr, default=TRUE. Defaultně metoda hledá jen aktivní (zveřejněné) položky.
     * @param bool $actual Nepovinný parametr, default=TRUE. Defaultně metoda hledá jen aktuální položky.
     * @return MenuItemInterface array of
     */
    public function findByPaperFulltextSearch($langCodeFk, $text, $active=\TRUE, $actual=\TRUE);

}
