<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Repository;

use Model\Repository\RepoAbstract;

use Red\Model\Entity\MenuRoot;
use Red\Model\Entity\MenuRootInterface;
use Red\Model\Dao\MenuRootDao;
use Red\Model\Hydrator\MenuRootHydrator;

/**
 * Description of MenuRootRepo
 *
 * @author pes2704
 */
class MenuRootRepo extends RepoAbstract {

    /**
     *
     * @param MenuRootDao $menuRootDao
     * @param MenuRootHydrator $menuRootHydrator
     */
    public function __construct(MenuRootDao $menuRootDao, MenuRootHydrator $menuRootHydrator) {
        $this->dataManager = $menuRootDao;
        $this->registerHydrator($menuRootHydrator);

    }

    /**
     *
     * @param string $name
     * @return MenuRootInterface|null
     */
    public function get($name): ?MenuRootInterface {
        $key = $this->getPrimaryKeyTouples(['name'=>$name]);
        return $this->getEntity($key);
    }

    /**
     *
     * @return MenuRootInterface array of
     */
    public function findAll() {
        return $this->findEntities();
    }

    public function add(MenuRootInterface $entity) {
        $this->addEntity($entity);
    }

    public function remove(MenuRootInterface $entity) {
        $this->removeEntity($entity);
    }

    protected function createEntity() {
        return new MenuRoot();
    }

    protected function indexFromKeyParams($name) {
        return $name;
    }

    protected function indexFromEntity(MenuRootInterface $menuRoot) {
        return $menuRoot->getName();
    }

    protected function indexFromRow($row) {
        return $row['name'];
    }
}
