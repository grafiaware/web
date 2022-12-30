<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Auth\Model\Repository;

use Model\Repository\RepoAbstract;

use Model\Hydrator\HydratorInterface;
use Model\Entity\PersistableEntityInterface;

use Auth\Model\Entity\CredentialsInterface;
use Auth\Model\Entity\Credentials;
use Auth\Model\Dao\CredentialsDao;

use Auth\Model\Repository\Exception\UnableRecreateEntityException;



class CredentialsRepo extends RepoAbstract implements CredentialsRepoInterface {

    public function __construct(CredentialsDao $credentialsDao, HydratorInterface $credentialsHydrator) {
        $this->dataManager = $credentialsDao;
        $this->registerHydrator($credentialsHydrator);
    }

    /**
     *
     * @param type $loginNameFk
     * @return CredentialsInterface|null
     */
    public function get($loginNameFk): ?CredentialsInterface {
        $key = $this->dataManager->getPrimaryKeyTouples(['login_name_fk'=>$loginNameFk]);
        return $this->getEntity($key);
    }

    public function getByReference($loginNameFk): ?PersistableEntityInterface {
        $key = $this->dataManager->getPrimaryKeyTouples(['login_name_fk'=>$loginNameFk]);
        return $this->getEntity($key);
    }

    public function add(CredentialsInterface $credentials) {
        $this->addEntity($credentials);
    }

    public function remove(CredentialsInterface $credentials) {
        $this->removeEntity($credentials);
    }

    protected function createEntity() {
        return new Credentials();
    }

    protected function indexFromKeyParams($loginNameFk) {
        return $loginNameFk;
    }

    protected function indexFromEntity(CredentialsInterface $credentials) {
        return $credentials->getLoginNameFk();
    }

    protected function indexFromRow($row) {
        return $row['login_name_fk'];
    }
    }

