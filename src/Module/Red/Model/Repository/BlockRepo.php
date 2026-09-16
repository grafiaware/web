<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Repository;

use Pes\Model\Repository\RepoAbstract;
use Pes\Model\Repository\RepoReadonlyInterface;

use Red\Model\Entity\Block;
use Red\Model\Entity\BlockInterface;
use Red\Model\Dao\BlockDao;
use Red\Model\Hydrator\BlockHydrator;

/**
 * Description of ComponentRepo
 *
 * @author pes2704
 */
class BlockRepo extends RepoAbstract implements BlockRepoInterface {

    public function __construct(BlockDao $componentDao, BlockHydrator $componentHydrator) {
        $this->dataManager = $componentDao;
        $this->registerHydrator($componentHydrator);
    }

    /**
     *
     * @param string $name
     * @return BlockInterface|null
     */
    public function get(string $name):?BlockInterface {
        return $this->getEntity(name: $name);
    }

    protected function createEntity() {
        return new Block();
    }

    public function add(BlockInterface $block): void {
        $this->addEntity($block);
    }

    public function remove(BlockInterface $block): void {
        $this->removeEntity($block);
    }

    protected function indexFromEntity(BlockInterface $block): string {
        return $block->getName();
    }

    protected function indexFromRow(array $row): string {
        return $row['name'];
    }
}
