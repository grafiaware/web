<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Auth\Model\Repository;

use Model\Repository\RepoInterface;

use Auth\Model\Entity\LoginAggregateCredentialsInterface;

/**
 *
 * @author pes2704
 */
interface LoginAggregateCredentialsRepoInterface  extends RepoInterface {

    /**
     *
     * @param type $loginName
     * @return LoginAggregateCredentialsInterface|null
     */
    public function get($loginName): ?LoginAggregateCredentialsInterface;

    /**
     *
     * @param LoginAggregateCredentialsInterface $login
     */
    public function add(LoginAggregateCredentialsInterface $login);

    /**
     *
     * @param LoginAggregateCredentialsInterface $login
     */
    public function remove(LoginAggregateCredentialsInterface $login);
}
