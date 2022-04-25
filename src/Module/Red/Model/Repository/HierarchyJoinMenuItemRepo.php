<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Repository;

use Model\Repository\RepoAbstract;
use Model\Hydrator\HydratorInterface;

use Red\Model\Repository\MenuItemRepo;

use Red\Model\Entity\MenuItemInterface;
use Red\Model\Entity\HierarchyAggregate;
use Red\Model\Entity\HierarchyAggregateInterface;
use Red\Model\Dao\Hierarchy\HierarchyAggregateReadonlyDaoInterface;
use Red\Model\Hydrator\HierarchyHydrator;
use Red\Model\Hydrator\HierarchyChildHydrator;

/**
 * Description of HierarchyJoinMenuItemRepo
 *
 * Používá Dao, které pracuje nas hierarchy (nested set) JOIN menuItem
 *
 * @author pes2704
 */
class HierarchyJoinMenuItemRepo extends RepoAbstract {  // HierarchyAggregateMenuItemRepo nemá skutečné rodičovské repo

    public function __construct(HierarchyAggregateReadonlyDaoInterface $editHirerarchy, HierarchyHydrator $hierarchyNodeHydrator,
            MenuItemRepo $menuItemRepo, HierarchyChildHydrator $hierarchyChildHydrator
            ) {


        $this->dataManager = $editHirerarchy;
        $this->registerOneToOneAssociation(MenuItemInterface::class, ['uid_fk', 'lang_code_fk'], $menuItemRepo);
        $this->registerHydrator($hierarchyNodeHydrator);
        $this->registerHydrator($hierarchyChildHydrator);
    }

    protected function createEntity() {
        return new HierarchyAggregate();
    }

    /**
     *
     * @param type $langCode
     * @param type $uid
     * @return HierarchyAggregateInterface|null
     */
    public function get($langCodeFk, $uidFk): ?HierarchyAggregateInterface {
        $key = $this->getPrimaryKeyTouples(['lang_code_fk'=>$langCodeFk, 'uid_fk'=>$uidFk]);
        return $this->getEntity($key);
    }

    /**
     * JEN POMOCNÁ METODA PRO LADĚNÍ
     * Vrací item podle hodnoty lang_code a title v tabulce menu_item.
     * Vybírá case insensitive. Pokud je více položek se stejným title, vrací jen první.
     *
     * @param string $langCode Identifikátor language
     * @param string $title
     * @return HierarchyAggregateInterface|null
     */
    public function getNodeByTitle($key): ?HierarchyAggregateInterface {
        $rowData = $this->dataManager->getByTitleHelper($key);
//        $index = $langCode.$row['uid_fk']; // index z parametru a row
//        if (!isset($this->collection[$index])) {
//            $this->recreateEntity($index, $row);
//        }
//        return $this->collection[$index] ?? null;
        return $this->addEntityByRowData($rowData);
    }

    /**
     * Vrací pole položek "dětí", tj. přímých potomků vyhledanou podle rodiče - primárního (kompozitního) klíče: (langCode, uid) s podmínkou active a actual.
     * Podud je parametr active TRUE, vybírá jen položky s vlastností active=TRUE, jinak vrací aktivní i neaktivní.
     * Pokud je parametr actual=TRUE, vybírá jen položky kde dnešní datum je mezi show_time a hide_time včetně.
     *
     * @param string $langCode Identifikátor language
     * @param string $parentUid Identifikátor rodiče z menu_nested_set
     * @return HierarchyAggregateInterface array of
     */
    public function findChildren($langCode, $parentUid) {
//        $children = [];
//        foreach($this->dataManager->getImmediateSubNodes($langCode, $parentUid) as $row) {
//            $index = $this->indexFromRow($row);
//            $this->recreateEntity($index, $row);
//            $children[] = $this->collection[$index];
//        }
//        return $children;
        return $this->addEntitiesByRowDataArray($this->dataManager->getImmediateSubNodes($langCode, $parentUid));
    }

    /**
     *
     * @param string $langCode Identifikátor language
     * @return HierarchyAggregateInterface array of
     */
    public function getFullTree($langCode) {
//        $tree = [];
//        foreach($this->dataManager->getFullTree($langCode) as $row) {
//            $index = $this->indexFromRow($row);
//            $this->recreateEntity($index, $row);
//            $tree[] = $this->collection[$index];
//        }
//        return $tree;
        return $this->addEntitiesByRowDataArray($this->dataManager->getFullTree($langCode));
    }

    /**
     *
     * @param string $langCode Identifikátor language
     * @param string $rootUid
     * @param int $maxDepth int or NULL
     * @return HierarchyAggregateInterface array of
     */
    public function getSubTree($langCode, $rootUid, $maxDepth=NULL) {
//        $subTree = [];
//        foreach($this->dataManager->getSubTree($langCode, $rootUid, $maxDepth) as $row) {
//            $index = $this->indexFromRow($row);
//            $this->recreateEntity($index, $row);
//            $subTree[] = $this->collection[$index];
//        }
//        return $subTree;
        return $this->addEntitiesByRowDataArray($this->dataManager->getSubTree($langCode, $rootUid, $maxDepth));
    }

    /**
     *
     * @param string $langCode Identifikátor language
     * @param string $parentUid
     * @param int $maxDepth int or NULL
     * @return HierarchyAggregateInterface array of
     */
    public function getSubNodes($langCode, $parentUid, $maxDepth=NULL) {
//        $subTree = [];
//        foreach($this->dataManager->getSubNodes($langCode, $parentUid, $maxDepth) as $row) {
//            $index = $this->indexFromRow($row);
//            $this->recreateEntity($index, $row);
//            $subTree[] = $this->collection[$index];
//        }
//        return $subTree;
        return $this->addEntitiesByRowDataArray($this->dataManager->getSubNodes($langCode, $parentUid, $maxDepth));
    }

    /**
     *
     * @param array $row
     * @return string index
     */
//    private function recreateEntity($index, $row) {
//        if ($row) {
//            if (!isset($this->collection[$index])) {
//                $menuItem = new MenuItem();
//                $this->menuItemHydrator->hydrate($menuItem, $row);
//                $menuItem->setPersisted();
//                $this->menuItemRepo->add($menuItem);
//
//                $menuNode = new HierarchyAggregate();
//                $this->hierarchyNodeHydrator->hydrate($menuNode, $row);
//                $menuNode->setMenuItem($menuItem);
//                $menuNode->setPersisted();
//                $this->collection[$index] = $menuNode;
//            }
//        }
//    }

    protected function indexFromKeyParams($id) {
        return $id['lang_code_fk'].$id['uid_fk'];
    }

    protected function indexFromEntity(HierarchyAggregateInterface $menuNode) {
        return $menuNode->getMenuItem()->getLangCodeFk().$menuNode->getUid();
    }

    protected function indexFromRow($row) {
        return $row['lang_code_fk'].$row['uid_fk'];
    }
}
