<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Module\Status\Model\Entity;

use Model\Entity\EntityAbstract;

use Auth\Model\Entity\LoginAggregateFullInterface;

use Model\Entity\UserActionsInterface;
use Model\Entity\UserActions;

/**
 * Description of Login
 *
 * @author pes2704
 */
class StatusSecurity extends EntityAbstract implements StatusSecurityInterface {

    /**
     * @var LoginAggregateFullInterface
     */
    private $loginAggregate;

    /**
     * @var UserActionsInterface
     */
    private $userActions;

    /**
     * Vrací jméno
     *
     * @return LoginAggregateFullInterface|null
     */
    public function getLoginAggregate(): ?LoginAggregateFullInterface {
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
     * @return StatusSecurityInterface
     */
    public function setUserActions(UserActionsInterface $userActions): StatusSecurityInterface {
        $this->userActions = $userActions;
        return $this;
    }

    /**
     * {@inheritdoc}
     * @param LoginAggregateFullInterface $loginAggregate
     * @return void
     */
    public function renewSecurityStatus(LoginAggregateFullInterface $loginAggregate=null): StatusSecurityInterface {
        $this->loginAggregate = $loginAggregate;
        $this->userActions = new UserActions();  // má default hodnoty
        return $this;
    }

    public function hasSecurityContext(): bool {
        return isset($this->loginAggregate) AND $this->loginAggregate->isPersisted();
    }
}
