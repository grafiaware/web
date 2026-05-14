<?php

namespace Events\Model\Entity;

use Model\Entity\PersistableEntityInterface;
use Events\Model\Entity\LoginInterface;
use DateTime;

/**
 *
 * @author pes2704
 */
interface LoginInterface extends PersistableEntityInterface {
   
    /**
     *
     * @return string
     */
    public function getLoginName() : string ;
               
    /**
     *
     * @return DateTime
     */
    public function getCreated(): DateTime;    
       
    /**
     *
     * @return DateTime|null
     */
    public function getUpdated(): ?DateTime;
      
    /**
     *
     * @return string
     */
    public function getDeletedDueToAuth(): string ;
    
            
    
    
     /**
     *
     * @param string $loginName
     * @return LoginInterface  $this
     */
    public function setLoginName(string $loginName): LoginInterface;
        
     /**
     *
     * @param DateTime $created
     * @return  LoginInterface $this
     */
    public function setCreated(DateTime $created) : LoginInterface ;
    
    /**
      *
      * @param DateTime $updated 
      * @return  LoginInterface $this
      */
    public function setUpdated(?DateTime $updated) : LoginInterface;    
    
    /**
     *
     * @param DateTime $deletedDueToAuth
     * @return  LoginInterface $this
     */
    public function setDeletedDueToAuth($deletedDueToAuth): LoginInterface;
    
    
    
    
    
    
    
    
    
    
    
}
