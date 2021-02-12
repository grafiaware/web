<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Security\Auth;

/**
 *
 * @author pes2704
 */
interface NamePasswordAuthenticatorInterface {

    /**
     *
     * @param string $loginName
     * @param string $password
     * @return bool
     */
    public function authenticate($loginName, $password): bool;

}
