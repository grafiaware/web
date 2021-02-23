<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Entity;

/**
 *
 * @author pes2704
 */
interface LoginAggregateInterface extends LoginInterface {

    /**
     *
     * @return CredentialsInterface
     */
    public function getCredentials(): CredentialsInterface;

    /**
     *
     * @param CredentialsInterface $credentials
     * @return LoginAggregateInterface
     */
    public function setCredentials(CredentialsInterface $credentials): LoginAggregateInterface;
}
