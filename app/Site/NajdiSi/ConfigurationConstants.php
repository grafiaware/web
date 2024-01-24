<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site\NajdiSi;

/**
 * Description of Configuration
 *
 * @author pes2704
 */
class ConfigurationConstants {

    // site
    const WEB_SITE = 'najdisi/';

    // local
    const WEB_TEMPLATES_COMMON = 'local/site/common/templates/';
    const WEB_TEMPLATES_SITE = 'local/site/'.self::WEB_SITE.'templates/';
    const WEB_STATIC = 'local/site/'.self::WEB_SITE.'static/';
    // public
    const WEB_ASSETS = 'public/assets/';
    const WEB_LINKS_COMMON = 'public/site/common/';
    const WEB_LINKS_SITE = 'public/site/'.self::WEB_SITE;
    // files
    const WEB_FILES_COMMON = '_files/common/';
    const WEB_FILES_SITE = '_files/'.self::WEB_SITE;
//    const WEB_BOOTSTRAP_LOGS = PES_RUNNING_ON_PRODUCTION_HOST ? '/' : '/_www_na_logs/';
    const WEB_BOOTSTRAP_LOGS = '_www_na_logs/';

    // production host
    const WEB_BOOTSTRAP_PRODUCTION_HOST =  'replikant2871'; 
}
