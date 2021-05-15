<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Repository;

use Model\Repository\RepoAssotiatedOneInterface;

use Red\Model\Entity\PaperInterface;

/**
 *
 * @author pes2704
 */
interface PaperRepoInterface extends RepoAssotiatedOneInterface {
    public function get($id): ?PaperInterface;
    public function add(PaperInterface $paper);
    public function remove(PaperInterface $paper);
}
