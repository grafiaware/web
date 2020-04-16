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

/**
 * Description of ComponentRepo
 *
 * @author pes2704
 */
class ComponentRepo {

    private $componentDao;

    public function __construct(ComponentDao $componentDao) {
        $this->componentDao = $componentDao;
    }

    /**
     *
     * @param type $name
     * @return ComponentInterface|null
     */
    public function get($name):?ComponentInterface {
        $row = $this->componentDao->get($name);
        return $row ? $this->createItem($row) : NULL;
    }

    /**
     *
     * @return ComponentInterface array of
     */
    public function find() {
        $entities = [];
        foreach ($this->componentDao->findAll() as $row) {
            $entities[] = $this->createItem($row);
        }
        return $entities;
    }

    /**
     *
     * @param type $lang
     * @param type $row
     * @return PaperInterface
     */
    private function createItem($row) {
        return (new Component())
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
