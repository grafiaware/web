<?php

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
    public function add(CredentialsInterface $credentials);
    public function remove(CredentialsInterface $credentials);
}
