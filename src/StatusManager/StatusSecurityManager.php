<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace StatusManager;

use Model\Entity\StatusSecurity;
use Model\Entity\StatusSecurityInterface;
use Model\Entity\LoginAggregateCredentialsInterface;

/**
 * Description of SecurityStatusManager
 *
 * @author pes2704
 */
class StatusSecurityManager implements StatusManagerInterface {

    /**
     * @var StatusSecurityInterface
     */
    protected $statusSecurity;

    /**
     * Určeno pro vytvoření bezpečnostního kontextu aplikace.
     *
     * @return EntitySingletonInterface
     */
    public function createStatus(): StatusSecurityInterface {
        $this->statusSecurity = new StatusSecurity();
        return $this->statusSecurity;
    }

    /**
     * Určeno pro změnu bezpečnostního statusu. Je nutné volat vždy při změně bezpečnostního kontextu, typicky při přihlášení, odhlášení uživatele.
     *
     * Musí smazat všechny informace v security statusu odvozené z bezpečnostního kontextu,
     * typicky např. ty, vytvořené s použitím přihlášeného uživatele.
     *
     * @param LoginAggregateCredentialsInterface $loginAggregate
     * @return void
     */
    public function renewStatus(LoginAggregateCredentialsInterface $loginAggregate=null): void {
        $this->statusSecurity->setUser($loginAggregate);
        $this->statusSecurity->setUserActions(new UserActions());  // má default hodnoty

    }
}
