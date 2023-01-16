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

use Model\Repository\RepoAssociatedWithJoinOneTrait;

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

    use RepoAssociatedWithJoinOneTrait;

    /**
     * Vrací MenuItem podle hodnoty primárního klíče - kompozit z langCode a uid.
     * Vrací jen položky, které jsou aktivní a aktuální.
     *
     * @param string $langCodeFk
     * @param string $uidFk
     * @return MenuItemInterface|null
     */
    public function get($langCodeFk, $uidFk): ?MenuItemInterface {
        return $this->getEntity($langCodeFk, $uidFk);
    }

    /**
     * Vrací MenuItem podle id, id je cizí klíč v entitách Paper, Article, Multipage.
     * Vrací jen položky, které jsou aktivní a aktuální.
     *
     * @param type $id
     * @return MenuItemInterface|null
     */
    public function getById($id): ?MenuItemInterface {
        $rowData = $this->dataManager->getUnique(['id'=>$id]);
        return $this->recreateEntityByRowData($rowData);    }

    /**
     * Vrací MenuItem podle hodnoty prettyUri.
     * Vrací jen položky, které jsou aktivní a aktuální.
     *
     * @param type $prettyUri
     * @return MenuItemInterface|null
     */
    public function getByPrettyUri($prettyUri): ?MenuItemInterface {
        $rowData = $this->dataManager->getUnique(['prettyuri'=>$prettyUri]);
        return $this->recreateEntityByRowData($rowData);
    }

    /**
     *
     * @param type $uidFk
     * @return iterable
     */
    public function findAllLanguageVersions($uidFk): iterable {
        $rowDataArray = $this->dataManager->findAllLanguageVersions(['uid_fk'=>$uidFk]);
        return $this->recreateEntitiesByRowDataArray($rowDataArray);
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
        return $this->recreateEntitiesByRowDataArray($rowDataArray);
    }

    public function add(MenuItemInterface $menuItem) {
        $this->addEntity($menuItem);
    }

    public function remove(MenuItemInterface $menuItem) {
        $this->removeEntity($menuItem);
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
