<?php

namespace Events\Service;

use Events\Model\Entity\LoginInterface;

/**
 *
 * @author vlse2610
 */
interface LoginServiceInterface {
    
    /**
     * Volá se  z middleware Events... SynchroControler, a  z ValidateService     
     * 
     * @param LoginInterface $login
     * @return void
     */
    public function setDeleteUserNameFromEventsLogin(LoginInterface $login): void;
    
    
    /**
     * Volá se  z middleware Events... SynchroControler, a  z ValidateService     *      
     * 
     * @param LoginInterface $login
     * @return void
     */
    public function setAddUserNameToEventsLogin(LoginInterface $login /*string $loginName*/): void; 
    
    
    
}
