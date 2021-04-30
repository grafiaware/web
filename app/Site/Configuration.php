<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site;

use Site\Grafia as Siteconfig;
include 'app/Site/Grafia/ConfigurationConstants.php';
include 'app/Site/Grafia/ConfigurationDb.php';
include 'app/Site/Grafia/ConfigurationRed.php';
include 'app/Site/Grafia/ConfigurationStyles.php';

//use Site\TydenZdravi as Siteconfig;
//include 'app/Site/TydenZdravi/ConfigurationConstants.php';
//include 'app/Site/TydenZdravi/ConfigurationDb.php';
//include 'app/Site/TydenZdravi/ConfigurationRed.php';
//include 'app/Site/TydenZdravi/ConfigurationStyles.php';

//use Site\VeletrhPrace as Siteconfig;
//include 'app/Site/VeletrhPrace/ConfigurationConstants.php';
//include 'app/Site/VeletrhPrace/ConfigurationDb.php';
//include 'app/Site/VeletrhPrace/ConfigurationRed.php';
//include 'app/Site/VeletrhPrace/ConfigurationStyles.php';

//use Site\Grafia as Siteconfig;
//use Site\Grafia as Siteconfig;
/**
 * Cache konfigurace
 *
 * změny při změně site:
 * - local/site/site-definitions.less - odkomentovat a zakomentovat definici site-definitions @sitename
 *
 * @author pes2704
 */
class Configuration {

    private static $cache;

    public static function getConfigModule($name) {
        if(!isset(self::$cache[$name])) {
            switch ($name) {
                ###############################
                # configutation red
                #
                case 'bootstrap':
                    self::$cache[$name] = Siteconfig\ConfigurationRed::bootstrap();
                    break;
                case 'app':
                    self::$cache[$name] = Siteconfig\ConfigurationRed::app();
                    break;
                case 'component':
                    self::$cache[$name] = Siteconfig\ConfigurationRed::component();
                    break;
                case 'statusPresentationManager':
                    self::$cache[$name] = Siteconfig\ConfigurationRed::statusPresentationManager();
                    break;
                case 'layoutControler':
                    self::$cache[$name] = Siteconfig\ConfigurationRed::layoutControler();
                    break;
                case 'pageControler':
                    self::$cache[$name] = Siteconfig\ConfigurationRed::pageControler();
                    break;
                case 'loginLogoutControler':
                    self::$cache[$name] = Siteconfig\ConfigurationRed::loginLogoutControler();
                    break;
                case 'componentControler':
                    self::$cache[$name] = Siteconfig\ConfigurationRed::componentControler();
                    break;
                case 'templateControler':
                    self::$cache[$name] = Siteconfig\ConfigurationRed::templateControler();
                    break;
                case 'filesUploadControler':
                    self::$cache[$name] = Siteconfig\ConfigurationRed::filesUploadControler();
                    break;
                case 'transformator':
                    self::$cache[$name] = Siteconfig\ConfigurationRed::transformator();
                    break;
                case 'mail':
                    self::$cache[$name] = Siteconfig\ConfigurationRed::mail();
                    break;
                case 'files':
                    self::$cache[$name] = Siteconfig\ConfigurationRed::files();
                    break;
                ###############################
                # configutation db
                #
                case 'api':
                    self::$cache[$name] = Siteconfig\ConfigurationDb::api();
                    break;
                case 'build':
                    self::$cache[$name] = Siteconfig\ConfigurationDb::build();
                    break;
                case 'dbOld':
                    self::$cache[$name] = Siteconfig\ConfigurationDb::dbOld();
                    break;
                case 'dbUpgrade':
                    self::$cache[$name] = Siteconfig\ConfigurationDb::dbUpgrade();
                    break;
                case 'hierarchy':
                    self::$cache[$name] = Siteconfig\ConfigurationDb::hierarchy();
                    break;
                case 'login':
                    self::$cache[$name] = Siteconfig\ConfigurationDb::login();
                    break;
                case 'web':
                    self::$cache[$name] = Siteconfig\ConfigurationDb::web();
                    break;
                case 'rs':
                    self::$cache[$name] = Siteconfig\ConfigurationDb::rs();
                    break;

                ###############################
                # configutation styles
                #
                case 'rendererDefaults':
                    self::$cache[$name] = Siteconfig\ConfigurationStyles::rendererDefaults();
                    break;
                case 'renderer':
                    self::$cache[$name] = Siteconfig\ConfigurationStyles::renderer();
                    break;
                case 'languageSelectRenderer':
                    self::$cache[$name] = Siteconfig\ConfigurationStyles::languageSelectRenderer();
                    break;
            }
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

    public static function loginLogoutControler() {
        return self::getConfigModule('loginLogoutControler');
    }

    public static function componentControler() {
        return self::getConfigModule('componentControler');
    }

    public static function templateControler() {
        return self::getConfigModule('templateControler');
    }

    public static function filesUploadControler() {
        return self::getConfigModule('filesUploadControler');
    }

    public static function languageSelectRenderer() {
        return self::getConfigModule('languageSelectRenderer');
    }

    public static function transformator() {
        return self::getConfigModule('transformator');
    }

    public static function mail() {
        return self::getConfigModule('mail');
    }

    public static function files() {
        return self::getConfigModule('files');
    }

}
