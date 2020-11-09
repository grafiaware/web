<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site;

include 'ConfigurationTz_newdb.php';
include 'ConfigurationTZ_wwwgrafia.php';
include 'ConfigurationGr_wwwgrafia.php';
//include 'ConfigurationGr2_grafiacz_20200916.php';
//include 'ConfigurationGr3_grafiacz.php';
//include 'ConfigurationGr4_grafiacz_a8.php';
//include 'ConfigurationOa_otevreneatelierycz.php';

/**
 * Description of Configuration
 *
 * @author pes2704
 */
class Configuration extends ConfigurationTz_newdb {
//class Configuration extends ConfigurationTZ_wwwgrafia {
//class Configuration extends ConfigurationGr_wwwgrafia {
//class Configuration extends ConfigurationGr2_grafiacz_20200916 {
//class Configuration extends ConfigurationGr3_grafiacz {
//class Configuration extends ConfigurationGr4_grafiacz_a8 {
//class Configuration extends ConfigurationOa_otevreneatelierycz {

    private static $cache;

    public static function getConfigModule($name) {
        if(!isset(self::$cache[$name])) {
            self::$cache[$name] = parent::$name();
        }
        return self::$cache[$name];
    }

    ### bootstrap ###
    #
    public static function bootstrap() {
        return self::getConfigModule('bootstrap');
    }

    ### kontejner ###
    #
    public static function api() {
        return self::getConfigModule('api');
    }

    public static function app() {
        return self::getConfigModule('app');
    }

    public static function build() {
        return self::getConfigModule('build');
    }

    public static function component() {
        return self::getConfigModule('component');
    }

    public static function dbOld() {
        return self::getConfigModule('dbOld');
    }

    public static function dbUpgrade() {
        return self::getConfigModule('dbUpgrade');
    }

    public static function hierarchy() {
        return self::getConfigModule('hierarchy');
    }

    public static function login() {
        return self::getConfigModule('login');
    }

    public static function rendererDefaults() {
        return self::getConfigModule('rendererDefaults');
    }

    public static function renderer() {
        return self::getConfigModule('renderer');
    }

    public static function web() {
        return self::getConfigModule('web');
    }

    public static function rs() {
        return self::getConfigModule('rs');
    }

    ### presentation ###
    #
    public static function statusPresentationManager() {
        return self::getConfigModule('statusPresentationManager');
    }

    public static function layoutControler() {
        return self::getConfigModule('layoutControler');
    }

    public static function pageControler() {
        return self::getConfigModule('pageControler');
    }

    public static function languageSelectRenderer() {
        return self::getConfigModule('languageSelectRenderer');
    }

    public static function transformator() {
        return self::getConfigModule('transformator');
    }

}
