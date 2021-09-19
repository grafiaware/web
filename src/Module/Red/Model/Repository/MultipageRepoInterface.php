<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Repository;

use Model\Repository\RepoAssotiatedOneInterface;

use Red\Model\Entity\MultipageInterface;

/**
 *
 * @author pes2704
 */
interface MultipageRepoInterface extends RepoAssotiatedOneInterface {
    public function get($id): ?MultipageInterface;
    public function add(MultipageInterface $article);
    public function remove(MultipageInterface $article);
}
