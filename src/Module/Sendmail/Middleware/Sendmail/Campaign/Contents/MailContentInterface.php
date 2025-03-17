<?php
namespace Sendmail\Middleware\Sendmail\Campaign\Contents;

use Mail\Params;

/**
 *
 * @author vlse2610
 */
interface MailContentInterface {
    
    public function setAssembly($name): void;
    
    /**
     * 
     * @param type $assembly  oznaceni "sestavy" mailu
     * @param type $mailAdresata
     * @param type $jmenoAdresata
     */
    
    /**
     * 
     * @param string $mailAdresata
     * @param string $jmenoAdresata
     * @return Params
     */
    public function getParams($mailAdresata, $jmenoAdresata): Params;
     
     
}



