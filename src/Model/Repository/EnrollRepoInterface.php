<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Entity\EnrollInterface;

/**
 *
 * @author pes2704
 */
interface EnrollRepoInterface extends RepoInterface {
    public function get($id): ?EnrollInterface;
    public function add(EnrollInterface $enroll);
    public function remove(EnrollInterface $enroll);
}
