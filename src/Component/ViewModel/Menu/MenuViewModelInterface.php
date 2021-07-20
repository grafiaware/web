<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Component\ViewModel\Menu;

use Component\ViewModel\StatusViewModelInterface;

use Red\Model\Entity\HierarchyAggregateInterface;
use Red\Model\Entity\MenuRootInterface;

/**
 *
 * @author pes2704
 */
interface MenuViewModelInterface {

    /**
     * Prezentuj pouze publikované položky
     * @return bool
     */
    public function presentOnlyPublished();

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

    public function isEditableItem(): bool;

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
