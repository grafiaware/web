<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site\Grafia;

use Pes\Database\Handler\DbTypeEnum;

/**
 * Description of Configuration
 *
 * @author pes2704
 */
class ConfigurationDb extends ConfigurationConstants {

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
            # Ostatní parametry konfigurace databáze v kontejneru dbUpgrade
            #
            'api.db.everyone.name' => 'gr_everyone',
            'api.db.everyone.password' => 'gr_everyone',
            'api.db.authenticated.name' => 'gr_auth',
            'api.db.authenticated.password' => 'gr_auth',
            'api.db.administrator.name' => 'gr_admin',
            'api.db.administrator.password' => 'gr_admin',
            #
            ###################################
            #
            'api.logs.view.directory' => 'Logs/App/Web',
            'api.logs.view.file' => 'Render.log',
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
            # Sekce konfigurace databáze
            # Konfigurace databáze může být v aplikačním kontejneru nebo různá v jednotlivých middleware kontejnerech.
            #
            ## konfigurována dvě připojení k databázi - jedno pro vývoj a druhé pro běh na produkčním stroji
            #
            # user s právy drop a create database + crud práva + grant option k nové (upgrade) databázi
            # a také select k staré databázi - reálně nejlépe role DBA
            'build.db.user.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'UPGRADE_BUILD_PRODUCTION_USER' : 'gr_upgrader',
            'build.db.user.password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'UPGRADE_BUILD_PRODUCTION_HOST' : 'gr_upgrader',
            #
            ###################################

            ###################################
            # Konfigurace create users - ostatní parametry přidá kontejner
            #
            'build.config.users.everyone' =>
                [
                    'everyone_user' => 'gr_everyone',
                    'everyone_password' => 'gr_everyone',
                ],
            'build.config.users.granted' =>
                [
                    'authenticated_user' => 'gr_auth',
                    'authenticated_password' => 'gr_auth',
                    'administrator_user' => 'gr_admin',
                    'administrator_password' => 'gr_admin',
                ],
            #
            ###################################

            ###################################
            # Konfigurace make - ostatní parametry přidá kontejner
            # pole build.config.make.roots: [type, list, title]
            'build.config.make.items' => [
                ['root', 'root', 'ROOT'],
                ['trash', 'trash', 'Trash'],
                ['paper', 'blocks', 'Blocks'],
                ['paper', 'menu_vertical', 'Menu'],
                ['paper', 'menu_horizontal', 'Menu'],
                ['paper', 'menu_redirect', 'Menu'],
            ],
            'build.config.make.roots' => [
                'root',
                'trash',
                'blocks',
                'menu_vertical',
                'menu_horizontal',
                'menu_redirect',
            ],
            'build.config.convert.copy' =>
                [
                    'source' => 'stranky',
                    'target' => 'stranky'
                ],
            'build.config.convert.updatestranky' => [
                ['a0', 's00', -1],        // !! menu menu_vertical je s titulní stranou list=a0 - existující stránku list=a0 ve staré db změním na list='s00', poradi=-1
            ],
            'build.config.convert.home' => [
                'home', 's00',        // titulní stránka s00 (změněná a0) je home page
            ],
            'build.config.convert.repairs' => [
                // smazání chybné stránky v grafia databázích s list='s_01' - chybná syntax list způdobí chyby při vyztváření adjlist - původní stránka nemá žádný obsah
                "DELETE FROM stranky WHERE list = 's_01'",
                ],
            #
            ###################################

            ###################################
            # Konfigurace logů konverze
            'build.db.logs.directory' => 'Logs/Build',
            'build.db.logs.file.drop' => 'Drop.log',
            'build.db.logs.file.create' => 'Create.log',
            'build.db.logs.file.convert' => 'Convert.log',
            #
            ###################################

            ###################################
            # Konfigurace hierarchy tabulek
            #
            'build.hierarchy.table' => 'hierarchy',
            'build.hierarchy.view' => 'hierarchy_view',
            #
            ##################################
            #
        ];
    }


    /**
     * Konfigurace kontejneru - vrací parametry pro DbOldContainerConfigurator
     * @return array
     */
    public static function dbOld() {
        return [
            #################################
            # Sekce konfigurace databáze
            # Konfigurace databáze může být v aplikačním kontejneru nebo různá v jednotlivých middleware kontejnerech.
            # Služby, které vrací objekty jsou v aplikačním kontejneru a v jednotlivých middleware kontejnerech musí být
            # stejná sada služeb, které vracejí hodnoty konfigurace.
            #
            'dbold.db.type' => DbTypeEnum::MySQL,
            'dbold.db.port' => '3306',
            'dbold.db.charset' => 'utf8',
            'dbold.db.collation' => 'utf8_general_ci',

            'dbold.db.connection.host' => PES_RUNNING_ON_PRODUCTION_HOST ? 'OLD_PRODUCTION_NAME' : 'localhost',
            'dbold.db.connection.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'OLD_PRODUCTION_HOST' : 'wwwgrafia',

            'dbold.logs.directory' => 'Logs/DbOld',
            'dbold.logs.db.file' => 'Database.log',
            #
            # Konec sekce konfigurace databáze
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
            # Konfigurace databáze
            #
            # konfigurovány dvě databáze pro Hierarchy a Konverze kontejnery
            # - jedna pro vývoj a druhá pro běh na produkčním stroji
            #
            'dbUpgrade.db.type' => DbTypeEnum::MySQL,
            'dbUpgrade.db.port' => '3306',
            'dbUpgrade.db.charset' => 'utf8',
            'dbUpgrade.db.collation' => 'utf8_general_ci',
            'dbUpgrade.db.connection.host' => PES_RUNNING_ON_PRODUCTION_HOST ? 'UPGRADE_PRODUCTION_HOST' : 'localhost',
            'dbUpgrade.db.connection.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'UPGRADE_PRODUCTION_NAME' : 'gr_upgrade',
            #
            #  Konec sekce konfigurace databáze
            ###################################
            # Konfigurace logu databáze
            #
            'dbUpgrade.logs.db.directory' => 'Logs/Hierarchy',
            'dbUpgrade.logs.db.file' => 'Database.log',
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
            #################################
            # Konfigurace databáze
            # Ostatní parametry konfigurace databáze v kontejneru dbUpgrade
            #
            'dbUpgrade.db.user.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'UPGRADE_PRODUCTION_USER_NAME' : 'gr_upgrader',
            'dbUpgrade.db.user.password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'UPGRADE_PRODUCTION_USER_PASSWORD' : 'gr_upgrader',
            #
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
            # Sekce konfigurace účtů databáze
            #
            # user s právem select k databázi s tabulkou uživatelských oprávnění
            # MySQL 5.6: délka jména max 16 znaků

            'login.db.account.everyone.name' => 'gr_login',  // nelze použít jméno uživatele použité pro db upgrade - došlo by k duplicitě jmen v build create
            'login.db.account.everyone.password' => 'gr_login',

            'login.logs.database.directory' => 'Logs/Login',
            'login.logs.database.file' => 'Database.log',
            #
            ###################################

        ];
    }

    /**
     * Konfigurace kontejneru - vrací parametry pro WebContainerConfigurator
     * @return array
     */
    public static function web() {
        return [
            #################################
            # Sekce konfigurace účtů databáze
            # Konfigurace připojení k databázi je v aplikačním kontejneru, je pro celou aplikaci stejná.
            # Služby, které vrací objekty s informacemi pro připojení k databázi jsou také v aplikačním kontejneru a v jednotlivých middleware
            # kontejnerech se volají jako služby delegate kontejneru.
            #
            # Zde je konfigurace údajů uživatele pro připojení k databázi. Ta je pro každý middleware v jeho kontejneru.
            'web.db.account.everyone.name' => 'gr_everyone',
            'web.db.account.everyone.password' => 'gr_everyone',
            'web.db.account.authenticated.name' => 'gr_auth',
            'web.db.account.authenticated.password' => 'gr_auth',
            'web.db.account.administrator.name' => 'gr_admin',
            'web.db.account.administrator.password' => 'gr_admin',
            #
            ###################################
        ];
    }

    /**
     * Konfigurace kontejneru - vrací parametry pro RsContainerConfigurator
     * @return array
     */
    public static function rs() {
        return [

            #################################
            # Sekce konfigurace účtů databáze
            # Konfigurace připojení k databázi je v aplikačním kontejneru, je pro celou apliaci stejná.
            # Služby, které vrací objekty s informacemi pro připojení k databázi jsou také v aplikačním kontejneru a v jednotlivých middleware
            # kontejnerech se volají jako služby delegate kontejneru.
            #
            # Zde je konfigurace údajů uživatele pro připojení k databízi. Ta je pro každý middleware v jeho kontejneru.
            'rs.db.account.everyone.name' => 'gr_everyone',
            'rs.db.account.everyone.password' => 'gr_everyone',
            'rs.db.account.authenticated.name' => 'gr_auth',
            'rs.db.account.authenticated.password' => 'gr_auth',
            'rs.db.account.administrator.name' => 'gr_admin',
            'rs.db.account.administrator.password' => 'gr_admin',
            #
            ###################################

        ];
    }
}
