<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Dao\MenuItemTypeDao;
use Model\Hydrator\HydratorInterface;
use Model\Entity\MenuItemTypeInterface;
use Model\Entity\MenuItemType;

use Model\Repository\Exception\UnableRecreateEntityException;

/**
 * Description of MenuItemTypeRepo
 *
 * @author pes2704
 */
class MenuItemTypeRepo extends RepoAbstract implements MenuItemTypeRepoInterface { // implements Chybí interface pro repa {

    public function __construct(MenuItemTypeDao $menuItemTypeDao, HydratorInterface $menuItemTypeHydrator) {
        $this->dao = $menuItemTypeDao;
        $this->registerHydrator($menuItemTypeHydrator);
    }

    /**
     * Vrací MenuItemTypeInterface nebo null.
     *
     * @param string $type Hodnota identifikátoru type.
     * @return MenuItemTypeInterface|null
     */
    public function get($type): ?MenuItemTypeInterface {
        $index = $type;
        if (!isset($this->collection[$index])) {
            $this->recreateEntity($index, $this->dao->get($type));
        }
        return $this->collection[$index] ?? null;
    }

    /**
     * Vrací pole entit MenuItemTypeInterface.
     * @return MenuItemTypeInterface array of
     */
    public function findAll() {
        $rows = $this->dao->findAll();
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

    public function add(MenuItemTypeInterface $menuItemType) {
        $index = $this->indexFromEntity($menuItemType);
        $this->collection[$index] = $menuItemType;
    }

    public function remove(MenuItemTypeInterface $menuItemType) {
        $this->removed[] = $menuItemType;
        unset($this->collection[$this->indexFromEntity($menuItemType)]);
    }

    protected function indexFromEntity(MenuItemTypeInterface $menuItemType) {
        return $menuItemType->getType();
    }

    protected function indexFromRow($row) {
        return $row['type'];
    }

    protected function createEntity() {
        return new MenuItemType();
    }
}
