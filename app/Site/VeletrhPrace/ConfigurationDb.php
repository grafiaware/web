<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site\VeletrhPrace;

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
            'api.db.everyone.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'veletrhpraceonline001' : 'vp_everyone',
            'api.db.everyone.password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'vp_upgrader' : 'vp_everyone',
            'api.db.authenticated.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'veletrhpraceonline001' : 'vp_auth',
            'api.db.authenticated.password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'vp_upgrader' : 'vp_auth',
            'api.db.administrator.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'veletrhpraceonline001' : 'vp_admin',
            'api.db.administrator.password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'vp_upgrader' : 'vp_admin',
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
            'build.db.user.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'veletrhpraceonline001' : 'vp_upgrader',
            'build.db.user.password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'vp_upgrader' : 'vp_upgrader',
            #
            ###################################

            ###################################
            # Konfigurace create users - ostatní parametry přidá kontejner
            #
            'build.config.users.everyone' =>
                [
                    'everyone_user' => PES_RUNNING_ON_PRODUCTION_HOST ? 'veletrhpraceonline001' : 'vp_everyone',
                    'everyone_password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'vp_upgrader' : 'vp_everyone',
                ],
            'build.config.users.granted' =>
                [
                    'authenticated_user' => PES_RUNNING_ON_PRODUCTION_HOST ? 'veletrhpraceonline001' : 'vp_auth',
                    'authenticated_password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'vp_upgrader' : 'vp_auth',
                    'administrator_user' => PES_RUNNING_ON_PRODUCTION_HOST ? 'veletrhpraceonline001' : 'vp_admin',
                    'administrator_password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'vp_upgrader' : 'vp_admin',
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
                ['empty', 'menu_vertical', 'Menu'],
            ],
            'build.config.make.roots' => [
                'root',
                'trash',
                'blocks',
                'menu_vertical',
            ],
            'build.config.convert.copy' => [],
            'build.config.convert.updatestranky' => [],
            'build.config.convert.home' => [],
            'build.config.convert.repairs' => [],
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

            'dbold.db.connection.host' => PES_RUNNING_ON_PRODUCTION_HOST ? '127.0.0.1' : 'localhost',
            'dbold.db.connection.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'veletrhpraceonline01' : 'gr_pracovni',

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
            'dbUpgrade.db.connection.host' => PES_RUNNING_ON_PRODUCTION_HOST ? '127.0.0.1' : 'localhost',
            'dbUpgrade.db.connection.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'veletrhpraceonline01' : 'veletrhprace',
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
            'dbUpgrade.db.user.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'veletrhpraceonline001' : 'vp_upgrader',
            'dbUpgrade.db.user.password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'vp_upgrader' : 'vp_upgrader',
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

            'login.db.account.everyone.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'veletrhpraceonline002' : 'vp_login',  // nelze použít jméno uživatele použité pro db upgrade - došlo by k duplicitě jmen v build create
            'login.db.account.everyone.password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'vp_login' : 'vp_login',

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
            'web.db.account.everyone.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'veletrhpraceonline001' : 'vp_everyone',
            'web.db.account.everyone.password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'vp_upgrader' : 'vp_everyone',
            'web.db.account.authenticated.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'veletrhpraceonline001' : 'vp_auth',
            'web.db.account.authenticated.password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'vp_upgrader' : 'vp_auth',
            'web.db.account.administrator.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'veletrhpraceonline001' : 'vp_admin',
            'web.db.account.administrator.password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'vp_upgrader' : 'vp_admin',
            #
            ###################################
        ];
    }

}
