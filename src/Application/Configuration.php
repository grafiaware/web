<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application;


use Pes\Database\Handler\DbTypeEnum;

/**
 * Description of Configuration
 *
 * @author pes2704
 */
class Configuration {
    public static function api() {
        return [
            #################################
            # Sekce konfigurace účtů databáze pro api kontejner
            #
            'api.db.everyone.name' => 'gr_everyone',
            'api.db.everyone.password' => 'gr_everyone',
            'api.db.authenticated.name' => 'gr_authenticated',
            'api.db.authenticated.password' => 'gr_authenticated',
            'api.db.administrator.name' => 'gr_administrator',
            'api.db.administrator.password' => 'gr_administrator',
            #
            ###################################
            #
            'api.logs.view.directory' => 'Logs/App/Web',
            'api.logs.view.file' => 'Render.log',
            #
            ###################################
        ];
    }

    public static function app() {
        return [
            #################################
            # Konfigurace adresáře logů
            #
            'app.logs.directory' => 'Logs/App',
            #
            #################################

            #################################
            # Konfigurace session
            #
            WebAppFactory::SESSION_NAME_SERVICE => 'www_gr_session',
            'app.logs.session.file' => 'Session.log',
            #
            ##################################

            ##################################
            # Konfigurace session
            #
            'app.logs.router.file' => 'Router.log',
            #
            ##################################
        ];
    }

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
            'build.db.user.name' => PES_DEVELOPMENT ? 'gr_upgrader' : (PES_PRODUCTION ? 'UPGRADE_BUILD_PRODUCTION_USER' : 'xxxxxxxxxxxxxxxxx'),
            'build.db.user.password' => PES_DEVELOPMENT ? 'gr_upgrader' : (PES_PRODUCTION ? 'UPGRADE_BUILD_PRODUCTION_HOST' : 'xxxxxxxxxxxxxxxxx'),
            #
            #  Konec sekce konfigurace databáze
            ###################################

            ###################################
            # Konfigurace konverze
            #

            'build.config.createusers' =>
                [
                    'everyone_user' => 'gr_everyone',
                    'everyone_password' => 'gr_everyone',
                    'authenticated_user' => 'gr_authenticated',
                    'authenticated_password' => 'gr_authenticated',
                    'administrator_user' => 'gr_administrator',
                    'administrator_password' => 'gr_administrator',
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
        ];
    }

    public static function component() {
        return [
            'component.logs.view.directory' => 'Logs/App/Web',
            'component.logs.view.file' => 'Render.log',
        ];
    }

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

            'dbold.db.connection.host' => PES_DEVELOPMENT ? 'localhost' : (PES_PRODUCTION ? 'OLD_PRODUCTION_NAME' : 'xxxxxxxxxxxxxxxxx'),
            'dbold.db.connection.name' => PES_DEVELOPMENT ? 'grafiacz' : (PES_PRODUCTION ? 'OLD_PRODUCTION_HOST' : 'xxxxxxxxxxxxxxxxx'),
//            'dbold.db.connection.name' => PES_DEVELOPMENT ? 'wwwgrafia' : (PES_PRODUCTION ? 'OLD_PRODUCTION_HOST' : 'xxxxxxxxxxxxxxxxx'),

            'dbold.logs.directory' => 'Logs/DbOld',
            'dbold.logs.db.file' => 'Database.log',
            #
            # Konec sekce konfigurace databáze
            ###################################
        ];
    }

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
            'dbUpgrade.db.connection.host' => PES_DEVELOPMENT ? 'localhost' : (PES_PRODUCTION ? 'UPGRADE_PRODUCTION_HOST' : 'xxxx'),
            'dbUpgrade.db.connection.name' => PES_DEVELOPMENT ? 'gr_upgrade' : (PES_PRODUCTION ? 'UPGRADE_PRODUCTION_NAME' : 'xxxx'),
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

    public static function hierarchy() {
        return  [
            #################################
            # Konfigurace databáze
            # Konfigurovány databáze - v kontejneru dbUpgrade
            #
            'dbUpgrade.db.user.name' => PES_DEVELOPMENT ? 'gr_upgrader' : (PES_PRODUCTION ? 'UPGRADE_PRODUCTION_USER_NAME' : 'xxxx'),
            'dbUpgrade.db.user.password' => PES_DEVELOPMENT ? 'gr_upgrader' : (PES_PRODUCTION ? 'UPGRADE_PRODUCTION_USER_PASSWORD' : 'xxxx'),
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
            'web.db.account.authenticated.name' => 'gr_authenticated',
            'web.db.account.authenticated.password' => 'gr_authenticated',
            'web.db.account.administrator.name' => 'gr_administrator',
            'web.db.account.administrator.password' => 'gr_administrator',
            #
            ###################################
        ];
    }

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
            'rs.db.account.authenticated.name' => 'gr_authenticated',
            'rs.db.account.authenticated.password' => 'gr_authenticated',
            'rs.db.account.administrator.name' => 'gr_administrator',
            'rs.db.account.administrator.password' => 'gr_administrator',
            #
            ###################################

        ];
    }
}
