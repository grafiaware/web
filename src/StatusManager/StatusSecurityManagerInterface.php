<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace StatusManager;

use Model\Entity\StatusSecurityInterface;
use Model\Entity\LoginAggregateCredentialsInterface;

/**
 *
 * @author pes2704
 */
interface StatusSecurityManagerInterface {

    /**
     * Určeno pro vytvoření bezpečnostního kontextu aplikace.
     *
     * @return StatusSecurityInterface
     */
    public function createSecurityStatus(): StatusSecurityInterface;

    /**

     * Určeno pro změnu bezpečnostního statusu. Je nutné volat vždy při změně bezpečnostního kontextu, typicky při přihlášení, odhlášení uživatele.
     *
     * Musí smazat všechny informace v security statusu odvozené z bezpečnostního kontextu,
     * typicky např. ty, vytvořené s použitím přihlášeného uživatele.
     *
     * @param LoginAggregateCredentialsInterface $loginAggregate
     * @return void
     */
    public function renewSecurityStatus(LoginAggregateCredentialsInterface $loginAggregate=null): void;
}
