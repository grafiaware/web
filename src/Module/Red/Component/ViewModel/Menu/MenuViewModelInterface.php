<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Component\ViewModel\Menu;

use Component\ViewModel\ViewModelInterface;

use Red\Model\Entity\HierarchyAggregateInterface;
use Red\Model\Entity\MenuRootInterface;

/**
 *
 * @author pes2704
 */
interface MenuViewModelInterface extends ViewModelInterface {

    public function presentEditableMenu(): bool;

    /**
     * Prezentuj pouze publikované položky
     * @return bool
     */
    public function presentOnlyPublished(): bool;

    /**
     *
     * @param string $componentName
     * @return void
     */
    public function setMenuRootName($componentName): void;

    /**
     *
     * @param int $maxDepth
     * @return void
     */
    public function setMaxDepth($maxDepth): void;

    /**
     * Položka menu (menu subtree nodes a views) bodou henerovány včetně kořenového prvku menu. První úrovwň menu pak obsahuje právě tuto jednu kořenovou položku.
     *
     * @param type $withRootItem
     * @return void
     */
    public function withRootItem($withRootItem=false): void ;

    public function setItemType($itemType);

    public function getItemType();
    
    public function getPostCommand($key);

    /**
     * Vrací prezentovanou položku hierarchie, pokud je položkou tohoto modelu menu. Řídí se hodnotami status presentation.
     *
     * @return HierarchyAggregateInterface|null
     */
    public function getPresentedMenuNode(HierarchyAggregateInterface $rootNode): ?HierarchyAggregateInterface;

    public function getNodeModels(): array;

    /**
     *
     * @param type $nodeUid
     * @return HierarchyAggregateInterface|null
     */
//    public function getMenuNode($nodeUid): ?HierarchyAggregateInterface ;

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
     * Původní metoda
     *
     * @return NodeViewModelInterface array af
     */
    public function getSubTreeNodes();

//    public function setSubTreeItemViews($itemViews);
//
//    public function getSubTreeItemViews();
}
