<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Auth\Model\Entity;

use Model\Entity\EntityInterface;

/**
 *
 * @author pes2704
 */
interface LoginAggregateFullInterface extends LoginAggregateCredentialsInterface, LoginAggregateRegistrationInterface {

    /**
     *
     * @return CredentialsInterface|null
     */
    public function getCredentials(): ?CredentialsInterface;


    /**
     *
     * @return RegistrationInterface|null
     */
    public function getRegistration(): ?RegistrationInterface;

    /**
     *
     * @param RegistrationInterface $registration
     * @return void
     */
    public function setRegistration(RegistrationInterface $registration=null): void;

    /**
     *
     * @param CredentialsInterface $credentials
     * @return void
     */
    public function setCredentials(CredentialsInterface $credentials = null): void;

}
