<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Repository;

use Model\Repository\RepoAbstract;
use Model\Hydrator\HydratorInterface;
use Model\Repository\RepoReadonlyInterface;

use Red\Model\Entity\MenuItem;
use Red\Model\Entity\MenuItemInterface;
use Red\Model\Entity\HierarchyAggregate;
use Red\Model\Entity\MenuItemAggregateHierarchyInterface;
use Red\Model\Dao\Hierarchy\HierarchyAggregateReadonlyDaoInterface;
use Red\Model\Dao\Hierarchy\HierarchyAggregateEditDaoInterface;

use Red\Model\Repository\MenuItemRepo;

/**
 * Description of HierarchyAggregateRepo
 *
 * @author pes2704
 */
class HierarchyRepoOLD extends RepoAbstract implements RepoReadonlyInterface {

    public function __construct(HierarchyAggregateReadonlyDaoInterface $readHirerarchy,
            HydratorInterface $menuNodeHydrator
            ) {
        $this->dataManager = $readHirerarchy;
        $this->registerHydrator($menuNodeHydrator);
    }

    protected function createEntity() {
        return new PaperAggregatePaperContent();
    }

    /**
     * Vrací entitu vyhledanou podle primárního (kompozitního) klíče: (langCode, uid) s podmínkou active a actual.
     * Podud je parametr active TRUE, vybírá jen publikované položky s vlastností active=TRUE, jinak vrací všechny - aktivní i neaktivní.
     * Pokud je parametr actual=TRUE, vybírá jen položky kde dnešní datum je mezi datumy show_time a hide_time včetně, jinak vrací všechny.
     *
     * @param string $langCode Identifikátor language
     * @param string $uid Identifikátor menu_nested_set
     * @return HierarchyAggregateInterface|null
     */
    /**
     *
     * @param type $id
     * @return PaperInterface|null
     */
    public function get($langCode, $uid) {
        return $this->getEntity($langCode, $uid);
    }

    /**
     * JEN POMOCNÁ METODA PRO LADĚNÍ
     * Vrací item podle hodnoty lang_code a title v tabulce menu_item.
     * Vybírá case insensitive. Pokud je více položek se stejným title, vrací jen první.
     *
     * @param string $langCode Identifikátor language
     * @param string $title
     * @return MenuItemAggregateHierarchyInterface
     */
    public function getNodeByTitle($langCode, $title) {
        $row = $this->dao->getByTitleHelper($langCode, $title);
        $index = $langCode.$row['uid_fk']; // index z parametru a row
        if (!isset($this->collection[$index])) {
            $this->recreateEntity($index, $row);
        }
        return $this->collection[$index] ?? null;

    }

    /**
     * Vrací pole položek "dětí", tj. přímých potomků vyhledanou podle rodiče - primárního (kompozitního) klíče: (langCode, uid) s podmínkou active a actual.
     * Podud je parametr active TRUE, vybírá jen položky s vlastností active=TRUE, jinak vrací aktivní i neaktivní.
     * Pokud je parametr actual=TRUE, vybírá jen položky kde dnešní datum je mezi show_time a hide_time včetně.
     *
     * @param string $langCode Identifikátor language
     * @param string $parentUid Identifikátor rodiče z menu_nested_set
     * @return MenuItemAggregateHierarchyInterface array of
     */
    public function findChildren($langCode, $parentUid) {
        $children = [];
        foreach($this->dao->getImmediateSubNodes($langCode, $parentUid) as $row) {
            $index = $this->indexFromRow($row);
            $this->recreateEntity($index, $row);
            $children[] = $this->collection[$index];
        }
        return $children;
    }

    /**
     *
     * @param string $langCode Identifikátor language
     * @return MenuItemAggregateHierarchyInterface array of
     */
    public function getFullTree($langCode) {
        $tree = [];
        foreach($this->dao->getFullTree($langCode) as $row) {
            $index = $this->indexFromRow($row);
            $this->recreateEntity($index, $row);
            $tree[] = $this->collection[$index];
        }
        return $tree;
    }

    /**
     *
     * @param string $langCode Identifikátor language
     * @param string $rootUid
     * @param int $maxDepth int or NULL
     * @return MenuItemAggregateHierarchyInterface array of
     */
    public function getSubTree($langCode, $rootUid, $maxDepth=NULL) {
        $subTree = [];
        foreach($this->dao->getSubTree($langCode, $rootUid, $maxDepth) as $row) {
            $index = $this->indexFromRow($row);
            $this->recreateEntity($index, $row);
            $subTree[] = $this->collection[$index];
        }
        return $subTree;
    }

    /**
     *
     * @param string $langCode Identifikátor language
     * @param string $parentUid
     * @param int $maxDepth int or NULL
     * @return MenuItemAggregateHierarchyInterface array of
     */
    public function getSubNodes($langCode, $parentUid, $maxDepth=NULL) {
        $subTree = [];
        foreach($this->dao->getSubTree($langCode, $parentUid, $maxDepth) as $row) {
            $index = $this->indexFromRow($row);
            $this->recreateEntity($index, $row);
            $subTree[] = $this->collection[$index];
        }
        return $subTree;
    }

    /**
     *
     * @param array $row
     * @return string index
     */
    private function recreateEntity($index, $row) {
        if ($row) {
            if (!isset($this->collection[$index])) {
                $menuItem = new MenuItem();
                $this->menuItemHydrator->hydrate($menuItem, $row);
                $menuItem->setPersisted();
                $this->menuItemRepo->add($menuItem);

                $menuNode = new HierarchyAggregate();
                $this->hierarchyNodeHydrator->hydrate($menuNode, $row);
                $menuNode->setMenuItem($menuItem);
                $menuNode->setPersisted();
                $this->collection[$index] = $menuNode;
            }
        }
    }

    protected function indexFromEntity(MenuItemAggregateHierarchyInterface $menuNode) {
        return $menuNode->getHierarchy()->getLangCodeFk().$menuNode->getUid();
    }

    protected function indexFromRow($row) {
        return $row['lang_code_fk'].$row['uid'];
    }

}
