<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Repository;

use Model\Repository\RepoAbstract;

use Red\Model\Entity\MenuItem;
use Red\Model\Entity\MenuItemInterface;

use Red\Model\Dao\MenuItemDao;

use Model\Hydrator\HydratorInterface;
use Model\Entity\EntityInterface;
use Model\Repository\Exception\UnableRecreateEntityException;

/**
 * Description of MenuItemRepo
 *
 * @author pes2704
 */
class MenuItemRepo extends RepoAbstract implements MenuItemRepoInterface {

    protected $dao;

    public function __construct(MenuItemDao $menuItemDao, HydratorInterface $menuItemHydrator) {
        $this->dataManager = $menuItemDao;
        $this->registerHydrator($menuItemHydrator);
    }

    /**
     * Vrací MenuItem podle hodnoty primárního klíče - kompozit z langCode a uid.
     * Vrací jen položky, které jsou aktivní a aktuální.
     *
     * @param string $langCodeFk
     * @param string $uidFk
     * @return MenuItemInterface|null
     */
    public function get($langCodeFk, $uidFk): ?MenuItemInterface {
        $key = $this->getPrimaryKeyTouples(['lang_code_fk'=>$langCodeFk, 'uid_fk'=>$uidFk]);
        return $this->getEntity($key);
    }

    /**
     * Vrací MenuItem podle hodnoty primárního klíče - kompozit z langCode a uid.
     * Vrací i neaktivní a nektuální položky.
     *
     * @param type $langCodeFk
     * @param type $uidFk
     * @return MenuItemInterface|null
     */
    public function getOutOfContext($langCodeFk, $uidFk): ?MenuItemInterface {
        $index = $this->indexFromKeyParams($langCodeFk, $uidFk);
        if (!isset($this->collection[$index])) {   // collection je private
            $key = $this->getPrimaryKeyTouples(['lang_code_fk'=>$langCodeFk, 'uid_fk'=>$uidFk]);
            $rowData = $this->dataManager->getOutOfContext($key);
            $this->addData($index, $rowData);  // natvrdo dá rowData do $this->data // private
            $this->recreateEntity($index, $rowData);  // private
        }
        return $this->collection[$index] ?? null;
    }

    /**
     * Vrací MenuItem podle id, id je cizí klíč v entitách Paper, Article, Multipage.
     * Vrací jen položky, které jsou aktivní a aktuální.
     *
     * @param type $id
     * @return MenuItemInterface|null
     */
    public function getById($id): ?MenuItemInterface {
        $rowData = $this->dataManager->get($id);  // zatím je tu MenuItemDao!
        return $this->addEntityByRowData($rowData);
    }

    /**
     * Vrací MenuItem podle hodnoty prettyUri.
     * Vrací jen položky, které jsou aktivní a aktuální.
     *
     * @param type $prettyUri
     * @return MenuItemInterface|null
     */
    public function getByPrettyUri($prettyUri): ?MenuItemInterface {
        $rowData = $this->dataManager->getByPrettyUri(['prettyuri'=>$prettyUri]);
        return $this->addEntityByRowData($rowData);
    }

    /**
     *
     * @param array $id Asociativní pole atributů klíče
     * @return EntityInterface|null
     */
    public function getByReference($id): ?EntityInterface {
        $rowData = $this->dataManager->get($id);  // zatím je tu MenuItemDao!
        return $this->addEntityByRowData($rowData);
    }

    /**
     *
     * @param type $uidFk
     * @return iterable
     */
    public function findAllLanguageVersions($uidFk): iterable {
        $rowDataArray = $this->dataManager->findAllLanguageVersions($uidFk);
        return $this->addEntitiesByRowDataArray($rowDataArray);
    }

    /**
     * NEFUNKČNÍ! metoda dao vrací data z menu_item JOIN paper - v tomto smyslu je to opravdu PaperFulltextSearch, jen asi nevzniká správně menuItem
     *
     * @param string $langCodeFk
     * @param string $text
     * @return MenuItemInterface array of
     */
    public function findByPaperFulltextSearch($langCodeFk, $text) {
        $rowDataArray = $this->dataManager->findByContentFulltextSearch($langCodeFk, $text);
        return $this->addEntitiesByRowDataArray($rowDataArray);
    }

    public function add(MenuItemInterface $menuItem) {
        $index = $this->indexFromEntity($menuItem);
        $this->collection[$index] = $menuItem;
    }

    public function remove(MenuItemInterface $menuItem) {
        $this->removed[] = $menuItem;
        unset($this->collection[$this->indexFromEntity($menuItem)]);
    }

    protected function createEntity() {
        return new MenuItem();
    }

    protected function indexFromKeyParams($key) {
        return $key['lang_code_fk'].$key['uid_fk'];
    }

    protected function indexFromEntity(MenuItemInterface $menuItem) {
        return $menuItem->getLangCodeFk().$menuItem->getUidFk();
    }

    protected function indexFromRow($row) {
        return $row['lang_code_fk'].$row['uid_fk'];
    }
}
