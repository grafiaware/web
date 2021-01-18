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
     * @param string $componentName
     * @return void
     */
    public function setMenuRootName($componentName): void;

    /**
     *
     * @param bool $withTitle
     * @return void
     */
    public function withTitleItem($withTitle=false): void;

    /**
     *
     * @param int $maxDepth
     * @return void
     */
    public function setMaxDepth($maxDepth): void;
    /**
     * Vrací prezentovanou položku hierarchie, pokud je položkou tohoto modelu menu. Řídí se hodnotami status presentation.
     *
     * @return HierarchyAggregateInterface|null
     */
    public function getPresentedMenuNode(HierarchyAggregateInterface $rootNode): ?HierarchyAggregateInterface;

    /**
     *
     * @param string $menuRootName
     * @return MenuRootInterface
     */
    public function getMenuRoot($menuRootName);

    /**
     *
     * @param type $nodeUid
     * @return HierarchyAggregateInterface|null
     */
    public function getMenuNode($nodeUid): ?HierarchyAggregateInterface ;

    /**
     *
     * @param string $parentUid
     * @return HierarchyAggregateInterface array af
     */
//    public function getChildrenMenuNodes($parentUid);

    /**
     *
     * @param type $parentUid
     * @param type $maxDepth
     * @return ItemViewModelInterface array of
     */
//    public function getChildrenItemModels($parentUid);

    /**
     *
     * @return ItemViewModelInterface array af
     */
    public function getSubTreeItemModels();
}
