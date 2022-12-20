<?php

namespace Auth\Model\Repository;

use Model\Repository\RepoAssotiatingOneInterface;
use Auth\Model\Entity\LoginAggregateRegistrationInterface;

/**
 * Description of LoginAggregateRegistrationRepoInterface
 *
 * @author vlse2610
 */
interface LoginAggregateRegistrationRepoInterface extends RepoAssotiatingOneInterface {

    /**
     *
     * @param type $loginName
     * @return LoginAggregateRegistrationInterface|null
     */
    public function get($loginName): ?LoginAggregateRegistrationInterface;

    /**
     *
     * @param LoginAggregateRegistrationInterface $loginAgg
     */
    public function add(LoginAggregateRegistrationInterface $loginAgg);

    /**
     *
     * @param LoginAggregateRegistrationInterface $loginAgg
     */
    public function remove(LoginAggregateRegistrationInterface $loginAgg);

}





