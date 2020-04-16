<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Entity;

use Model\Entity\UserInterface;
use Model\Entity\UserActionsInterface;

/**
 *
 * @author pes2704
 */
interface StatusSecurityInterface {

    /**
     * Vrací entitu User
     * 
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface;

    /**
     * Nastaví entitu User
     * @param \Model\Entity\UserInterface $user
     * @return \Model\Entity\StatusSecurityInterface
     */
    public function setUser(UserInterface $user): StatusSecurityInterface;
}
