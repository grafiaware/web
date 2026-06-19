<?php
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
     * VolÃ¡ se  z middleware ValidateUser.
     * Validuje uÅ¾ivatele.
     * Neni-li uÅ¾ivatel ve statusu security, tj. jeÅ¡tÄ nezvalidovÃ¡no, jednÃ¡ se o prvnÃ­ request.
     * Å½e zvalidovÃ¡no, poznamenÃ¡ do statusu security.   
     *     
     * @param ServerRequestInterface $request
     * @return void
     */       
    public function validateUser (ServerRequestInterface $request): void;
    
    
    
    
}
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
