<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site\VeletrhPrace;

use Pes\Logger\FileLogger;

/**
 * Description of ConfigurationBuild
 *
 * @author pes2704
 */
class ConfigurationBuild {

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
            'build.db.user.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'veletrhpraceo001' : 'vp_upgrader',
            'build.db.user.password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'vp_upgrader' : 'vp_upgrader',
            #
            ###################################

            ###################################
            # Konfigurace vytvářených uživatelů - příkaz createusers - ostatní parametry přidá kontejner
            #
            'build.config.users.everyone' =>
                [
                    'everyone_user' => PES_RUNNING_ON_PRODUCTION_HOST ? 'veletrhpraceo001' : 'vp_everyone',
                    'everyone_password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'vp_upgrader' : 'vp_everyone',
                ],
            'build.config.users.granted' =>
                [
                    'authenticated_user' => PES_RUNNING_ON_PRODUCTION_HOST ? 'veletrhpraceo001' : 'vp_auth',
                    'authenticated_password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'vp_upgrader' : 'vp_auth',
                    'administrator_user' => PES_RUNNING_ON_PRODUCTION_HOST ? 'veletrhpraceo001' : 'vp_admin',
                    'administrator_password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'vp_upgrader' : 'vp_admin',
                ],
            #
            ###################################

            ###################################
            # Konfigurace make - ostatní parametry přidá kontejner
            # pole build.config.make.items: [api_module, api_generator, list, title]
            'build.config.make.items' => [
                ['red', 'empty', 'root', 'ROOT'],
                ['red', 'empty', 'trash', 'Trash'],
                ['red', 'empty', 'blocks', 'Blocks'],
                ['red', 'empty', 'menu_vertical', 'Menu vertical'],
            ],
            'build.config.make.root' => [
                'root',
                ],
            'build.config.make.menuroots' => [
                'trash',
                'blocks',
                'menu_vertical',
            ],
            'build.config.convert.copy' =>  [],
            'build.config.convert.repairs' => [],
            'build.config.convert.updatestranky' => [],
            'build.config.convert.prefixmap' => [],
            'build.config.convert.importrootuid' => [],
            'build.config.convert.home' => [],

            'build.config.convert.final' => [],
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
}
