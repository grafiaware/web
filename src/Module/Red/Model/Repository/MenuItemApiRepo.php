<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Repository;

use Model\Repository\RepoAbstract;

use Red\Model\Dao\MenuItemApiDao;
use Model\Hydrator\HydratorInterface;
use Red\Model\Entity\MenuItemApiInterface;
use Red\Model\Entity\MenuItemApi;

use Model\Repository\Exception\UnableRecreateEntityException;

/**
 * Description of MenuItemApiRepo
 *
 * @author pes2704
 */
class MenuItemApiRepo extends RepoAbstract implements MenuItemApiRepoInterface { 

    public function __construct(MenuItemApiDao $menuItemApiDao, HydratorInterface $menuItemApiHydrator) {
        $this->dataManager = $menuItemApiDao;
        $this->registerHydrator($menuItemApiHydrator);
    }

    /**
     * Vrací MenuItemApiInterface nebo null.
     *
     * @param type $module
     * @param type $generator
     * @return MenuItemApiInterface|null
     */
    public function get($module, $generator): ?MenuItemApiInterface {
        return $this->getEntity($module, $generator);
    }

    /**
     * Vrací pole entit MenuItemApiInterface.
     * @return MenuItemApiInterface array of
     */
    public function findAll() {
        return $this->findEntities();
    }

    public function add(MenuItemApiInterface $menuItemType) {
        $this->addEntity($menuItemType);
    }

    public function remove(MenuItemApiInterface $menuItemType) {
        $this->removeEntity($menuItemType);
    }

    protected function indexFromEntity(MenuItemApiInterface $menuItemApi) {
        return $menuItemApi->getModule().$menuItemApi->getGenerator();
    }

    protected function indexFromRow($row) {
        return $row['module'].$row['generator'];
    }

    protected function createEntity() {
        return new MenuItemApi();
    }
}
