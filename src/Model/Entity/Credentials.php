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
    private $password_hash;

    /**
     * @var string
     */
    private $role;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var \DateTime
     */
    private $updated;

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

    public function getPassword_hash(): string {
        return $this->password_hash;
    }

    public function getCreated(): \DateTime {
        return $this->created;
    }

    public function getUpdated(): \DateTime {
        return $this->updated;
    }

    public function setPassword_hash(string $password_hash): CredentialsInterface {
        $this->password_hash = $password_hash;
        return $this;
    }

    public function setCreated(\DateTime $created): CredentialsInterface {
        $this->created = $created;
        return $this;
    }

    public function setUpdated(\DateTime $updated): CredentialsInterface {
        $this->updated = $updated;
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
