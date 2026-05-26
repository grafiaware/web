<?php
namespace Events\Service;

use Psr\Http\Message\ServerRequestInterface;

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
    
    
}
