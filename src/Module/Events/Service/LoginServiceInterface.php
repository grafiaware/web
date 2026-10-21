<?php
namespace Events\Service;

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
