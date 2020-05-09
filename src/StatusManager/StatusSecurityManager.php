<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace StatusManager;

use Model\Entity\StatusSecurity;
use Model\Entity\StatusSecurityInterface;
use Model\Entity\UserInterface;

/**
 * Description of SecurityStatusManager
 *
 * @author pes2704
 */
class StatusSecurityManager implements StatusSecurityManagerInterface {

    /**
     * @var StatusSecurityInterface
     */
    protected $statusSecurity;

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function createSecurityStatus(): StatusSecurityInterface {
        $this->statusSecurity = new StatusSecurity();
        return $this->statusSecurity;
    }

    /**
     * {@inheritdoc}
     * @param UserInterface $user
     * @return void
     */
    public function renewSecurityStatus(UserInterface $user=null): void {
        $this->statusSecurity->setUser($user);
        $this->statusSecurity->setUserActions(new UserActions());  // má default hodnoty

    }
}
