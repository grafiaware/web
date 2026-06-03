<?php
namespace Events\Service;

use Psr\Http\Message\ServerRequestInterface;
use Events\Model\Entity\LoginInterface;

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPInterface.php to edit this template
 */

/**
 *
 * @author vlse2610
 */
interface ValidateServiceInterface {
    

    /**
     * Volá se  z middleware ValidateUser
     * 
     * @param ServerRequestInterface $request
     * @return void
     */
    public function validateUser (ServerRequestInterface $request): void;
    
    
    /**
     * Volá se  z middleware Events... SynchroControler, a interne z ValidateService
     * 
     * @param type $loginName
     * @return void
     */
    public function deleteUserNameFromEvents(LoginInterface $login): void;
    
    /**
     * Volá se  z middleware Events... SynchroControler, a interne z ValidateService     * 
     * 
     * @param type $loginName
     * @return void
     */
    public function addUserNameToEvents(string $loginName): void; 
    
    
}