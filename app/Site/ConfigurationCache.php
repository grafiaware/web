<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site;

#### ZDE PONECH ODKOMENROVANOU JEN JEDNU DVOJICI #############################
#

//use Site\Grafia as Siteconfig;
//const SITE_PATH = 'app/Site/Grafia/';

use Site\NajdiSi as Siteconfig;
const SITE_PATH = 'app/Site/NajdiSi/';

//use Site\OtevreneAteliery as Siteconfig;
//const SITE_PATH = 'app/Site/OtevreneAteliery/';

//use Site\TydenZdravi as Siteconfig;
//const SITE_PATH = 'app/Site/TydenZdravi/';

//use Site\VeletrhPrace as Siteconfig;
//const SITE_PATH = 'app/Site/VeletrhPrace/';

#
###########################################################################

// konfigurace bootstrap se používá před nastavením autoloaderu
include SITE_PATH.'ConfigurationConstants.php';
include SITE_PATH.'ConfigurationBootstrap.php';


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
                    self::$cache[$name] = Siteconfig\ConfigurationBootstrap::bootstrap();
                    break;
                case 'app':
                    self::$cache[$name] = Siteconfig\ConfigurationWeb::app();
                    break;
                case 'webComponent':
                    self::$cache[$name] = Siteconfig\ConfigurationWeb::webComponent();
                    break;
                case 'commonTemplates':
                    self::$cache[$name] = Siteconfig\ConfigurationWeb::commonTemplates();
                    break;                
                case 'presentationStatus':
                    self::$cache[$name] = Siteconfig\ConfigurationWeb::presentationStatus();
                    break;
                case 'layoutControler':
                    self::$cache[$name] = Siteconfig\ConfigurationWeb::layoutControler();
                    break;
                case 'menu':
                    self::$cache[$name] = Siteconfig\ConfigurationWeb::menu();
                    break;
                case 'itemActionControler':
                    self::$cache[$name] = Siteconfig\ConfigurationRed::itemActionControler();
                    break;
                case 'auth':
                    self::$cache[$name] = Siteconfig\ConfigurationAuth::auth();
                    break;
                case 'componentControler':
                    self::$cache[$name] = Siteconfig\ConfigurationRed::componentControler();
                    break;
                case 'redTemplates':
                    self::$cache[$name] = Siteconfig\ConfigurationRed::redTemplates();
                    break;
                case 'redUpload':
                    self::$cache[$name] = Siteconfig\ConfigurationRed::redUploads();
                    break;
                case 'transformator':
                    self::$cache[$name] = Siteconfig\ConfigurationWeb::transformator();
                    break;
                case 'mail':
                    self::$cache[$name] = Siteconfig\ConfigurationWeb::mail();
                    break;
                case 'files':
                    self::$cache[$name] = Siteconfig\ConfigurationWeb::files();
                    break;
                case 'eventsUploads':
                    self::$cache[$name] = Siteconfig\ConfigurationEvents::eventsUploads();
                    break;  
                case 'eventTemplates':
                    self::$cache[$name] = Siteconfig\ConfigurationEvents::eventTemplates();
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
                case 'sqlite':
                    self::$cache[$name] = Siteconfig\ConfigurationDb::sqlite();
                    break;
                case 'web':
                    self::$cache[$name] = Siteconfig\ConfigurationDb::web();
                    break;
                case 'rs':
                    self::$cache[$name] = Siteconfig\ConfigurationDb::rs();
                    break;
                case 'dbEvents':
                    self::$cache[$name] = Siteconfig\ConfigurationEvents::dbEvents();
                    break;
                ###############################
                # configutation consent
                #
                case 'consent':
                    self::$cache[$name] = Siteconfig\ConfigurationConsent::consent();
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

    public static function sqlite() {
        return self::getConfigModule('sqlite');
    }

    public static function web() {
        return self::getConfigModule('web');
    }

    public static function rs() {
        return self::getConfigModule('rs');
    }

    public static function dbEvents() {
        return self::getConfigModule('dbEvents');
    }
    
    ### presentation ###
    #
    public static function commonTemplates() {
        return self::getConfigModule('commonTemplates');
    }
    public static function presentationStatus() {
        return self::getConfigModule('presentationStatus');
    }
    public static function layoutControler() {
        return self::getConfigModule('layoutControler');
    }

    public static function menu() {
        return self::getConfigModule('menu');
    }

    public static function itemActionControler() {
        return self::getConfigModule('itemActionControler');
    }
    
    public static function auth() {
        return self::getConfigModule('auth');
    }

    public static function componentControler() {
        return self::getConfigModule('componentControler');
    }

    public static function redTemplates() {
        return self::getConfigModule('redTemplates');
    }

    public static function redUploads() {
        return self::getConfigModule('redUpload');
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

    public static function eventsUploads() {
        return self::getConfigModule('eventsUploads');
    }
    
    public static function eventTemplates() {
        return self::getConfigModule('eventTemplates');
    }
    
    
    
    ### consent ###
    #
        public static function consent() {
        return self::getConfigModule('consent');
    }    
}
