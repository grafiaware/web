<?php

namespace Events\Model\Entity;

use Pes\Model\Entity\PersistableEntityInterface;


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
