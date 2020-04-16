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
class MenuItemTypeRepo extends RepoAbstract { // implements Chybí interface pro repa {

    public function __construct(MenuItemTypeDao $menuItemTypeDao, HydratorInterface $menuItemTypeHydrator) {
        $this->dao = $menuItemTypeDao;
        $this->hydrator = $menuItemTypeHydrator;
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
        return $this->collection[$index];
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

    /**
     *
     * @param array $row
     */
    protected function recreateEntity($index, $row) {
        if ($row) {
            $menuItemType = new MenuItemType();
            $this->hydrator->hydrate($menuItemType, $row);
            $menuItemType->setPersisted();
            $this->collection[$index] = $menuItemType;
        } else {
            throw new UnableRecreateEntityException("Nelze obnovit entitu, hledaná položka v databázi neexistuje.");
        }
    }

    protected function indexFromEntity(MenuItemTypeInterface $menuItemType) {
        return $menuItemType->getType();
    }

    protected function indexFromRow($row) {
        return $row['type'];
    }
}
