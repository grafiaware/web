<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Repository;

use Model\Repository\RepoInterface;

use Red\Model\Entity\MenuItemApiInterface;

/**
 *
 * @author pes2704
 */
interface MenuItemApiRepoInterface extends RepoInterface  {

    /**
     * Vrací MenuItemApiInterface nebo null
     * 
     * @param type $module
     * @param type $generator
     * @return MenuItemApiInterface|null
     */
    public function get($module, $generator): ?MenuItemApiInterface;

    /**
     * Vrací pole entit MenuItemApiInterface.
     * @return MenuItemApiInterface array of
     */
    public function findAll();
    
    /**
     *
     * @param MenuItemApiInterface $menuItemApi
     */
    public function add(MenuItemApiInterface $menuItemApi);

    /**
     *
     * @param MenuItemApiInterface $menuItemApi
     */
    public function remove(MenuItemApiInterface $menuItemApi);

}
