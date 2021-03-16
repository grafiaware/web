<?php

namespace Model\Repository;

use Model\Repository\RepoAssotiatedOneInterface;

use Model\Entity\RegistrationInterface;
use Model\Entity\EntityInterface;

/**
 *
 * @author vlse2610
 */
interface RegistrationRepoInterface extends RepoAssotiatedOneInterface {

    public function get($loginNameFk): ?RegistrationInterface;

    public function getByUid($uid): ?RegistrationInterface;

    public function add(RegistrationInterface $registration);

    public function remove(RegistrationInterface $registration);
    
}
