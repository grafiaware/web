<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Site\VeletrhPrace;

use Pes\Logger\FileLogger;

/**
 * Description of ConfigurationConsent
 *
 * @author pes2704
 */
class ConfigurationConsent {
    public static function consent() {
        return [
            'consent.logs.directory' => 'PersistentLogs/Consent',
            'consent.logs.file' => 'CookieConsent.log',
            'consent.logs.type' => FileLogger::APPEND_TO_LOG,            
        ];
        
    }
}
