<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Repository;

use Model\Repository\RepoAbstract;
use Model\Repository\RepoReadonlyInterface;

use Red\Model\Entity\Block;
use Red\Model\Entity\BlockInterface;
use Red\Model\Dao\BlockDao;
use Red\Model\Hydrator\BlockHydrator;

/**
 * Description of ComponentRepo
 *
 * @author pes2704
 */
class BlockRepo extends RepoAbstract implements BlockRepoInterface, RepoReadonlyInterface {

    public function __construct(BlockDao $componentDao, BlockHydrator $componentHydrator) {
        $this->dataManager = $componentDao;
        $this->registerHydrator($componentHydrator);
    }

    /**
     *
     * @param type $name
     * @return BlockInterface|null
     */
    public function get($name):?BlockInterface {
        return $this->getEntity($name);
    }

    protected function createEntity() {
        return new Block();
    }

    public function add(BlockInterface $block) {
        $this->addEntity($block);
    }

    public function remove(BlockInterface $block) {
        $this->removeEntity($block);
    }

    protected function indexFromEntity(BlockInterface $block) {
        return $block->getName();
    }

    protected function indexFromRow($row) {
        return $row['name'];
    }
}
