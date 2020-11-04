<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Entity\Block;
use Model\Entity\BlockInterface;
use Model\Dao\BlockDao;
use Model\Hydrator\BlockHydrator;

/**
 * Description of ComponentRepo
 *
 * @author pes2704
 */
class BlockRepo extends RepoAbstract implements BlockRepoInterface, RepoReadonlyInterface {

    public function __construct(BlockDao $componentDao, BlockHydrator $componentHydrator) {
        $this->dao = $componentDao;
        $this->registerHydrator($componentHydrator);
    }

    /**
     *
     * @param type $name
     * @return BlockInterface|null
     */
    public function get($name):?BlockInterface {
        $index = $name;
        if (!isset($this->collection[$index])) {
            $this->recreateEntity($index, $this->dao->get($name));
        }
        return $this->collection[$index] ?? null;
    }

    protected function createEntity() {
        return new Block();
    }

    public function add(EntityInterface $entity) {
        ;
    }

    public function remove(EntityInterface $entity) {
        ;
    }
}
