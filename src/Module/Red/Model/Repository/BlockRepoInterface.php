<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Repository;

use Red\Model\Entity\BlockInterface;

/**
 *
 * @author pes2704
 */
interface BlockRepoInterface {
    /**
     *
     * @param string $name
     * @return BlockInterface|null
     */
    public function get($name):?BlockInterface;

    public function add(BlockInterface $block);

    public function remove(BlockInterface $block);

}
