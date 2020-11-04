<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site;

include 'ConfigurationGr_wwwgrafia.php';
include 'ConfigurationGr2_grafiacz_20200916.php';
include 'ConfigurationGr3_grafiacz.php';
include 'ConfigurationGr4_grafiacz_a8.php';
include 'ConfigurationOa_otevreneatelierycz.php';

/**
 * Description of Configuration
 *
 * @author pes2704
 */
class Configuration extends ConfigurationGr_wwwgrafia {
//class Configuration extends ConfigurationGr2_grafiacz_20200916 {
//class Configuration extends ConfigurationGr3_grafiacz {
//class Configuration extends ConfigurationGr4_grafiacz_a8 {
//class Configuration extends ConfigurationOa_otevreneatelierycz {

    private static $cache;

    ### bootstrap ###
    #
    public static function bootstrap() {
        if(!isset(self::$cache['bootstrap'])) {
            self::$cache['bootstrap'] = parent::bootstrap();
        }
        return self::$cache['bootstrap'];
    }

    ### kontejner ###
    #
    public static function api() {
        if(!isset(self::$cache['api'])) {
            self::$cache['api'] = parent::api();
        }
        return self::$cache['api'];
    }

    public static function app() {
        if(!isset(self::$cache['app'])) {
            self::$cache['app'] = parent::app();
        }
        return self::$cache['app'];
    }

    public static function build() {
        if(!isset(self::$cache['build'])) {
            self::$cache['build'] = parent::build();
        }
        return self::$cache['build'];
    }

    public static function component() {
        if(!isset(self::$cache['component'])) {
            self::$cache['component'] = parent::component();
        }
        return self::$cache['component'];
    }

    public static function dbOld() {
        if(!isset(self::$cache['dbOld'])) {
            self::$cache['dbOld'] = parent::dbOld();
        }
        return self::$cache['dbOld'];
    }

    public static function dbUpgrade() {
        if(!isset(self::$cache['dbUpgrade'])) {
            self::$cache['dbUpgrade'] = parent::dbUpgrade();
        }
        return self::$cache['dbUpgrade'];
    }

    public static function hierarchy() {
        if(!isset(self::$cache['hierarchy'])) {
            self::$cache['hierarchy'] = parent::hierarchy();
        }
        return self::$cache['hierarchy'];
    }

    public static function login() {
        if(!isset(self::$cache['login'])) {
            self::$cache['login'] = parent::login();
        }
        return self::$cache['login'];
    }

    public static function rendererDefaults() {
        if(!isset(self::$cache['rendererDefaults'])) {
            self::$cache['rendererDefaults'] = parent::rendererDefaults();
        }
        return self::$cache['rendererDefaults'];
    }

    public static function renderer() {
        if(!isset(self::$cache['renderer'])) {
            self::$cache['renderer'] = parent::renderer();
        }
        return self::$cache['renderer'];
    }

    public static function web() {
        if(!isset(self::$cache['web'])) {
            self::$cache['web'] = parent::web();
        }
        return self::$cache['web'];
    }

    public static function rs() {
        if(!isset(self::$cache['rs'])) {
            self::$cache['rs'] = parent::rs();
        }
        return self::$cache['rs'];
    }

    ### presentation ###
    #
    public static function statusPresentationManager() {
        if(!isset(self::$cache['statusPresentationManager'])) {
            self::$cache['statusPresentationManager'] = parent::statusPresentationManager();
        }
        return self::$cache['statusPresentationManager'];
    }

    public static function layoutControler() {
        if(!isset(self::$cache['layoutControler'])) {
            self::$cache['layoutControler'] = parent::layoutControler();
        }
        return self::$cache['layoutControler'];
    }

    public static function pageControler() {
        if(!isset(self::$cache['pageControler'])) {
            self::$cache['pageControler'] = parent::pageControler();
        }
        return self::$cache['pageControler'];
    }

    public static function languageSelectRenderer() {
        if(!isset(self::$cache['languageSelectRenderer'])) {
            self::$cache['languageSelectRenderer'] = parent::languageSelectRenderer();
        }
        return self::$cache['languageSelectRenderer'];
    }

    public static function transformator() {
        if(!isset(self::$cache['transformator'])) {
            self::$cache['transformator'] = parent::transformator();
        }
        return self::$cache['transformator'];
    }

}
