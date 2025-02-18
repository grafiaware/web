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
    public function getModul();
    
    /**
     *
     * @param string $modul
     * @return LoginInterface  $this
     */
    public function setModul(string $modul): LoginInterface ;
  
   
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
    
    
    
    
}
