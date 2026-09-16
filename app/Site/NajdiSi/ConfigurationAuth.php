<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site\NajdiSi;

use Access\Enum\RoleEnum;

use Pes\Database\Handler\DbTypeEnum;
use Pes\Logger\FileLogger;

/**
 * Description of Configuration
 *
 * @author pes2704
 */
class ConfigurationAuth extends ConfigurationConstants {

    public static function auth() {

        ## JMÉNO - malé velké písmeno, číslice, min. 5 znaků
        $jmenoPattern = "\w{5,}";
        $jmenoInfo = "Jméno musí obsahovat jen malá nebo velká písmena bez diakritiky, číslice a podtržítko. Jiné znaky (například mezera) nejsou povoleny. Délka musí být nejméně 5 znaků.";

        ## HESLO - malé velké písmeno, číslice, min. 5 znaků
        $passwordPattern = "(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{5,}";
        $passwordInfo = "Heslo musí obsahovat nejméně jedno velké písmeno, jedno malé písmeno a jednu číslici. Jiné znaky než písmena a číslice nejsou povoleny. Délka musí být nejméně 5 znaků.";

        ## HESLO - malé velké písmeno, číslice, min. 3 znaky
//        $passwordPattern = "(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{3,}";
//        $passwordInfo = "Heslo musí obsahovat nejméně jedno velké písmeno, jedno malé písmeno a jednu číslici. Jiné znaky než písmena a číslice nejsou povoleny. Délka musí být nejméně 3 znaky.";

        $siteSpecificToken = str_replace('/', '', self::WEB_SITE);
        return [
                'fieldNameJmeno' => 'jmeno'.$siteSpecificToken,
                'fieldNameHeslo' => 'heslo'.$siteSpecificToken,
                'fieldNameHesloStare' => 'hesloStare'.$siteSpecificToken,
                'jmenoPattern' => $jmenoPattern,
                'jmenoInfo' => $jmenoInfo,
                'passwordPattern' => $passwordPattern,
                'passwordInfo' => $passwordInfo,
                'roleVisitor' => RoleEnum::VISITOR,
                'roleRepresentative' => RoleEnum::REPRESENTATIVE,
                'roleEventsAdministrator' => RoleEnum::EVENTS_ADMINISTRATOR,
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
            'auth.db.connection.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'najdisicz02' : 'single_login',  // 'revoluceorg04' : 'single_login',
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

            'auth.db.account.everyone.name' => PES_RUNNING_ON_PRODUCTION_HOST ? 'najdisicz003' : 'single_login',  // nelze použít jméno uživatele použité pro db upgrade - došlo by k duplicitě jmen v build create
            'auth.db.account.everyone.password' => PES_RUNNING_ON_PRODUCTION_HOST ? 'najdisicz003' : 'single_login',

            'auth.logs.database.directory' => 'Logs/Auth',
            'auth.logs.database.file' => 'Database.log',
            'auth.logs.database.type' => FileLogger::FILE_PER_DAY
            #
            ###################################

        ];
    }
}
