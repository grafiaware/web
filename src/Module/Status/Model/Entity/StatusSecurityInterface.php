<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Status\Model\Entity;

use Model\Entity\EntityInterface;

use Auth\Model\Entity\LoginAggregateFullInterface;

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
interface StatusSecurityInterface extends EntityInterface {

    /**
     *
     * @return LoginAggregateFullInterface|null
     */
    public function getLoginAggregate(): ?LoginAggregateFullInterface;


    /**
     *
     * @param LoginAggregateFullInterface $loginAggregate
     * @return void
     */
    public function renewSecurityStatus(LoginAggregateFullInterface $loginAggregate=null): StatusSecurityInterface;

    public function hasSecurityContext(): bool;
}
