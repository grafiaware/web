<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;

use Model\Entity\EntityInterface;
use Model\Entity\CredentialsInterface;
use Model\Entity\Credentials;
use Model\Dao\CredentialsDao;
use Model\Hydrator\HydratorInterface;

use Model\Repository\Exception\UnableRecreateEntityException;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class CredentialsRepo extends RepoAbstract implements CredentialsRepoInterface {

    public function __construct(CredentialsDao $credentialdDao, HydratorInterface $userHydrator) {
        $this->dao = $credentialdDao;
        $this->registerHydrator($userHydrator);
    }

    /**
     *
     * @param type $loginNameFk
     * @return PaperInterface|null
     */
    public function get($loginNameFk): ?CredentialsInterface {
        $index = $this->indexFromKeyParams($loginNameFk);
        if (!isset($this->collection[$index])) {
            $this->recreateEntity($index, $this->dao->get($loginNameFk));
        }
        return $this->collection[$index] ?? NULL;
    }

    public function getByReference($id): ?EntityInterface {
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

    protected function indexFromKeyParams($loginName) {
        return $loginName;
    }

    protected function indexFromEntity(CredentialsInterface $credentials) {
        return $credentials->getLoginName();
    }

    protected function indexFromRow($row) {
        return $row['login_name'];
    }
    }

