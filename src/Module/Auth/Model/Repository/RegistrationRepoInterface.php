<?php

namespace Auth\Model\Repository;

use Model\Repository\RepoAssotiatedOneInterface;

use Auth\Model\Entity\RegistrationInterface;
use Auth\Model\Entity\EntityInterface;

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