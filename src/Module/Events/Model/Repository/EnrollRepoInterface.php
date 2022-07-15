<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Events\Model\Repository;

use Model\Repository\RepoInterface;

use Events\Model\Entity\EnrollInterface;

/**
 *
 * @author pes2704
 */
interface EnrollRepoInterface extends RepoInterface {
    public function get($loginName, $eventId): ?EnrollDaoInterface;
    public function add(EnrollDaoInterface $enroll);
    public function remove(EnrollDaoInterface $enroll);
}
