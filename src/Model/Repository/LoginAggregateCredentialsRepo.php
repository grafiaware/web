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

use Model\Hydrator\LoginChildHydrator;

use Model\Entity\LoginAggregateCredentials;
use Model\Entity\LoginAggregateCredentialsInterface;
use Model\Entity\LoginInterface;
use Model\Entity\CredentialsInterface;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class LoginAggregateCredentialsRepo extends LoginRepo implements LoginRepoInterface {

    public function __construct(LoginDao $loginDao, HydratorInterface $loginHydrator,
            CredentialsRepo $credentialsRepo, LoginChildHydrator $loginCredentialsHydrator) {
        parent::__construct($loginDao, $loginHydrator);
        $this->registerOneToOneAssotiation(CredentialsInterface::class, 'login_name', $credentialsRepo);
        $this->registerHydrator($loginCredentialsHydrator);
    }

    protected function createEntity() {
        return new LoginAggregateCredentials();
    }

    public function add(LoginInterface $loginAggregate) {
        /** @var LoginAggregateCredentialsInterface $loginAggregate */
        $this->addAssociated(CredentialsInterface::class, $loginAggregate); //add($loginAggregate->getCredentials()); <- do repo abstract
        parent::add($loginAggregate);
    }
    public function remove(LoginInterface $loginAggregate) {
        /** @var LoginAggregateCredentialsInterface $loginAggregate */
        $this->removeAssociated(CredentialsInterface::class, $loginAggregate); //add($loginAggregate->getCredentials()); <- do repo abstract
        parent::add($loginAggregate);
    }
}
