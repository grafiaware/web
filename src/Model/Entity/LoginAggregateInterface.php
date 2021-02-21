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
interface LoginAggregateInterface {

    /**
     *
     * @return string|null
     */
    public function getLoginName(): ?string;

    /**
     *
     * @param string $loginName
     * @return LoginAggregateInterface
     */
    public function setLoginName(string $loginName): LoginAggregateInterface;

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
