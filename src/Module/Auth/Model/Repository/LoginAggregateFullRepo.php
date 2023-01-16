<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Auth\Model\Repository;

use Model\Repository\RepoAggregateInterface;
use Model\Hydrator\HydratorInterface;

use Model\Repository\RepoAbstract;
use Auth\Model\Dao\LoginDao;

use Auth\Model\Repository\CredentialsRepo;
use Auth\Model\Repository\RegistrationRepo;

use Auth\Model\Hydrator\LoginChildCredentialsHydrator;
use Auth\Model\Hydrator\LoginChildRegistrationHydrator;

use Model\Repository\RepoAssotiatingOneTrait;

use Auth\Model\Entity\LoginAggregateFull;
use Auth\Model\Entity\LoginAggregateFullInterface;

use Auth\Model\Entity\CredentialsInterface;
use Auth\Model\Entity\RegistrationInterface;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class LoginAggregateFullRepo extends RepoAbstract implements LoginAggregateFullRepoInterface {

    public function __construct(LoginDao $loginDao, HydratorInterface $loginHydrator
            ) {
        $this->dataManager = $loginDao;
        $this->registerHydrator($loginHydrator);
    }

    use RepoAssotiatingOneTrait;

    protected function createEntity() {
        return new LoginAggregateFull();
    }

    public function get($loginName): ?LoginAggregateFullInterface {
        return $this->getEntity($loginName);
    }

    public function add(LoginAggregateFullInterface $loginAgg) {
        $this->addEntity($loginAgg);
    }

    public function remove(LoginAggregateFullInterface $loginAgg) {
        $this->removeEntity($loginAgg);
    }

    #### protected ###########

    protected function indexFromEntity(LoginAggregateFullInterface $loginAggReg) {
        return $loginAggReg->getLoginName();
    }

    protected function indexFromRow($row) {
        return $row['login_name'];
    }
}
