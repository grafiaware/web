<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace StatusManager;

use Model\Entity\StatusSecurity;
use Model\Entity\StatusSecurityInterface;
use Model\Entity\LoginAggregateInterface;

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
     * @param LoginAggregateInterface $loginAggregate
     * @return void
     */
    public function renewSecurityStatus(LoginAggregateInterface $loginAggregate=null): void {
        $this->statusSecurity->setUser($loginAggregate);
        $this->statusSecurity->setUserActions(new UserActions());  // má default hodnoty

    }
}
