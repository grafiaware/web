<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Repository;
use Model\Entity\LoginAggregateInterface;
use Model\Entity\LoginAggregate;
use Model\Entity\Credentials;
use Model\Dao\LoginAggregateDao;
use Model\Hydrator\HydratorInterface;

use Model\Repository\Exception\UnableRecreateEntityException;

/**
 * Description of Menu
 *
 * @author pes2704
 */
class LoginAggregateReadonlyRepo extends RepoAbstract implements RepoInterface {

    /**
     *
     * @var HydratorInterface
     */
    private $childHydrator;



    public function __construct(
            LoginAggregateDao $loginAggregateDao,
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
     * @return LoginAggregateInterface|null
     */
    public function get($loginName): ?LoginAggregateInterface {
        $index = $this->indexFromKeyParams($loginName);
        if (!isset($this->collection[$index])) {
            $joinedRow = $this->dao->get($loginName);
            $credentials = $this->createChildEntity($joinedRow);
            $this->recreateEntity($index, ['login_name'=>$joinedRow['login_name'], 'credentials'=>$credentials]);
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

    protected function indexFromEntity(LoginAggregateInterface $loginAggregate) {
        return $loginAggregate->getLoginName();
    }

    protected function indexFromRow($row) {
        return $row['login_name'];
    }
    }

