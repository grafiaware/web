<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Red\Model\Repository;

use Model\Repository\RepoAssotiatedManyInterface;

use Red\Model\Entity\PaperSectionInterface;

/**
 *
 * @author pes2704
 */
interface PaperSectionRepoInterface extends RepoAssotiatedManyInterface {
    public function get($id): ?PaperSectionInterface;
    public function add(PaperSectionInterface $paper);
    public function remove(PaperSectionInterface $paper);
    public function findByPaperIdFk($paperIdFk): iterable;
}
