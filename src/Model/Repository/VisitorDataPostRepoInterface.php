<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Entity\VisitorDataPostInterface;

/**
 *
 * @author pes2704
 */
interface VisitorDataPostRepoInterface extends RepoInterface {
    public function get($loginName, $shortName): ?VisitorDataPostInterface;
    public function add(VisitorDataPostInterface $enroll);
    public function remove(VisitorDataPostInterface $enroll);
}
