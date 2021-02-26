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
 * Třída nemá metodu getUser(), nikdy nevrací celý objekt User. Tak nelze měnit vlastnosti objektu User získaného z StatusSecurity.
 * Nelze použít:
 * <code>
 * $statusSecurity->getUser()->setUserName('Adam');
 * </code>
 * Lze nastavit jen nový objekt User metodou setUser(). Jde o malou zábranu změnám vlastností objektu User bez změny ostatních objektů
 * v bezpečnostním kontextu.
 *
 * @author pes2704
 */
interface StatusSecurityInterface extends EntitySingletorInterface {

    /**
     *
     * @return LoginAggregateCredentialsInterface|null
     */
    public function getLoginAggregate(): ?LoginAggregateCredentialsInterface;

    /**
     *
     * @return UserActionsInterface|null
     */
    public function getUserActions(): ?UserActionsInterface;

    /**
     *
     * @param UserActionsInterface $userActions
     * @return \Model\Entity\StatusSecurityInterface
     */
    public function setUserActions(UserActionsInterface $userActions): StatusSecurityInterface;

    /**
     *
     * @param LoginAggregateCredentialsInterface $loginAggregate
     * @return void
     */
    public function renewSecurityStatus(LoginAggregateCredentialsInterface $loginAggregate=null): StatusSecurityInterface;
}
