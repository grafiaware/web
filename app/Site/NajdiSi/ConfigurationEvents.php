<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Site\NajdiSi;

/**
 * Description of ConfigurationEvents
 *
 * @author pes2704
 */
class ConfigurationEvents extends ConfigurationConstants {

    // local
    const EVENTS_TEMPLATES_COMMON = 'local/site/common/templates/events/';
    const EVENTS_TEMPLATES_SITE = 'local/site/'.self::WEB_SITE.'templates/events/';

    /**
     * Konfigurace prezentace - vrací parametry pro ComponentControler
     * @return array
     */
    public static function eventTemplates() {

        return [
                'templates' => self::EVENTS_TEMPLATES_SITE,
            ];
    }    
    

    /**
     * Konfigurace upload files - vrací parametry pro FilesUploadControler a VisitorProfileControler
     * @return array
     */
    public static function eventsUploads() {

        return [
            'upload.events.visitor' => PES_RUNNING_ON_PRODUCTION_HOST ? self::WEB_FILES_SITE.'upload/events/visitor' : self::WEB_FILES_SITE.'upload/events/visitor',
            'upload.events.acceptedextensions' => [".doc", ".docx", ".dot", ".odt", "pages", ".xls", ".xlsx", ".ods", ".txt", ".pdf"],
            ];
    }    
    
}
