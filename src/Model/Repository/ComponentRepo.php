<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Entity\Component;
use Model\Entity\ComponentInterface;
use Model\Dao\ComponentDao;
use Model\Hydrator\ComponentHydrator;

/**
 * Description of ComponentRepo
 *
 * @author pes2704
 */
class ComponentRepo extends RepoAbstract implements ComponentRepoInterface, RepoReadonlyInterface {

    public function __construct(ComponentDao $componentDao, ComponentHydrator $componentHydrator) {
        $this->dao = $componentDao;
        $this->registerHydrator($componentHydrator);
    }

    /**
     *
     * @param type $name
     * @return ComponentInterface|null
     */
    public function get($name):?ComponentInterface {
        $index = $name;
        if (!isset($this->collection[$index])) {
            $this->recreateEntity($index, $this->dao->get($name));
        }
        return $this->collection[$index] ?? null;
    }

    protected function createEntity() {
        return new Component();
    }

    public function add(EntityInterface $entity) {
        ;
    }

    public function remove(EntityInterface $entity) {
        ;
    }
}
