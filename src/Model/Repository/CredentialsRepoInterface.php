<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Repository\RepoAssotiatedOneInterface;

use Model\Entity\CredentialsInterface;
use Model\Entity\EntityInterface;

/**
 *
 * @author pes2704
 */
interface CredentialsRepoInterface extends RepoAssotiatedOneInterface {
    public function get($loginNameFk): ?CredentialsInterface;
    public function getByReference($id): ?EntityInterface;
    public function add(CredentialsInterface $credentials);
    public function remove(CredentialsInterface $credentials);
}
