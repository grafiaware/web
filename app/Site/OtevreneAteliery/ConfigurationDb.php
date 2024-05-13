<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site\OtevreneAteliery;

use Pes\Database\Handler\DbTypeEnum;
use Pes\Logger\FileLogger;

/**
 * Description of Configuration
 *
 * @author pes2704
 */
class ConfigurationDb {

    ### kontejner ###
    #

    /**
     * Konfigurace kontejneru - vrací parametry pro ApiContainerConfigurator
     * @return array
     */
    public static function api() {
        return [
            #################################
            # Sekce konfigurace účtů databáze pro api kontejner
            # - konfigurováni dva uživatelé - jeden pro vývoj a druhý pro běh na produkčním stroji
            #
            # Konfigurace připojení k databázi je v delegate kontejneru.
            # Konfigurace připojení k databázi může být v aplikačním kontejneru nebo různá v jednotlivých middleware kontejnerech.
            #
            'red.db.everyone.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'revoluceorg003' : 'oa_everyone',
            'red.db.everyone.password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'oa_databaze_heslo_nastavit' : 'oa_everyone',
            'red.db.authenticated.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'revoluceorg003' : 'oa_auth',
            'red.db.authenticated.password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'oa_databaze_heslo_nastavit' : 'oa_auth',
            'red.db.administrator.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'revoluceorg003' : 'oa_admin',
            'red.db.administrator.password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'oa_databaze_heslo_nastavit' : 'oa_admin',
            #
            ###################################
            # Konfigurace logu databáze
            #
            'red.logs.view.directory' => 'Logs/Red',
            'red.logs.view.file' => 'Render.log',
            'red.logs.view.type' => FileLogger::FILE_PER_DAY
            #
            ###################################
        ];
    }

    /**
     * Konfigurace kontejneru - vrací parametry pro BuildContainerConfigurator
     * @return array
     */
    public static function build() {
        return [
            #################################
            # Sekce konfigurace uživatele databáze
            # - konfigurováni dva uživatelé - jeden pro vývoj a druhý pro běh na produkčním stroji
            # - uživatelé musí mít
            #      - práva drop a create database
            #      - crud práva + grant option k nové (upgrade) databázi redakčního sytému
            #      - crud práva + grant option k nové databázi zabezpečení
            #      - select k staré databázi redakního systému
            # - reálně nejlépe role DBA
            #
            # Konfigurace připojení k databázi je v delegate kontejneru.
            # Konfigurace připojení k databázi může být v aplikačním kontejneru nebo různá v jednotlivých middleware kontejnerech.
            #
            'build.db.user.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'UPGRADE_BUILD_PRODUCTION_USER' : 'oa_upgrader',
            'build.db.user.password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'UPGRADE_BUILD_PRODUCTION_HOST' : 'oa_upgrader',
            #
            ###################################

            ###################################
            # Konfigurace vytvářených uživatelů - příkaz createusers - ostatní parametry přidá kontejner
            #
            'build.config.users.everyone' =>
                [
                    'everyone_user' => PES_RUNNING_ON_PRODUCTION_HOST ? 'revoluceorg003' : 'oa_everyone',
                    'everyone_password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'UPGRADE_BUILD_PRODUCTION_USER' : 'oa_everyone',
                ],
            'build.config.users.granted' =>
                [
                    'authenticated_user' => PES_RUNNING_ON_PRODUCTION_HOST ? 'revoluceorg003' : 'oa_auth',
                    'authenticated_password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'UPGRADE_BUILD_PRODUCTION_USER' : 'oa_auth',
                    'administrator_user' => PES_RUNNING_ON_PRODUCTION_HOST ? 'revoluceorg003' : 'oa_admin',
                    'administrator_password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'UPGRADE_BUILD_PRODUCTION_USER' : 'oa_admin',
                ],
            #
            ###################################

            ###################################
            # Konfigurace make - ostatní parametry přidá kontejner
            # pole build.config.make.items: [api_module, api_generator, list, title]
            
            'build.config.items.api.red_empty' => ['red', 'empty'],
            
            'build.config.make.items' => [               
                [ 'trash', 'Trash'],
                [ 'blocks', 'Blocks'],
                [ 'menu_vertical', 'Menu vertical'],
            ],
            'build.config.convert.items' => [               
                [ 'trash', 'Trash'],
                [ 'blocks', 'Blocks'],
                [ 'menu_vertical', 'Menu vertical'],
            ],
            'build.config.import.items' => [                
                [ 'trash', 'Trash'],
                [ 'blocks', 'Blocks'],
                [ 'menu_vertical', 'Menu vertical'],
            ],
            
            'build.config.import.rootuid' => [
                '66422be5178a9'     // hierarchy uid položky menu, do které se provede konverze staré databáze pri importu
            ],
                 
            'build.config.root' => [
                'root', 'NAS_ROOT'
                ],
            
           
            
            'build.config.convert.copy' => [
                    'source' => 'otevreneatelierycz_20230905.stranky',
                    'target' => 'oa_upgrade.stranky'
            ],
            'build.config.import.copy' => [
                    'source' => 'otevreneatelierycz_20230905.stranky',
                    'target' => 'oa_upgrade.stranky'
            ],
            'build.config.convert.repairs' => [
            ],
            'build.config.import.repairs' => [
            ],
            'build.config.convert.updatestranky' => [
                ['a0', 's00', 0],        // !! menu menu_vertical je s titulní stranou list=a0 - existující stránku list=a0 ve staré db změním na list='l00', poradi=0
            ],
            'build.config.import.updatestranky' => [
                ['a0', 's00', 0],        // !! menu menu_vertical je s titulní stranou list=a0 - existující stránku list=a0 ve staré db změním na list='l00', poradi=0
            ],
            'build.config.convert.prefixmap' => [
                's'=>'menu_vertical',
                'a'=>'blocks'
            ],
            'build.config.import.prefixmap' => [
                's'=>'menu_vertical',
                'a'=>'blocks'
            ],       
            'build.config.convert.home' => [
                'home', 's00',        // titulní stránka s00 (změněná a0) je home page
            ],

            'build.config.convert.final' => [
                ],
            'build.config.import.final' => [
                ],

            #
            ###################################

            ###################################
            # Konfigurace logů konverze
            'build.db.logs.directory' => 'Logs/Build',
            'build.db.logs.file.dropOrCreateDb' => 'dropOrCreateDb.log',
            'build.db.logs.file.dropOrCreateUsers' => 'dropOrCreateUsers.log',
            'build.db.logs.file.convert' => 'Convert.log',
            'build.db.logs.file.type' => FileLogger::REWRITE_LOG
            #
            ###################################
        ];
    }


    /**
     * Konfigurace kontejneru - vrací parametry pro DbOldContainerConfigurator
     * @return array
     */
    public static function dbOld() {
        return [
            #################################
            # Konfigurace připojení k databázi starého redakčního systému
            #
            # Konfigurována jsou dvě připojení k databázi - jedno pro vývoj a druhé pro běh na produkčním stroji
            #
            'auth.db.type' => DbTypeEnum::MySQL,
            'auth.db.port' => '3306',
            'auth.db.charset' => 'utf8',
            'auth.db.collation' => 'utf8_general_ci',
            'auth.db.connection.host' => PES_RUNNING_ON_PRODUCTION_HOST ? '127.0.0.1' : '127.0.0.1' ,   // 'localhost' zbytečně překládá jméno,
            'auth.db.connection.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'revoluceorg04' : 'single_login',  // 'revoluceorg04' : 'single_login',
            #
            ###################################
            # Konfigurace logu databáze
            #
            'auth.logs.directory' => 'Logs/Auth',
            'auth.logs.db.file' => 'Database.log',
            'auth.logs.db.type' => FileLogger::FILE_PER_DAY,
            #
            ###################################
        ];
    }

    /**
     * Konfigurace kontejneru - vrací parametry pro DbUpgradeContainerConfigurator
     * @return array
     */
    public static function dbUpgrade() {
        return [
            #####################################
            # Konfigurace připojení k databázi nového redakčního systému
            #
            # Konfigurována jsou dvě připojení k databázi - jedno pro vývoj a druhé pro běh na produkčním stroji
            #
            'red.db.type' => DbTypeEnum::MySQL,
            'red.db.port' => '3306',
            'red.db.charset' => 'utf8',
            'red.db.collation' => 'utf8_general_ci',
            'red.db.connection.host' => PES_RUNNING_ON_PRODUCTION_HOST ? 'UPGRADE_PRODUCTION_HOST' : '127.0.0.1' ,   // 'localhost' zbytečně překládá jméno,
            'red.db.connection.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'UPGRADE_PRODUCTION_NAME' : 'oa_upgrade',
            #
            ###################################
            # Konfigurace logu databáze
            #
            'red.logs.db.directory' => 'Logs/Red',
            'red.logs.db.file' => 'Database.log',
            'red.logs.db.type' => FileLogger::FILE_PER_DAY,
            #
            #################################

        ];
    }

    /**
     * Konfigurace kontejneru - vrací parametry pro HierarchyContainerConfigurator
     * @return array
     */
    public static function hierarchy() {
        return  [
            ###################################
            # Konfigurace hierarchy tabulek
            #
            'hierarchy.table' => 'hierarchy',
            'hierarchy.view' => 'hierarchy_view',
            'hierarchy.menu_item_table' => 'menu_item',
            #
            ##################################
            # konfigurace menu
            #
            'hierarchy.new_title' => 'Nová položka',
            #
            #####################################
        ];
    }

    /**
     * Konfigurace kontejneru - vrací parametry pro LoginContainerConfigurator
     * @return array
     */
    public static function login() {
        return  [
            #################################
            # Sekce konfigurace uživatele databáze
            #
            # - konfigurováni dva uživatelé - jeden pro vývoj a druhý pro běh na produkčním stroji
            # - uživatelé musí mít právo select k databázi s tabulkou uživatelských oprávnění
            # MySQL 5.6: délka jména max 16 znaků

            'auth.db.account.everyone.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'xxxxxxxxxxxxxxx' : 'single_login',  // nelze použít jméno uživatele použité pro db upgrade - došlo by k duplicitě jmen v build create
            'auth.db.account.everyone.password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'gr_login' : 'single_login',

            'auth.logs.database.directory' => 'Logs/Auth',
            'auth.logs.database.file' => 'Database.log',
            'auth.logs.database.type' => FileLogger::FILE_PER_DAY
            #
            ###################################

        ];
    }
    /**
     * Konfigurace kontejneru - vrací parametry pro WebContainerConfigurator a DbUpgradeContainerConfigurator
     * @return array
     */
    public static function sqlite() {
        return [
            #####################################
            # Konfigurace připojení k databázi sqlite
            #
            #
            'sqlite.db.type' => DbTypeEnum::SQLITE,
            'sqlite.db.connection.name' => PES_RUNNING_ON_PRODUCTION_HOST ? '/sqlite' : '/sqlite',
            #
            ###################################
            # Konfigurace logu databáze
            #
            'sqlite.logs.db.directory' => 'Logs/Sqlite',
            'sqlite.logs.db.file' => 'Database.log',
            'sqlite.logs.db.type' => FileLogger::FILE_PER_DAY,
            #
            #################################

        ];
    }
    
    /**
     * Konfigurace kontejneru - vrací parametry pro WebContainerConfigurator
     * @return array
     */
    public static function web() {
        return [
            #################################
            # Sekce konfigurace uživatelů databáze nového redakčního systému
            #
            # Zde je konfigurace údajů uživatele pro připojení k databázi.
            #
            # Konfigurace připojení k databázi je v delegate kontejneru.
            # Konfigurace připojení k databázi může být v aplikačním kontejneru nebo různá v jednotlivých middleware kontejnerech.
            #
            'web.db.account.everyone.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'oa_uzivatel_jmeno_nastavit' : 'oa_everyone',
            'web.db.account.everyone.password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'oa_uzivatel_heslo_nastavit' : 'oa_everyone',
            'web.db.account.authenticated.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'oa_uzivatel_jmeno_nastavit' : 'oa_auth',
            'web.db.account.authenticated.password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'oa_uzivatel_heslo_nastavit' : 'oa_auth',
            'web.db.account.administrator.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'oa_uzivatel_jmeno_nastavit' : 'oa_admin',
            'web.db.account.administrator.password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'oa_uzivatel_heslo_nastavit' : 'oa_admin',
            #
            ###################################
        ];
    }

}
