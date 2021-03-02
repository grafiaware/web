<?php

namespace Model\Repository;

use Model\Entity\LoginAggregateRegistrationInterface;

/**
 * Description of LoginAggregateRegistrationRepoInterface
 *
 * @author vlse2610
 */
interface LoginAggregateRegistrationRepoInterface extends LoginRepoInterface {   
    public function get($loginName): ?LoginAggregateRegistrationInterface;
}





