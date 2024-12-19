<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Site\NajdiSi;

use Pes\Database\Handler\DbTypeEnum;

/**
 * Description of ConfigurationEvents
 *
 * @author pes2704
 */
class ConfigurationEvents extends ConfigurationConstants {

    // local
    const EVENTS_TEMPLATES_COMMON = 'local/site/common/templates/events/';
    const EVENTS_TEMPLATES_SITE = 'local/site/'.self::WEB_SITE.'templates/events/';

    public static function dbEvents() {

        return [
            #####################################
            # Konfigurace připojení k databázi Events
            #
            # Konfigurována jsou dvě připojení k databázi - jedno pro vývoj a druhé pro běh na produkčním stroji
            #
            'dbEvents.db.type' => DbTypeEnum::MySQL,
            'dbEvents.db.port' => '3306',
            'dbEvents.db.charset' => 'utf8',
            'dbEvents.db.collation' => 'utf8_general_ci',
            'dbEvents.db.connection.host' => PES_RUNNING_ON_PRODUCTION_HOST ? '127.0.0.1' : 'xenon.intranet.grafia.cz',
            'dbEvents.db.connection.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'xxxxxxxxxxx' : 'events',
            #
            ###################################
            # Konfigurace logu databáze
            #
            'dbEvents.logs.db.directory' => 'Logs/Events',
            'dbEvents.logs.db.file' => 'Database.log',
            'dbEvents.logs.db.loginsync' => 'LoginSync.log',
            #
            #################################
            ];
    }  
    
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
            'upload.events.acceptedextensions' => [".doc", ".docx", ".dot", ".odt", ".pages", ".xls", ".xlsx", ".ods", ".txt", ".pdf"],
            ];
    }    
    
}
