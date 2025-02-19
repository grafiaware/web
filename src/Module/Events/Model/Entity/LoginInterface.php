<?php

namespace Events\Model\Entity;

use Model\Entity\PersistableEntityInterface;
use Events\Model\Entity\LoginInterface;


/**
 *
 * @author pes2704
 */
interface LoginInterface extends PersistableEntityInterface {
   
    /**
     *
     * @return string
     */
    public function getLoginName();
      
     /**
     *
     * @param string $loginName
     * @return LoginInterface  $this
     */
    public function setLoginName(string $loginName): LoginInterface;
    
    
    
    /**
     *
     * @return string
     */
    public function getModule();
    
    /**
     *
     * @param string $module
     * @return LoginInterface  $this
     */
    public function setModule(string $module): LoginInterface ;
  
   
    /**
     *
     * @return string|null
     */
    public function getRole(): ?string ;
    
    /**
     *
     * @param string $role
     * @return LoginInterface  $this
     */
    public function setRole(string $role=null): LoginInterface ;        
       
    
    /**
     *
     * @return string|null
     */
    public function getEmail(): ?string ;
    
    /**
     * 
     * @param string $email
     * @return LoginInterface  $this
     */
    public function setEmail(string $email = null):LoginInterface ;      
    
        
    /**
     * 
     * @return string|null
     */
     public function getInfo(): ?string;    
    /**
     * 
     * @param string $info
     * @return LoginInterface  $this
     */
    public function setInfo(string $info=null): LoginInterface ;      
    
    
    /**
     * 
     * @return string
     */
     public function getUrl(): string ;    
    /**
     * 
     * @param string $url
     * @return LoginInterface  $this
     */
    public function setUrl(string $url): LoginInterface ;    
     
    
    /**
     *
     * @return \DateTime
     */
    public function getCreated(): \DateTime ;
     /**
     *
     * @param \DateTime $created
     * @return LoginInterface
     */
    public function setCreated(\DateTime $created): LoginInterface ;
   

    /**
     *
     * @return \DateTime
     */
    public function getUpdated(): \DateTime ;
    /**
     *
     * @param \DateTime $updated
     * @return LoginInterface
     */
    public function setUpdated(\DateTime $updated): LoginInterface ;
      
    
    
    
    
    
    
}
