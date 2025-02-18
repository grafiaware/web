<?php

namespace Events\Model\Entity;

use Model\Entity\PersistableEntityAbstract;
use Events\Model\Entity\LoginInterface;

/**
 * Description of Login
 *
 * @author pes2704
 */
class Login extends PersistableEntityAbstract implements LoginInterface {

    /**
     * @var string
     */
    private $loginName; //NOT NULL    
     /**
     * @var string
     */
    private $modul; //NOT NULL
    
     /**
     * @var string
     */
    private $role; 
     /**
     * @var string
     */
    private $email;
     /**
     * @var string
     */
    private $info;
    


    /**
     *
     * @return string
     */
    public function getLoginName() {
        return $this->loginName;
    }
    /**
     *
     * @param string $loginName
     * @return LoginInterface  $this
     */
    public function setLoginName( string $loginName): LoginInterface {
        $this->loginName = $loginName;
        return $this;
    }
    
    /**
     *
     * @return string
     */
    public function getModul() {
        return $this->modul;
    }
    /**
     *
     * @param string $modul
     * @return LoginInterface  $this
     */
    public function setModul(string $modul): LoginInterface {
        $this->modul = $modul;
        return $this;
    }
    
    
    
    
    /**
     *
     * @return string|null
     */
    public function getRole(): ?string {
        return $this->role;
    }
    /**
     *
     * @param string $role
     * @return LoginInterface  $this
     */
    public function setRole(string $role=null): LoginInterface {
        $this->role = $role;
        return $this;
    }
       
    /**
     *
     * @return string|null
     */
    public function getEmail(): ?string {
        return $this->email;
    }
    /**
     * 
     * @param string $email
     * @return LoginInterface  $this
     */
    public function setEmail(string $email = null):LoginInterface {
        $this->email = $email;
        return $this;
    }
    
        
    /**
     * 
     * @return string|null
     */
     public function getInfo(): ?string {
        return $this->info;
    }
    /**
     * 
     * @param string $info
     * @return LoginInterface  $this
     */
    public function setInfo(string $info=null): LoginInterface {
        $this->info = $info;
        return $this;
    }
           
}
