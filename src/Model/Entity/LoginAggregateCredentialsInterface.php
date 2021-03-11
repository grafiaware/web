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
interface LoginAggregateCredentialsInterface extends LoginInterface {

    /**
     * 
     * @return \Model\Entity\CredentialsInterface|null
     */
    public function getCredentials(): ?CredentialsInterface;

    /**
     *
     * @param CredentialsInterface $credentials
     * @return LoginAggregateCredentialsInterface
     */
    public function setCredentials(CredentialsInterface $credentials = null): LoginAggregateCredentialsInterface;
}
