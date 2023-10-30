<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Site\VeletrhPrace;

/**
 * Description of ConfigurationEvents
 *
 * @author pes2704
 */
class ConfigurationEvents extends ConfigurationConstants {

    // local
    const EVENTS_TEMPLATES_COMMON = 'local/site/common/templates/events/';
    const EVENTS_TEMPLATES_SITE = 'local/site/'.self::WEB_SITE_PATH.'templates/events/';

    /**
     * Konfigurace prezentace - vrací parametry pro ComponentController
     * @return array
     */
    public static function eventTemplates() {

        return [
                'templates' => self::EVENTS_TEMPLATES_SITE,
            ];
    }    
    
    
    
    }
