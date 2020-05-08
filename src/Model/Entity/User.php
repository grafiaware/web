<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Model\Entity;

/**
 * Description of User
 *
 * @author pes2704
 */
class User implements UserInterface {

    /**
     * @var string
     */
    private $userName;

    /**
     * @var string
     */
    private $role;

    public function __construct() {
        $this->securityObservers = new \SplObjectStorage();
    }

    public function getUserName() {
        return $this->userName;
    }

    public function setUserName($userName): UserInterface {
        $this->notify();
        $this->userName = $userName;
        return $this;
    }

    public function getRole() {
        return $this->role;
    }

    public function setRole($role): UserInterface {
        $this->role = $role;
        return $this;
    }
}
