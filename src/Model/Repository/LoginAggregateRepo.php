<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Repository\LoginRepo;

use Model\Dao\LoginDao;
use Model\Hydrator\HydratorInterface;

use Model\Repository\CredentialsRepo;
use Model\Entity\LoginAggregate;
use Model\Hydrator\LoginChildHydrator;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class LoginAggregateRepo extends LoginRepo implements LoginRepoInterface {

    public function __construct(LoginDao $loginDao, HydratorInterface $loginHydrator,
            CredentialsRepo $credentialsRepo, LoginChildHydrator $loginCredentialsHydrator) {
        parent::__construct($loginDao, $loginHydrator);
        $this->registerOneToOneAssotiation('credentials', 'login_name', $credentialsRepo);
        $this->registerHydrator($loginCredentialsHydrator);
    }

    protected function createEntity() {
        return new LoginAggregate();
    }

}
