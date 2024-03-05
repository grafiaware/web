<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site\OtevreneAteliery;

use Pes\Logger\FileLogger;

/**
 * Description of Configuration
 *
 * @author pes2704
 */
class ConfigurationRed extends ConfigurationConstants {

    /**
     * Konfigurace kontejneru - vrací parametry pro ComponentContainerConfigurator
     *
     * Konfiguruje logování a šablony pro komponenty, které renderují šablony
     *
     * @return array
     */
    public static function redComponent() {
        return [
            'redcomponent.logs.directory' => 'Logs/Red',
            'redcomponent.logs.render' => 'Render.log',
            'redcomponent.logs.type' => FileLogger::REWRITE_LOG,
            'redcomponent.templates' => [
                'flash' => self::WEB_TEMPLATES_COMMON.'layout/info/flashMessages.php',
                'login' => self::WEB_TEMPLATES_COMMON.'layout/status/login.php',
//                'register' => self::WEB_TEMPLATES_COMMON.'layout/status/register.php',  // nahrazeno - site layout templates
                'logout' => self::WEB_TEMPLATES_COMMON.'layout/status/logout.php',
                'useraction' => self::WEB_TEMPLATES_COMMON.'layout/status/userAction.php',
                'statusboard' => self::WEB_TEMPLATES_COMMON.'layout/info/statusBoard.php',
                'controleditmenu' => self::WEB_TEMPLATES_COMMON.'layout/status/controlEditMenu.php',
                // site layout templates
                'register' => self::WEB_TEMPLATES_SITE.'layout/status/register-with-exhibitor-representative.php',
            ]
        ];
    }

    public static function itemActionControler() {
        return [
            'timeout' => 'PT1H'   // 1 hodina
        ];
    }

    public static function loginLogoutController() {
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
                'passwordPattern' => $passwordPattern,
                'passwordInfo' => $passwordInfo,
                'roleVisitor' => 'visitor',
                'roleRepresentative' => 'representative',
        ];
    }

    /**
     * Konfigurace prezentace - vrací parametry pro ComponentController
     * @return array
     */
    public static function componentController() {

        return [
                'templates' => self::WEB_TEMPLATES_SITE,
                'static' => self::WEB_STATIC,
                'compiled' => self::WEB_STATIC.'__compiled/',
            ];
    }

    /**
     * Konfigurace - parametry pro templateController
     * @return array
     */
    public static function redTemplates() {

        return [
                'templates.defaultExtension' => '.php',
                // pole složek, jsou prohledávány postupně při hledání souboru s šablonou zadaného typu
                'templates.folders' => [
                    'author'=>[self::WEB_TEMPLATES_COMMON.'red/author/'],   //jen v common
                    'paper' => [self::WEB_TEMPLATES_SITE.'red/paper/', self::WEB_TEMPLATES_COMMON.'red/paper/'],
                    'article' => [self::WEB_TEMPLATES_SITE.'red/article/', self::WEB_TEMPLATES_COMMON.'red/article/'],
                    'multipage' => [self::WEB_TEMPLATES_SITE.'red/multipage/', self::WEB_TEMPLATES_COMMON.'red/multipage/'],
                    ],
            ];
    }

    /**
     * Konfigurace upload files - vrací parametry pro FilesUploadController
     * @return array
     */
    public static function redUpload() {

        return [
            'upload.red' => PES_RUNNING_ON_PRODUCTION_HOST ? self::WEB_FILES_SITE.'upload/red/' : self::WEB_FILES_SITE.'upload/red/',
            ];
    }
}
