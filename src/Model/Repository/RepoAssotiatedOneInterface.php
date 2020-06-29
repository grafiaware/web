<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Entity\EntityInterface;

/**
 *
 * @author pes2704
 */
interface RepoAssotiatedOneInterface extends RepoInterface {
    public function getByReference($id): ?EntityInterface;
}
