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
use Model\Entity\PersistableEntityInterface;
use Model\Repository\Exception\UnableRecreateEntityException;

/**
 * Description of MenuItemRepo
 *
 * @author pes2704
 */
class MenuItemRepo extends RepoAbstract implements MenuItemRepoInterface {

    /**
     *
     * @var MenuItemDao
     */
    protected $dataManager;

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
        $key = $this->dataManager->getPrimaryKeyTouples(['lang_code_fk'=>$langCodeFk, 'uid_fk'=>$uidFk]);
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
            $key = $this->dataManager->getPrimaryKeyTouples(['lang_code_fk'=>$langCodeFk, 'uid_fk'=>$uidFk]);
            $rowData = $this->dataManager->getOutOfContext($key);
            $entity = $this->addEntityByRowData($rowData);
        }
        return $entity ?? null;
    }

    /**
     * Vrací MenuItem podle id, id je cizí klíč v entitách Paper, Article, Multipage.
     * Vrací jen položky, které jsou aktivní a aktuální.
     *
     * @param type $id
     * @return MenuItemInterface|null
     */
    public function getById($id): ?MenuItemInterface {
        $rowData = $this->dataManager->getById(['id'=>$id]);  // zatím je tu MenuItemDao!
        return $this->addEntityByRowData($rowData);    }

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
     * @return PersistableEntityInterface|null
     */
    public function getByReference($id): ?PersistableEntityInterface {
        // asociativní pole atributů primárního klíče - je to definováno metodou registerOneToOneAssociation() v rodičovském repo - t.j. HierarchyJoinMenuItemRepo
        $rowData = $this->dataManager->get($id);
        return $this->addEntityByRowData($rowData);
    }

    /**
     *
     * @param type $uidFk
     * @return iterable
     */
    public function findAllLanguageVersions($uidFk): iterable {
        $rowDataArray = $this->dataManager->findAllLanguageVersions(['uid_fk'=>$uidFk]);
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

    protected function indexFromEntity(MenuItemInterface $menuItem) {
        return $menuItem->getLangCodeFk().$menuItem->getUidFk();
    }

    protected function indexFromRow($row) {
        return $row['lang_code_fk'].$row['uid_fk'];
    }
}
