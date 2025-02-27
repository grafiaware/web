<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPInterface.php to edit this template
 */

namespace Sendmail\Middleware\Sendmail\Controler\Contents;

/**
 *
 * @author vlse2610
 */
interface MailContentInterface {
    
    /**
     * 
     * @param type $assembly  oznaceni "sestavy" mailu
     * @param type $mailAdresata
     * @param type $jmenoAdresata
     */
     public function getParams($assembly, $mailAdresata, $jmenoAdresata)  ;
     
     
}



