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
}
