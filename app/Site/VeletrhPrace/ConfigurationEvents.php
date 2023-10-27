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
class ConfigurationEvents {

    // site
    const EVENTS_SITE_PATH = 'veletrhprace/';

    // local
    const EVENTS_TEMPLATES_COMMON = 'local/site/common/templates/';
    const EVENTS_TEMPLATES_SITE = 'local/site/'.self::EVENTS_SITE_PATH.'templates/';
    const EVENTS_STATIC = 'local/site/'.self::EVENTS_SITE_PATH.'static/';
    // public
    const EVENTS_ASSETS = 'public/assets/';
    const EVENTS_LINKS_COMMON = 'public/site/common/';
    const EVENTS_LINKS_SITE = 'public/site/'.self::EVENTS_SITE_PATH;
    // files
    const EVENTS_FILES_PATH = '_files/'.self::EVENTS_SITE_PATH;
    const EVENTS_FILES_COMMON = '_files/common/';
    const EVENTS_FILES_SITE = '_files/'.self::EVENTS_SITE_PATH;
    const EVENTS_BOOTSTRAP_LOGS = '/_www_vp_logs/';

    // production host
    const EVENTS_BOOTSTRAP_PRODUCTION_HOST =  'replikant2871';  //vp

    
    
    
    
    }
