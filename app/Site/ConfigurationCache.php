<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site;

#### ZDE PONECH ODKOMENROVANOU JEN JEDNU DVOJICI #############################
#
use Site\G2 as Siteconfig;
const SITE_PATH = 'app/Site/G2/';

//use Site\Grafia as Siteconfig;
//const SITE_PATH = 'app/Site/Grafia/';

//use Site\OtevreneAteliery as Siteconfig;
//const SITE_PATH = 'app/Site/OtevreneAteliery/';

//use Site\TydenZdravi as Siteconfig;
//const SITE_PATH = 'app/Site/TydenZdravi/';

//use Site\VeletrhPrace as Siteconfig;
//const SITE_PATH = 'app/Site/VeletrhPrace/';
#
###########################################################################


include PROJECT_PATH.SITE_PATH.'ConfigurationConstants.php';
include PROJECT_PATH.SITE_PATH.'ConfigurationDb.php';
include PROJECT_PATH.SITE_PATH.'ConfigurationRed.php';
include PROJECT_PATH.SITE_PATH.'ConfigurationStyles.php';


/**
 * Cache konfigurace
 *
 * změny při změně site:
 * - local/site/site-definitions.less - odkomentovat a zakomentovat definici site-definitions @sitename
 *
 * @author pes2704
 */
class ConfigurationCache {

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
                case 'webComponent':
                    self::$cache[$name] = Siteconfig\ConfigurationRed::webComponent();
                    break;
                case 'redComponent':
                    self::$cache[$name] = Siteconfig\ConfigurationRed::redComponent();
                    break;
                case 'presentationStatus':
                    self::$cache[$name] = Siteconfig\ConfigurationRed::presentationStatus();
                    break;
                case 'layoutController':
                    self::$cache[$name] = Siteconfig\ConfigurationRed::layoutController();
                    break;
                case 'menu':
                    self::$cache[$name] = Siteconfig\ConfigurationRed::menu();
                    break;
                case 'itemActionControler':
                    self::$cache[$name] = Siteconfig\ConfigurationRed::itemActionControler();
                    break;
                case 'loginLogoutController':
                    self::$cache[$name] = Siteconfig\ConfigurationRed::loginLogoutController();
                    break;
                case 'componentController':
                    self::$cache[$name] = Siteconfig\ConfigurationRed::componentController();
                    break;
                case 'templates':
                    self::$cache[$name] = Siteconfig\ConfigurationRed::templates();
                    break;
                case 'filesUploadController':
                    self::$cache[$name] = Siteconfig\ConfigurationRed::filesUploadController();
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

    public static function webComponent() {
        return self::getConfigModule('webComponent');
    }

    public static function redComponent() {
        return self::getConfigModule('redComponent');
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
    public static function presentationStatus() {
        return self::getConfigModule('presentationStatus');
    }

    public static function layoutController() {
        return self::getConfigModule('layoutController');
    }

    public static function menu() {
        return self::getConfigModule('menu');
    }

    public static function itemActionControler() {
        return self::getConfigModule('itemActionControler');
    }
    
    public static function loginLogoutController() {
        return self::getConfigModule('loginLogoutController');
    }

    public static function componentController() {
        return self::getConfigModule('componentController');
    }

    public static function templates() {
        return self::getConfigModule('templates');
    }

    public static function filesUploadController() {
        return self::getConfigModule('filesUploadController');
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
