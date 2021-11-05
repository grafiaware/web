<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Auth\Model\Repository;

use Model\Hydrator\HydratorInterface;
use Model\Repository\RepoAggregateInterface;

use Auth\Model\Repository\LoginRepo;
use Auth\Model\Dao\LoginDao;
use Auth\Model\Repository\CredentialsRepo;
use Auth\Model\Hydrator\LoginChildCredentialsHydrator;

use Auth\Model\Entity\LoginAggregateCredentials;
use Auth\Model\Entity\LoginAggregateCredentialsInterface;
use Auth\Model\Entity\LoginInterface;
use Auth\Model\Entity\CredentialsInterface;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class LoginAggregateCredentialsRepo extends LoginRepo implements RepoAggregateInterface {

    public function __construct(LoginDao $loginDao, HydratorInterface $loginHydrator,
            CredentialsRepo $credentialsRepo, LoginChildCredentialsHydrator $loginCredentialsHydrator) {
        parent::__construct($loginDao, $loginHydrator);
        $this->registerOneToOneAssociation(CredentialsInterface::class, 'login_name', $credentialsRepo);
        $this->registerHydrator($loginCredentialsHydrator);
    }

    protected function createEntity() {
        return new LoginAggregateCredentials();
    }

    public function find($whereClause="", $touplesToBind=[]) {
        $selected = [];
        foreach ($this->dataManager->find($whereClause, $touplesToBind) as $loginRow) {
            $index = $this->indexFromRow($loginRow);
            if (!isset($this->collection[$index])) {
                $this->recreateEntity($index, $loginRow);
            }
            $selected[] = $this->collection[$index];
        }
        return $selected;
    }

}
