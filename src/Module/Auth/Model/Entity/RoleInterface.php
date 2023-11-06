<?php

namespace Auth\Model\Entity;

use Model\Entity\PersistableEntityInterface;

/**
 *
 * @author vlse2610
 */
interface RoleInterface  extends PersistableEntityInterface {
   
     /**
     *
     * @return string
     */
    public function getRole(): string ;
    

    /**
     *
     * @param string $role
     * @return \Auth\Model\Entity\RoleInterface
     */
    public function setRole(string $role): RoleInterface ;
    
    
    
}
