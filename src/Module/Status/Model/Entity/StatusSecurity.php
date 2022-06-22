<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Status\Model\Entity;

use Model\Entity\EntityAbstract;

use Auth\Model\Entity\LoginAggregateFullInterface;
use Red\Model\Entity\UserActionsInterface;

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
     * Vrací LoginAggregateFull - login s credentials a registration
     *
     * @return LoginAggregateFullInterface|null
     */
    public function getLoginAggregate(): ?LoginAggregateFullInterface {
        return $this->loginAggregate;
    }

    public function remove(): StatusSecurityInterface {
        $this->loginAggregate = null;
        $this->userActions = null;
        return $this;
    }

    /**
     * {@inheritdoc}
     * @param LoginAggregateFullInterface $loginAggregate
     * @return void
     */
    public function renew(LoginAggregateFullInterface $loginAggregate, UserActionsInterface $userActions): StatusSecurityInterface {
        $this->loginAggregate = $loginAggregate;
        $this->userActions = $userActions;
        return $this;
    }

    public function hasSecurityContext(): bool {
        return isset($this->loginAggregate) AND $this->loginAggregate->isPersisted();
    }

    ## user action

    /**
     *
     * @return UserActionsInterface|null
     */
    public function getUserActions(): ?UserActionsInterface {
        return $this->userActions;
    }
}
