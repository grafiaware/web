<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site\TydenZdravi;

/**
 * Description of Configuration
 *
 * @author pes2704
 */
class ConfigurationConstants {

    // site
    const RED_SITE_PATH = 'tydenzdravi/';

    // local
    const RED_TEMPLATES_COMMON = 'local/site/common/templates/';
    const RED_TEMPLATES_SITE = 'local/site/'.self::RED_SITE_PATH.'templates/';
    // public
    const RED_ASSETS = 'public/assets/';
    const RED_LINKS_COMMON = 'public/site/common/';
    const RED_LINKS_SITE = 'public/site/'.self::RED_SITE_PATH;
    // files
    const RED_FILES = '_www_tz_files/';
    const RED_BOOTSTRAP_LOGS = '/_www_tz_logs/';

}
