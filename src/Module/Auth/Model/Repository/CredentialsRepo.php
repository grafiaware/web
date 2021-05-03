<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Auth\Model\Repository;

use Model\Repository\RepoAbstract;
use Model\Entity\EntityInterface;
use Model\Hydrator\HydratorInterface;

use Auth\Model\Entity\CredentialsInterface;
use Auth\Model\Entity\Credentials;
use Auth\Model\Dao\CredentialsDao;

use Auth\Model\Repository\Exception\UnableRecreateEntityException;



class CredentialsRepo extends RepoAbstract implements CredentialsRepoInterface {

    public function __construct(CredentialsDao $credentialsDao, HydratorInterface $credentialsHydrator) {
        $this->dao = $credentialsDao;
        $this->registerHydrator($credentialsHydrator);
    }


    /**
     *
     * @param type $loginNameFk
     * @return CredentialsInterface|null
     */
    public function get($loginNameFk): ?CredentialsInterface {
        $index = $this->indexFromKeyParams($loginNameFk);
        if (!isset($this->collection[$index])) {
            $this->recreateEntity($index, $this->dao->get($loginNameFk));
        }
        return $this->collection[$index] ?? NULL;
    }

    public function getByReference($loginNameFk): ?EntityInterface {
        return $this->get($loginNameFk);
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

