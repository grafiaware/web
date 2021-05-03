<?php

namespace Auth\Model\Repository;

use Auth\Model\Entity\LoginAggregateRegistrationInterface;

/**
 * Description of LoginAggregateRegistrationRepoInterface
 *
 * @author vlse2610
 */
interface LoginAggregateRegistrationRepoInterface extends LoginRepoInterface {   
    public function get($loginName): ?LoginAggregateRegistrationInterface;
}





