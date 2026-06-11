<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Auth\Model\Repository;

use Pes\Model\Hydrator\HydratorInterface;

use Pes\Model\Repository\RepoAbstract;
use Auth\Model\Dao\LoginDao;

use Pes\Model\Repository\RepoAssotiatingOneTrait;

use Auth\Model\Entity\LoginAggregateFull;
use Auth\Model\Entity\LoginAggregateFullInterface;

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

    public function __construct(LoginDao $loginDao, HydratorInterface $loginHydrator
            ) {
        $this->dataManager = $loginDao;
        $this->registerHydrator($loginHydrator);
    }

    use RepoAssotiatingOneTrait;

    protected function createEntity() {
        return new LoginAggregateFull();
    }

    /**
     * Summary of get
     * @param mixed $loginName
     * @return \Pes\Model\Entity\PersistableEntityInterface|null
     */
    public function get($loginName): ?LoginAggregateFullInterface {
        return $this->getEntity($loginName);
    }
    #### protected ###########

    protected function indexFromEntity(LoginAggregateFullInterface $loginAggReg) {
        return $loginAggReg->getLoginName();
    }

    protected function indexFromRow($row) {
        return $row['login_name'];
    }
}

