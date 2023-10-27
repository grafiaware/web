<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site\OtevreneAteliery;

/**
 * Description of Configuration
 *
 * @author pes2704
 */
class ConfigurationConstants {

    // site
    const WEB_SITE_PATH = 'otevreneateliery/';

    // local
    const WEB_TEMPLATES_COMMON = 'local/site/common/templates/';
    const WEB_TEMPLATES_SITE = 'local/site/'.self::WEB_SITE_PATH.'templates/';
    const WEB_STATIC = 'local/site/'.self::WEB_SITE_PATH.'static/';
    // public
    const WEB_ASSETS = 'public/assets/';
    const WEB_LINKS_COMMON = 'public/site/common/';
    const WEB_LINKS_SITE = 'public/site/'.self::WEB_SITE_PATH;
    // files
    const WEB_FILES_PATH = '_files/'.self::WEB_SITE_PATH;
    const WEB_FILES_COMMON = '_files/common/';
    const WEB_FILES_SITE = '_files/'.self::WEB_SITE_PATH;
    const WEB_BOOTSTRAP_LOGS = '/_www_oa_logs/';

    // production host
    const WEB_BOOTSTRAP_PRODUCTION_HOST =  'WEB_BOOTSTRAP_PRODUCTION_HOST_doplnit';  //vp
}
