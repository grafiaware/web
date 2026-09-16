<?php

namespace Site\NajdiSi;

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
            'build.db.user.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'XXXXXXXXXXX' : 'na_upgrader',
            'build.db.user.password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'XXXXXXXXXXX' : 'na_upgrader',
            #
            ###################################

            ###################################
            # Konfigurace vytvářených uživatelů - příkaz createusers - ostatní parametry přidá kontejner
            #
            'build.config.users.everyone' =>
                [
                    'everyone_user' => PES_RUNNING_ON_PRODUCTION_HOST ? 'XXXXXXXXXXX' : 'na_everyone',
                    'everyone_password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'XXXXXXXXXXX' : 'na_everyone',
                ],
            'build.config.users.granted' =>
                [
                    'authenticated_user' => PES_RUNNING_ON_PRODUCTION_HOST ? 'XXXXXXXXXXX' : 'na_auth',
                    'authenticated_password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'XXXXXXXXXXX' : 'na_auth',
                    'administrator_user' => PES_RUNNING_ON_PRODUCTION_HOST ? 'XXXXXXXXXXX' : 'na_admin',
                    'administrator_password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'XXXXXXXXXXX' : 'na_admin',
                ],
            #
            ###################################

            ###################################
            # Konfigurace make - ostatní parametry přidá kontejner
            # pole build.config.make.items: [api_module, api_generator, list, title]
            
            'build.config.items.menurootsapi' => ['red', 'root'],
            
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
                '66432858db66c'     // hierarchy uid položky menu, do které se provede konverze staré databáze pri importu
            ],                 
            'build.config.root' => [
                'root', 'NAS_ROOT'
                ],
            'build.config.convert.copy' =>                 [
                    'source' => 'pracinajdisi_20240123.stranky',
                    'target' => 'najdisi.stranky'
                ],        
            'build.config.import.copy' => [
                    'source' => 'otevreneatelierycz_20230905.stranky',
                    'target' =>  'najdisi.stranky'
            ],
            
            
            'build.config.convert.repairs' => [
                ],
            'build.config.import.repairs' => [
            ],
            'build.config.convert.updatestranky' => [
                ['a0', 'l00', 0],        // !! menu menu_vertical je s titulní stranou list=a0 - existující stránku list=a0 ve staré db změním na list='l00', poradi=0
            ],
            'build.config.import.updatestranky' => [
                ['a0', 'l00', 0],        // !! menu menu_vertical je s titulní stranou list=a0 - existující stránku list=a0 ve staré db změním na list='l00', poradi=0
            ],
            'build.config.convert.prefixmap' => [
                'l'=>'menu_vertical',
                'a'=>'blocks'
            ],
            'build.config.import.prefixmap' => [
                's'=>'menu_vertical',
                'a'=>'blocks'
            ],       
            'build.config.convert.home' => [
                'home', 'l00',        // titulní stránka s00 (změněná a0) je home page
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
}
