<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Auth\Model\Repository;

use Model\Repository\RepoAbstract;

use Auth\Model\Entity\LoginAggregateCredentialsInterface;
use Auth\Model\Entity\LoginAggregate;
use Auth\Model\Entity\Credentials;
use Auth\Model\Dao\LoginAggregateReadonlyDao;
use Auth\Model\Hydrator\HydratorReadonlyInterface;
use Auth\Model\Hydrator\HydratorInterface;

use Auth\Model\Repository\Exception\UnableRecreateEntityException;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class LoginAggregateReadonlyRepo extends RepoAbstract implements LoginAggregateReadonlyRepoInterface {

    /**
     *
     * @var HydratorInterface
     */
    private $childHydrator;

    public function __construct(
            LoginAggregateReadonlyDao $loginAggregateDao,
            HydratorInterface $loginAggregateHydrator,
            HydratorInterface $credentialsHydrator
            ) {
        $this->dao = $loginAggregateDao;
        $this->registerHydrator($loginAggregateHydrator);
        $this->childHydrator = $credentialsHydrator;
    }

    /**
     *
     * @param string $loginName
     * @return LoginAggregateCredentialsInterface|null
     */
    public function get($loginName): ?LoginAggregateCredentialsInterface {
        $index = $this->indexFromKeyParams($loginName);
        if (!isset($this->collection[$index])) {
            $joinedRow = $this->dao->get($loginName);
            if ($joinedRow) {
                $credentials = $this->createChildEntity($joinedRow);
                $this->recreateEntity($index, ['login_name'=>$joinedRow['login_name'], 'credentials'=>$credentials]);
            }
        }
        return $this->collection[$index] ?? NULL;
    }

    private function createChildEntity($joinedRow) {
        $credentials = new Credentials();
        $this->childHydrator->hydrate($credentials, $joinedRow);
        return $credentials;
    }

    protected function createEntity() {
        return new LoginAggregate();
    }

    protected function indexFromKeyParams($loginName) {
        return $loginName;
    }

    protected function indexFromEntity(LoginAggregateCredentialsInterface $loginAggregate) {
        return $loginAggregate->getLoginName();
    }

    protected function indexFromRow($row) {
        return $row['login_name'];
    }
    }

