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
use Model\Hydrator\HydratorInterface;

use Model\Repository\Exception\UnableRecreateEntityException;

/**
 * Description of MenuItemRepo
 *
 * @author pes2704
 */
class MenuItemRepo extends RepoAbstract implements MenuItemRepoInterface {

    private $onlyPublished;


    public function __construct(MenuItemDao $menuItemDao, HydratorInterface $menuItemHydrator) {
        $this->dao = $menuItemDao;
        $this->hydrator = $menuItemHydrator;
    }

    public function setOnlyPublishedMode($onlyPublished = true): void {
        $this->onlyPublished = $onlyPublished;
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
            $this->recreateEntity($index, $this->dao->get($langCodeFk, $uidFk, $this->onlyPublished, $this->onlyPublished));
        }
        return $this->collection[$index] ?? null;
    }

    /**
     *
     * @param string $langCodeFk
     * @param string $text
     * @param bool $active Nepovinný parametr, default=TRUE. Defaultně metoda hledá jen aktivní (zveřejněné) položky.
     * @param bool $actual Nepovinný parametr, default=TRUE. Defaultně metoda hledá jen aktuální položky.
     * @return MenuItemInterface array of
     */
    public function findByPaperFulltextSearch($langCodeFk, $text, $active=\TRUE, $actual=\TRUE) {
        $rows = $this->dao->findByPaperFulltextSearch($langCodeFk, $text, $active, $actual);
        $collection = [];
        foreach ($rows as $row) {
            $index = $row['lang_code_fk'].$row['uid_fk'];
            if (!isset($this->collection[$index])) {
                $this->recreateEntity($index, $row);
            }
            $collection[] = $this->collection[$index];
        }
        return $collection;
    }

    public function add(MenuItemInterface $menuItem) {
        $index = $menuItem->getLangCodeFk().$menuItem->getUidFk();
        $this->collection[$index] = $menuItem;
    }

    /**
     *
     * @param array $row
     * @return MenuItem
     */
    private function recreateEntity($index, $row) {
        if ($row) {
            $menuItem = new MenuItem();
            $this->hydrator->hydrate($menuItem, $row);
            $menuItem->setPersisted();
            $this->collection[$index] = $menuItem;
        }
    }

    public function flush() {
        foreach ($this->collection as $index => $menuItem) {
            $this->hydrator->extract($menuItem, $row);
            if ($menuItem->isPersisted()) {
                $this->dao->update($row);
            } else {
                $this->dao->insert($row);
            }
        }
    }
}
