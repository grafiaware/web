<?php
<?php

namespace Events\Service;

use Events\Model\Entity\LoginInterface;

/**
 *
 * @author vlse2610
 */
interface LoginServiceInterface {
    
    /**
     * VolÃ¡ se  z middleware Events... SynchroControler, a  z ValidateService     
     *      
     * ZavolÃ¡no v pÅÃ­padÄ, Å¾e nÃ¡zev prihlaÅ¡enÃ©ho $validatedUserName NENÃ validni (nenÃ­ v single-login.login tabulce).
     * "VymaÅ¾e" nÃ¡zev z tabulky events.login, byl-li tam.
     * 
     * @param string $loginName
     * @return void
     */
    public function setDeleteUserNameFromEventsLogin(string $loginName): void;
    
    
    /**
     * VolÃ¡ se  z middleware Events... SynchroControler, a  z ValidateService 
     *     
     * ZavolÃ¡no v pÅÃ­padÄ, Å¾e nÃ¡zev prihlaÅ¡enÃ©ho $userName JE validni (je v single-login.login tabulce).
     * NenÃ­-li v events.login tabulce, zapiÅ¡e do events.login tabulky.
     *      
     * @param string $loginName
     * @return void
     */
    public function setAddUserNameToEventsLogin (string $loginName): void; 
    
    
    
}

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
     * Zavoláno v případě, že název prihlašeného $validatedUserName NENÍ validni (není v single-login.login tabulce).
     * "Vymaže" název z tabulky events.login, byl-li tam.
     * 
     * @param string $loginName
     * @return void
     */
    public function setDeleteUserNameFromEventsLogin(string $loginName): void;
    
    
    /**
     * Volá se  z middleware Events... SynchroControler, a  z ValidateService 
     *     
     * Zavoláno v případě, že název prihlašeného $userName JE validni (je v single-login.login tabulce).
     * Není-li v events.login tabulce, zapiše do events.login tabulky.
     *      
     * @param string $loginName
     * @return void
     */
    public function setAddUserNameToEventsLogin (string $loginName): void; 
    
    
    
}
