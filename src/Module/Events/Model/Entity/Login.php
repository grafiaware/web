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
     * @var string
     */
    private $module; //NOT NULL    
     /**
     * @var string
     */
    private $url;  //NOT NULL   
    
    /**
     * @var \DateTime
     */
    private $created;    //NOT NULL
    /**
     * @var \DateTime
     */
    private $updated;    //NOT NULL
       
    
    


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
    public function getModule() {
        return $this->module;
    }
    /**
     *
     * @param string $module
     * @return LoginInterface  $this
     */
    public function setModule(string $module): LoginInterface {
        $this->module = $module;
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
    
    
     /**
     * 
     * @return string
     */
     public function getUrl(): string {
        return $this->url;
    }
    /**
     * 
     * @param string $url
     * @return LoginInterface  $this
     */
    public function setUrl(string $url): LoginInterface {
        $this->url = $url;
        return $this;
    }
           
    
    
    /**
     *
     * @return \DateTime
     */
    public function getCreated(): \DateTime {
        return $this->created;
    }
    /**
     *
     * @param \DateTime $created
     * @return LoginInterface
     */
    public function setCreated(\DateTime $created): LoginInterface {
        $this->created = $created;
        return $this;
    }
    
    
    /**
     *
     * @return \DateTime
     */
    public function getUpdated(): \DateTime {
        return $this->updated;
    }
    /**
     *
     * @param \DateTime $updated
     * @return LoginInterface
     */
    public function setUpdated(\DateTime $updated): LoginInterface {
        $this->updated = $updated;
        return $this;
    }
    
    
    
    
    
}
