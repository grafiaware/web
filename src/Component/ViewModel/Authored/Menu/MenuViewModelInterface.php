<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Authored\Menu;

use Component\ViewModel\Authored\AuthoredViewModelInterface;

use Model\Entity\MenuNodeInterface;
use Model\Entity\MenuRootInterface;

/**
 *
 * @author pes2704
 */
interface MenuViewModelInterface extends AuthoredViewModelInterface {

    /**
     *
     * @return MenuNodeInterface
     */
    public function getPresentedMenuNode();

    /**
     *
     * @param string $menuRootName
     * @return MenuRootInterface
     */
    public function getMenuRoot($menuRootName);
    
    /**
     *
     * @param string $nodeUid
     * @return MenuNodeInterface
     */
    public function getMenuNode($nodeUid) ;

    /**
     *
     * @param string $parentUid
     * @return MenuNodeInterface array af
     */
    public function getChildrenMenuNodes($parentUid);

    /**
     *
     * @param type $rootUid
     * @param type $maxDepth
     * @return ItemViewModelInterface array af
     */
    public function getSubTreeItemModels($rootUid, $maxDepth=NULL);
}
