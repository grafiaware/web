<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Repository;

use Events\Model\Entity\VisitorDataInterface;

/**
 *
 * @author pes2704
 */
interface VisitorDataRepoInterface extends RepoInterface {
    public function get($id): ?VisitorDataInterface;
    public function add(VisitorDataInterface $enroll);
    public function remove(VisitorDataInterface $enroll);
}
