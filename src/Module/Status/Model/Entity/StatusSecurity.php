<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Status\Model\Entity;

use Model\Entity\PersistableEntityAbstract;

use Auth\Model\Entity\LoginAggregateFullInterface;
use Red\Model\Entity\UserActions;

use Red\Model\Entity\UserActionsInterface;

/**
 * Description of Login
 *
 * @author pes2704
 */
class StatusSecurity extends PersistableEntityAbstract implements StatusSecurityInterface {

    /**
     * @var LoginAggregateFullInterface
     */
    private $loginAggregate;

    /**
     * @var UserActionsInterface
     */
    private $userActions;
    
    private $info = [];

    /**
     * {@inheritdoc}
     * 
     * @return StatusSecurityInterface
     */
    public function removeContext(): StatusSecurityInterface {
        if (isset($this->loginAggregate)) {
            $this->userActions->processActionsForLossOfSecurityContext($this->loginAggregate->getLoginName());
            $this->loginAggregate = null;
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     * 
     * @param LoginAggregateFullInterface $loginAggregate
     * @return void
     */
    public function new(LoginAggregateFullInterface $loginAggregate): StatusSecurityInterface {
        $this->loginAggregate = $loginAggregate;
        $this->userActions = new UserActions();
        return $this;
    }

    /**
     * {@inheritdoc}
     * 
     * @return bool
     */
    public function hasValidSecurityContext(): bool {
        return isset($this->loginAggregate) AND $this->loginAggregate->isPersisted();
    }
    
    ### GETTERY
    
    /**
     * Vrací LoginAggregateFull - login s credentials a registration
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
    
    public function setInfo($name, $value) {
        $this->info[$name] = $value;
    }
    public function getInfo($name) {
        return $this->info[$name] ?? null;
    }
    public function getInfos(): array {
        return $this->info;
    }    
}
