<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Entity;

use Model\Entity\LoginAggregateCredentialsInterface;
use Model\Entity\UserActionsInterface;

/**
 * Description of Login
 *
 * @author pes2704
 */
class StatusSecurity extends EntityAbstract implements StatusSecurityInterface {

    /**
     * @var LoginAggregateCredentialsInterface
     */
    private $loginAggregate;

    /**
     * @var UserActionsInterface
     */
    private $userActions;

    /**
     * Vrací jméno
     *
     * @return \Model\Entity\LoginAggregateCredentialsInterface|null
     */
    public function getLoginAggregate(): ?LoginAggregateCredentialsInterface {
        return $this->loginAggregate;
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
     * @param LoginAggregateCredentialsInterface $loginAggregate
     * @return void
     */
    public function renewSecurityStatus(LoginAggregateCredentialsInterface $loginAggregate=null): StatusSecurityInterface {
        $this->loginAggregate = $loginAggregate;
        $this->userActions = new UserActions();  // má default hodnoty
        return $this;
    }
}
