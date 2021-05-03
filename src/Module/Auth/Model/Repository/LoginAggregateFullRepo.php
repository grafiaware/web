<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Auth\Model\Repository;

use Model\Repository\RepoAggregateInterface;
use Model\Hydrator\HydratorInterface;

use Auth\Model\Repository\LoginRepo;
use Auth\Model\Dao\LoginDao;

use Auth\Model\Repository\CredentialsRepo;
use Auth\Model\Repository\RegistrationRepo;

use Auth\Model\Hydrator\LoginChildCredentialsHydrator;
use Auth\Model\Hydrator\LoginChildRegistrationHydrator;

use Auth\Model\Entity\LoginAggregateFull;
use Auth\Model\Entity\LoginAggregateFullInterface;
use Auth\Model\Entity\CredentialsInterface;
use Auth\Model\Entity\RegistrationInterface;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class LoginAggregateFullRepo extends LoginRepo implements RepoAggregateInterface {

    public function __construct(LoginDao $loginDao, HydratorInterface $loginHydrator,
            CredentialsRepo $credentialsRepo, LoginChildCredentialsHydrator $loginCredentialsHydrator,
            RegistrationRepo $registrationRepo, LoginChildRegistrationHydrator $loginRegistrationHydrator
            ) {
        parent::__construct($loginDao, $loginHydrator);
        $this->registerOneToOneAssociation(CredentialsInterface::class, 'login_name', $credentialsRepo);
        $this->registerOneToOneAssociation(RegistrationInterface::class, 'login_name', $registrationRepo);
        $this->registerHydrator($loginCredentialsHydrator);
        $this->registerHydrator($loginRegistrationHydrator);
    }

    protected function createEntity() {
        return new LoginAggregateFull();
    }

    public function findAll() {
        $selected = [];
        foreach ($this->dao->findAll() as $loginRow) {
            $index = $this->indexFromRow($loginRow);
            if (!isset($this->collection[$index])) {
                $this->recreateEntity($index, $loginRow);
            }
            $selected[] = $this->collection[$index];
        }
        return $selected;
    }

}
