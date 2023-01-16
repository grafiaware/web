<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Auth\Model\Repository;

use Model\Repository\RepoAbstract;
use Model\Hydrator\HydratorInterface;

use \Model\Repository\RepoAssotiatingOneTrait;

use Auth\Model\Dao\LoginDao;

use Auth\Model\Entity\LoginAggregateCredentials;
use Auth\Model\Entity\LoginAggregateCredentialsInterface;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class LoginAggregateCredentialsRepo  extends RepoAbstract implements LoginAggregateCredentialsRepoInterface {

    public function __construct(LoginDao $loginDao, HydratorInterface $loginHydrator) {
        $this->dataManager = $loginDao;
        $this->registerHydrator($loginHydrator);
    }

    use RepoAssotiatingOneTrait;

    protected function createEntity() {
        return new LoginAggregateCredentials();
    }

    public function get($loginName): ?LoginAggregateCredentialsInterface {
        return $this->getEntity($loginName);
    }

    /**
     *
     * @param LoginAggregateCredentialsInterface $loginAgg
     */
    public function add(LoginAggregateCredentialsInterface $loginAgg) {
        $this->addEntity($loginAgg);
    }

    /**
     *
     * @param LoginAggregateCredentialsInterface $loginAgg
     */
    public function remove(LoginAggregateCredentialsInterface $loginAgg) {
        $this->removeEntity($loginAgg);
    }

    #### protected ###########

    protected function indexFromEntity(LoginAggregateCredentialsInterface $loginAggCred) {
        return $loginAggCred->getLoginName();
    }

    protected function indexFromRow($row) {
        return $row['login_name'];
    }

}
