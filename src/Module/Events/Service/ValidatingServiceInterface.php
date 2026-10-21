<?php
namespace Events\Service;

use Psr\Http\Message\ServerRequestInterface;
use Events\Model\Entity\LoginInterface;


/**
 *
 * @author vlse2610
 */
interface ValidatingServiceInterface {
    

    /**
     * Volá se  z middleware ValidateUser.
     * Validuje uživatele.
     * Neni-li uživatel ve statusu security, tj. ještě nezvalidováno, jedná se o první request.
     * Že zvalidováno, poznamená do statusu security.   
     *     
     * @param ServerRequestInterface $request
     * @return void
     */       
    public function validateUser (ServerRequestInterface $request): void;
    
    
    
    
}
