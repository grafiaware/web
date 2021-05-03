<?php

namespace Auth\Model\Repository;

use Model\Repository\RepoAssotiatedOneInterface;

use Auth\Model\Entity\CredentialsInterface;
use Auth\Model\Entity\EntityInterface;

/**
 *
 * @author pes2704
 */
interface CredentialsRepoInterface extends RepoAssotiatedOneInterface {
    public function get($loginNameFk): ?CredentialsInterface;
    public function add(CredentialsInterface $credentials);
    public function remove(CredentialsInterface $credentials);
}
