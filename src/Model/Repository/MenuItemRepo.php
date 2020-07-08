<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Entity\MenuItem;
use Model\Entity\MenuItemInterface;

use Model\Dao\MenuItemDao;
use Model\ContextPublishedInterface;

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
        $this->dao = $menuItemDao;
        $this->registerHydrator($menuItemHydrator);
    }

    /**
     *
     * @param string $langCodeFk
     * @param string $uidFk
     * @return MenuItemInterface|null
     */
    public function get($langCodeFk, $uidFk): ?MenuItemInterface {
        $index = $langCodeFk.$uidFk;
        if (!isset($this->collection[$index])) {
            $this->recreateEntity($index, $this->dao->get($langCodeFk, $uidFk));
        }
        return $this->collection[$index] ?? null;
    }

    public function getByReference($id): ?EntityInterface {
        return $this->get($id['lang_code_fk'], $id['uid_fk']);
    }

    /**
     *
     * @param string $langCodeFk
     * @param string $text
     * @param bool $active Nepovinný parametr, default=TRUE. Defaultně metoda hledá jen aktivní (zveřejněné) položky.
     * @param bool $actual Nepovinný parametr, default=TRUE. Defaultně metoda hledá jen aktuální položky.
     * @return MenuItemInterface array of
     */
    public function findByPaperFulltextSearch($langCodeFk, $text) {
        $rows = $this->dao->findByContentFulltextSearch($langCodeFk, $text);
        $collection = [];
        foreach ($rows as $row) {
            $index = $this->indexFromRow($row);
            if (!isset($this->collection[$index])) {
                $this->recreateEntity($index, $row);
            }
            $collection[] = $this->collection[$index];
        }
        return $collection;
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
