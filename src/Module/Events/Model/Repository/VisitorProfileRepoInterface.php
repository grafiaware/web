<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Repository;

use Model\Repository\RepoInterface;
use Events\Model\Entity\VisitorProfileInterface;

/**
 *
 * @author pes2704
 */
interface VisitorProfileRepoInterface extends RepoInterface {
    public function get($id): ?VisitorProfileInterface;
    public function add(VisitorProfileInterface $enroll);
    public function remove(VisitorProfileInterface $enroll);
}