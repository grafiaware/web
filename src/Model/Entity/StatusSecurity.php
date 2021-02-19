<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Entity;

use Model\Entity\CredentialsInterface;
use Model\Entity\UserActionsInterface;

/**
 * Description of Login
 *
 * @author pes2704
 */
class StatusSecurity implements StatusSecurityInterface {

    /**
     * @var CredentialsInterface
     */
    private $credentials;

    /**
     * @var UserActionsInterface 
     */
    private $userActions;

    /**
     * Vrací jméno
     *
     * @return \Model\Entity\CredentialsInterface|null
     */
    public function getCredentials(): ?CredentialsInterface {
        return $this->credentials;
    }

    /**
     *
     * @return UserActionsInterface|null
     */
    public function getUserActions(): ?UserActionsInterface {
        return $this->userActions;
    }

    /**
     *
     * @param UserActionsInterface $userActions
     * @return \Model\Entity\StatusSecurityInterface
     */
    public function setUserActions(UserActionsInterface $userActions): StatusSecurityInterface {
        $this->userActions = $userActions;
        return $this;
    }

    /**
     * {@inheritdoc}
     * @param CredentialsInterface $credentials
     * @return void
     */
    public function renewSecurityStatus(CredentialsInterface $credentials=null): StatusSecurityInterface {
        $this->credentials = $credentials;
        $this->userActions = new UserActions();  // má default hodnoty
        return $this;
    }
}
