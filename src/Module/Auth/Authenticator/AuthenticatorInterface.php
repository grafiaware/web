<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Auth\Authenticator;

use Auth\Model\Entity\LoginAggregateCredentialsInterface;

/**
 *
 * @author pes2704
 */
interface AuthenticatorInterface {

    /**
     *
     * @param LoginAggregateCredentialsInterface $loginAggregateEntity
     * @param string $password
     * @return bool
     */
    public function authenticate(LoginAggregateCredentialsInterface $loginAggregateEntity, $password): bool;

}
