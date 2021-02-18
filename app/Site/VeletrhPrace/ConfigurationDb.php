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
            # - konfigurováni dva uživatelé - jeden pro vývoj a druhý pro běh na produkčním stroji
            #
            # Konfigurace připojení k databázi je v delegate kontejneru.
            # Konfigurace připojení k databázi může být v aplikačním kontejneru nebo různá v jednotlivých middleware kontejnerech.
            #
            'api.db.everyone.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'veletrhpraceonline001' : 'vp_everyone',
            'api.db.everyone.password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'vp_upgrader' : 'vp_everyone',
            'api.db.authenticated.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'veletrhpraceonline001' : 'vp_auth',
            'api.db.authenticated.password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'vp_upgrader' : 'vp_auth',
            'api.db.administrator.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'veletrhpraceonline001' : 'vp_admin',
            'api.db.administrator.password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'vp_upgrader' : 'vp_admin',
            #
            ###################################
            # Konfigurace logu databáze
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
            'build.db.user.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'veletrhpraceonline001' : 'vp_upgrader',
            'build.db.user.password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'vp_upgrader' : 'vp_upgrader',
            #
            ###################################

            ###################################
            # Konfigurace vytvářených uživatelů - příkaz createusers - ostatní parametry přidá kontejner
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
            # Konfigurace připojení k databázi starého redakčního systému
            #
            # Konfigurována jsou dvě připojení k databázi - jedno pro vývoj a druhé pro běh na produkčním stroji
            #
            'dbold.db.type' => DbTypeEnum::MySQL,
            'dbold.db.port' => '3306',
            'dbold.db.charset' => 'utf8',
            'dbold.db.collation' => 'utf8_general_ci',
            'dbold.db.connection.host' => PES_RUNNING_ON_PRODUCTION_HOST ? '127.0.0.1' : 'localhost',
            'dbold.db.connection.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'veletrhpraceonline01' : 'gr_pracovni',
            #
            ###################################
            # Konfigurace logu databáze
            #
            'dbold.logs.directory' => 'Logs/DbOld',
            'dbold.logs.db.file' => 'Database.log',
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
            'dbUpgrade.db.type' => DbTypeEnum::MySQL,
            'dbUpgrade.db.port' => '3306',
            'dbUpgrade.db.charset' => 'utf8',
            'dbUpgrade.db.collation' => 'utf8_general_ci',
            'dbUpgrade.db.connection.host' => PES_RUNNING_ON_PRODUCTION_HOST ? '127.0.0.1' : 'localhost',
            'dbUpgrade.db.connection.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'veletrhpraceonline01' : 'veletrhprace',
            #
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
            # Konfigurace uživatele databáze
            #
            # - konfigurováni dva uživatelé - jeden pro vývoj a druhý pro běh na produkčním stroji
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
            # Sekce konfigurace uživatele databáze
            #
            # - konfigurováni dva uživatelé - jeden pro vývoj a druhý pro běh na produkčním stroji
            # - uživatelé musí mít právo select k databázi s tabulkou uživatelských oprávnění
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
            # Sekce konfigurace uživatelů databáze nového redakčního systému
            #
            # Zde je konfigurace údajů uživatele pro připojení k databázi.
            #
            # Konfigurace připojení k databázi je v delegate kontejneru.
            # Konfigurace připojení k databázi může být v aplikačním kontejneru nebo různá v jednotlivých middleware kontejnerech.
            #
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
