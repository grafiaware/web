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
class Credentials extends EntityAbstract implements CredentialsInterface {

    /**
     * @var string
     */
    private $loginName;

    /**
     * @var string
     */
    private $role;

    public function __construct() {
        $this->securityObservers = new \SplObjectStorage();
    }

    public function getLoginName() {
        return $this->loginName;
    }

    public function setLoginName($loginName): CredentialsInterface {
        $this->loginName = $loginName;
        return $this;
    }

    public function getRole() {
        return $this->role;
    }

    public function setRole($role): CredentialsInterface {
        $this->role = $role;
        return $this;
    }
}
