<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Entity\PaperContentInterface;

/**
 *
 * @author pes2704
 */
interface PaperContentRepoInterface extends RepoAssotiatedManyInterface {
    public function get($contentId): ?PaperContentInterface;
    public function findByReference($paperIdFk): iterable;
    public function add(PaperContentInterface $paper);
    public function remove(PaperContentInterface $paper);
}
