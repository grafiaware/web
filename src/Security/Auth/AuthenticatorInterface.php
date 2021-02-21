<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Security\Auth;

use Model\Entity\LoginAggregateInterface;

/**
 *
 * @author pes2704
 */
interface AuthenticatorInterface {

    /**
     *
     * @param LoginAggregateInterface $loginAggregateEntity
     * @param string $password
     * @return bool
     */
    public function authenticate(LoginAggregateInterface $loginAggregateEntity, $password): bool;

}
