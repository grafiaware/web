<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace StatusManager;

use Model\Entity\StatusSecurityInterface;

/**
 *
 * @author pes2704
 */
interface StatusSecurityManagerInterface extends \SplSubject {

    /**
     * Určeno pro volání při změně bezpečnostního kontextu aplikace. Musí smazat všechny informace v security statusu odvozené z bezpečnostního kontextu,
     * typicky např. ty, vytvořené s použitím přihlášeného uživatele - změna kontextu je přihlášení, odhlášení uživatele.
     *
     * @return void
     */
    public function newSecurityStatus(): StatusSecurityInterface;
}
