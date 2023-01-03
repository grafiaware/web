<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Repository;

use Model\Repository\RepoAbstract;
use Model\Hydrator\HydratorInterface;
use Model\Repository\RepoAssotiatingOneTrait;

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
class HierarchyJoinMenuItemRepo extends RepoAbstract implements HierarchyJoinMenuItemRepoInterface {  // HierarchyAggregateMenuItemRepo nemá skutečné rodičovské repo

    public function __construct(HierarchyAggregateReadonlyDaoInterface $editHirerarchy, HierarchyHydrator $hierarchyNodeHydrator
//            ,
//            MenuItemRepo $menuItemRepo, HierarchyChildHydrator $hierarchyChildHydrator
            ) {


        $this->dataManager = $editHirerarchy;
//        $this->registerOneToOneAssociation(MenuItemInterface::class, ['uid_fk', 'lang_code_fk'], $menuItemRepo);
        $this->registerHydrator($hierarchyNodeHydrator);
//        $this->registerHydrator($hierarchyChildHydrator);
    }

    use RepoAssotiatingOneTrait;

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
        return $this->getEntity($langCodeFk, $uidFk);
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
        return $this->recreateEntityByRowData($rowData);
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
        return $this->recreateEntitiesByRowDataArray($this->dataManager->getImmediateSubNodes($langCode, $parentUid));
    }

    /**
     *
     * @param string $langCode Identifikátor language
     * @return HierarchyAggregateInterface array of
     */
    public function getFullTree($langCode) {
        return $this->recreateEntitiesByRowDataArray($this->dataManager->getFullTree($langCode));
    }

    /**
     *
     * @param string $langCode Identifikátor language
     * @param string $rootUid
     * @param int $maxDepth int or NULL
     * @return HierarchyAggregateInterface array of
     */
    public function getSubTree($langCode, $rootUid, $maxDepth=NULL) {
        return $this->recreateEntitiesByRowDataArray($this->dataManager->getSubTree($langCode, $rootUid, $maxDepth));
    }

    protected function indexFromEntity(HierarchyAggregateInterface $menuNode) {
        return $menuNode->getMenuItem()->getLangCodeFk().$menuNode->getUid();
    }

    protected function indexFromRow($row) {
        return $row['lang_code_fk'].$row['uid_fk'];
    }
}
