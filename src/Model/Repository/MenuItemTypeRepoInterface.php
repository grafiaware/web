<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Entity\MenuItemTypeInterface;

/**
 *
 * @author pes2704
 */
interface MenuItemTypeRepoInterface extends RepoInterface  {

    /**
     * Vrací MenuItemTypeInterface nebo null.
     *
     * @param string $type Hodnota identifikátoru type.
     * @return MenuItemTypeInterface|null
     */
    public function get($type): ?MenuItemTypeInterface;

    /**
     * Vrací pole entit MenuItemTypeInterface.
     * @return MenuItemTypeInterface array of
     */
    public function findAll();
    /**
     *
     * @param MenuItemTypeInterface $menuItemType
     */
    public function add(MenuItemTypeInterface $menuItemType);

    /**
     *
     * @param MenuItemTypeInterface $menuItemType
     */
    public function remove(MenuItemTypeInterface $menuItemType);

}
