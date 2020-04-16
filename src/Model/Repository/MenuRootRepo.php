<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Entity\MenuRoot;
use Model\Entity\MenuRootInterface;
use Model\Dao\MenuRootDao;

/**
 * Description of MenuRootRepo
 *
 * @author pes2704
 */
class MenuRootRepo {

    private $menuRootDao;

    public function __construct(MenuRootDao $menuRootDao) {
        $this->menuRootDao = $menuRootDao;
    }

    /**
     *
     * @param string $name
     * @return MenuRootInterface|null
     */
    public function get($name): ?MenuRootInterface {
        $row = $this->menuRootDao->get($name);
        return $row ? $this->createItem($row) : NULL;
    }

    /**
     *
     * @return MenuRootInterface array of
     */
    public function find() {
        $entities = [];
        foreach ($this->menuRootDao->find() as $row) {
            $entities[] = $this->createItem($row);
        }
        return $entities;
    }

    /**
     *
     * @param type $lang
     * @param type $row
     * @return MenuRootInterface
     */
    private function createItem($row) {
        return (new MenuRoot())
                ->setName($row['name'])
                ->setUidFk($row['uid_fk']);
    }

    public function add(EntityInterface $entity) {
        ;
    }

    public function remove(EntityInterface $entity) {
        ;
    }
}
