<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Authored\Menu;

use Component\ViewModel\Authored\AuthoredViewModelInterface;

use Model\Entity\HierarchyAggregateInterface;
use Model\Entity\MenuRootInterface;

/**
 *
 * @author pes2704
 */
interface MenuViewModelInterface extends AuthoredViewModelInterface {

    /**
     *
     * @return HierarchyAggregateInterface
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
     * @return HierarchyAggregateInterface
     */
    public function getMenuNode($nodeUid) ;

    /**
     *
     * @param string $parentUid
     * @return HierarchyAggregateInterface array af
     */
    public function getChildrenMenuNodes($parentUid);

    /**
     *
     * @param type $parentUid
     * @param type $maxDepth
     * @return ItemViewModelInterface array of
     */
    public function getChildrenItemModels($parentUid);

    /**
     *
     * @param type $rootUid
     * @param type $maxDepth
     * @return ItemViewModelInterface array af
     */
    public function getSubTreeItemModels($rootUid, $maxDepth=NULL);
}
