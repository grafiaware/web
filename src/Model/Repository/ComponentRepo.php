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

    private $componentDao;

    public function __construct(ComponentDao $componentDao, ComponentHydrator $componentHydrator) {
        $this->componentDao = $componentDao;
        $this->registerHydrator($componentHydrator);
    }

    /**
     *
     * @param type $name
     * @return ComponentInterface|null
     */
    public function get($name):?ComponentInterface {
        $row = $this->componentDao->get($name);
        return $row ? $this->recreateEntity($name, $row) : NULL;
    }

    /**
     *
     * @return ComponentInterface array of
     */
    public function find(): iterable {
        $entities = [];
        foreach ($this->componentDao->findAll() as $row) {
            $entities[] = $this->recreateEntity($name, $row);
        }
        return $entities;
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
