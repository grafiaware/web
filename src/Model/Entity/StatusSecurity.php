<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Entity;

/**
 * Description of Login
 *
 * @author pes2704
 */
class StatusSecurity implements StatusSecurityInterface {

    /**
     * @var UserInterface
     */
    private $user;

    /**
     * Vrací entitu User
     * 
     * @return \Model\Entity\UserInterface|null
     */
    public function getUser(): ?UserInterface {
        return $this->user;
    }

    /**
     * Nastaví entitu User
     * @param \Model\Entity\UserInterface $user
     * @return \Model\Entity\StatusSecurityInterface
     */
    public function setUser(UserInterface $user): StatusSecurityInterface {
        $this->user = $user;
        return $this;
    }
}
