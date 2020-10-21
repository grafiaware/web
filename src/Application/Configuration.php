<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application;

use Pes\Database\Handler\DbTypeEnum;

use Component\View\Flash\FlashComponent;
use Component\View\Status\{
    LoginComponent,
    LogoutComponent,
    UserActionComponent
};

/**
 * Description of Configuration
 *
 * @author pes2704
 */
class Configuration {
    ### bootstrap ###
    #
    public static function bootstrap() {
        return [
            'bootatrap_logs_base_path' => '/_www_oa_logs/',
        ];
    }

    ### kontejner ###
    #
    public static function api() {
        return [
            #################################
            # Sekce konfigurace účtů databáze pro api kontejner
            # Ostatní parametry konfigurace databáze v kontejneru dbUpgrade
            #
            'api.db.everyone.name' => 'oa_everyone',
            'api.db.everyone.password' => 'oa_everyone',
            'api.db.authenticated.name' => 'oa_authenticated',
            'api.db.authenticated.password' => 'oa_authenticated',
            'api.db.administrator.name' => 'oa_administrator',
            'api.db.administrator.password' => 'oa_administrator',
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
            WebAppFactory::SESSION_NAME_SERVICE => 'www_oa_session',
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
            'build.db.user.name' => PES_DEVELOPMENT ? 'oa_upgrader' : (PES_PRODUCTION ? 'UPGRADE_BUILD_PRODUCTION_USER' : 'xxxxxxxxxxxxxxxxx'),
            'build.db.user.password' => PES_DEVELOPMENT ? 'oa_upgrader' : (PES_PRODUCTION ? 'UPGRADE_BUILD_PRODUCTION_HOST' : 'xxxxxxxxxxxxxxxxx'),
            #
            ###################################

            ###################################
            # Konfigurace konverze
            #
            'build.config.createusers' =>
                [
                    'everyone_user' => 'oa_everyone',
                    'everyone_password' => 'oa_everyone',
                    'authenticated_user' => 'oa_authenticated',
                    'authenticated_password' => 'oa_authenticated',
                    'administrator_user' => 'oa_administrator',
                    'administrator_password' => 'oa_administrator',
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

    public static function component() {
        return [
            'component.logs.view.directory' => 'Logs/App/Web',
            'component.logs.view.file' => 'Render.log',
            'component.template.'.FlashComponent::class =>      PROJECT_PATH.'public/web/templates/info/flashMessage.php',
            'component.template.'.LoginComponent::class =>      PROJECT_PATH.'public/web/templates/modal/login.php',
            'component.template.'.LogoutComponent::class =>     PROJECT_PATH.'public/web/templates/modal/logout.php',
            'component.template.'.UserActionComponent::class => PROJECT_PATH.'public/web/templates/modal/user_action.php',
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
            'dbold.db.connection.name' => PES_DEVELOPMENT ? 'otevreneatelierycz' : (PES_PRODUCTION ? 'OLD_PRODUCTION_HOST' : 'xxxxxxxxxxxxxxxxx'),

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
            'dbUpgrade.db.connection.name' => PES_DEVELOPMENT ? 'oa_upgrade' : (PES_PRODUCTION ? 'UPGRADE_PRODUCTION_NAME' : 'xxxx'),
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
            # Ostatní parametry konfigurace databáze v kontejneru dbUpgrade
            #
            'dbUpgrade.db.user.name' => PES_DEVELOPMENT ? 'oa_upgrader' : (PES_PRODUCTION ? 'UPGRADE_PRODUCTION_USER_NAME' : 'xxxx'),
            'dbUpgrade.db.user.password' => PES_DEVELOPMENT ? 'oa_upgrader' : (PES_PRODUCTION ? 'UPGRADE_PRODUCTION_USER_PASSWORD' : 'xxxx'),
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

            'login.db.account.everyone.name' => 'oa_login',  // nelze použít jméno uživatele použité pro db upgrade - došlo by k duplicitě jmen v build create
            'login.db.account.everyone.password' => 'oa_login',

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
            'web.db.account.everyone.name' => 'oa_everyone',
            'web.db.account.everyone.password' => 'oa_everyone',
            'web.db.account.authenticated.name' => 'oa_authenticated',
            'web.db.account.authenticated.password' => 'oa_authenticated',
            'web.db.account.administrator.name' => 'oa_administrator',
            'web.db.account.administrator.password' => 'oa_administrator',
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
            'rs.db.account.everyone.name' => 'oa_everyone',
            'rs.db.account.everyone.password' => 'oa_everyone',
            'rs.db.account.authenticated.name' => 'oa_authenticated',
            'rs.db.account.authenticated.password' => 'oa_authenticated',
            'rs.db.account.administrator.name' => 'oa_administrator',
            'rs.db.account.administrator.password' => 'oa_administrator',
            #
            ###################################

        ];
    }

    ### presentation ###
    #
    public static function statusPresentationManager() {
        return [
            'default_lang_code' => 'cs',
            'default_hierarchy_root_component_name' => 's'
        ];
    }

    public static function layoutControler() {
            $webPublicDir = \Middleware\Web\AppContext::getAppPublicDirectory();
            $webSitePublicDir = \Middleware\Web\AppContext::getAppSitePublicDirectory();
            $commonPublicDir = \Middleware\Web\AppContext::getPublicDirectory();
            $tinyPublicDir = \Middleware\Web\AppContext::getTinyPublicDirectory();

        $theme = 'oa';

        switch ($theme) {
            case 'old':
                $templatesLayout['layout'] = PROJECT_PATH.'public/web/site/grafia/layout/layout.php';
                $templatesLayout['linksJs'] = PROJECT_PATH.'public/web/site/grafia/layout/head/linkEditableJs.php';
                $templatesLayout['linksCss'] = PROJECT_PATH.'public/web/site/grafia/layout/head/linkEditableCss.php';
                $templatesLayout['tiny_config'] = PROJECT_PATH.'public/web/site/grafia/layout/head/tiny_config.js';
                break;
            case 'xhr':
                $templatesLayout['layout'] = PROJECT_PATH.'public/web/site/grafia/layoutXhr/layout.php';
                $templatesLayout['linksJs'] = PROJECT_PATH.'public/web/site/grafia/layoutXhr/head/linkEditableJs.php';
                $templatesLayout['linksCss'] = PROJECT_PATH.'public/web/site/grafia/layoutXhr/head/linkEditableCss.php';
                $templatesLayout['tiny_config'] = PROJECT_PATH.'public/web/site/grafia/layoutXhr/head/tiny_config.js';
                break;
            case 'new':
                $templatesLayout['layout'] = PROJECT_PATH.'public/web/site/newlayout/layout/layout.php';
                $templatesLayout['linksJs'] = PROJECT_PATH.'public/web/site/newlayout/layout/head/linkEditableJs.php';
                $templatesLayout['linksCss'] = PROJECT_PATH.'public/web/site/newlayout/layout/head/linkEditableCss.php';
                $templatesLayout['tiny_config'] = PROJECT_PATH.'public/web/site/newlayout/layout/head/tiny_config.js';
                break;
            case 'new1':
                $templatesLayout['layout'] = PROJECT_PATH.'public/web/site/newlayout_1/layout/layout.php';
                $templatesLayout['linksJs'] = PROJECT_PATH.'public/web/site/newlayout_1/layout/head/linkEditableJs.php';
                $templatesLayout['linksCss'] = PROJECT_PATH.'public/web/site/newlayout_1/layout/head/linkEditableCss.php';
                $templatesLayout['tiny_config'] = PROJECT_PATH.'public/web/site/newlayout_1/layout/head/tiny_config.js';
                break;
            case 'new2':
                $templatesLayout['layout'] = PROJECT_PATH.'public/web/site/newlayout_2/layout/layout.php';
                $templatesLayout['linksJs'] = PROJECT_PATH.'public/web/site/newlayout_2/layout/head/linkEditableJs.php';
                $templatesLayout['linksCss'] = PROJECT_PATH.'public/web/site/newlayout_2/layout/head/linkEditableCss.php';
                $templatesLayout['tiny_config'] = PROJECT_PATH.'public/web/site/newlayout_2/layout/head/tiny_config.js';
                break;
            case 'new3':
                $templatesLayout['layout'] = PROJECT_PATH.'public/web/site/newlayout_3/layout/layout.php';
                $templatesLayout['linksJs'] = PROJECT_PATH.'public/web/site/newlayout_3/layout/head/linkEditableJs.php';
                $templatesLayout['linksCss'] = PROJECT_PATH.'public/web/site/newlayout_3/layout/head/linkEditableCss.php';
                $templatesLayout['tiny_config'] = PROJECT_PATH.'public/web/site/newlayout_3/layout/head/tiny_config.js';
                break;
            case 'oa':
                $templatesLayout['layout'] = PROJECT_PATH.'public/web/site/oa/layout/layout.php';
                $templatesLayout['linksJs'] = PROJECT_PATH.'public/web/site/oa/layout/head/linkEditableJs.php';
                $templatesLayout['linksCss'] = PROJECT_PATH.'public/web/site/oa/layout/head/linkEditableCss.php';
                $templatesLayout['tiny_config'] = PROJECT_PATH.'public/web/site/oa/layout/head/tiny_config.js';
                break;
            default:
                $templatesLayout['layout'] = PROJECT_PATH.'public/web/site/grafia/layout/layout.php';
                $templatesLayout['linksJs'] = PROJECT_PATH.'public/web/site/grafia/layout/head/linkEditableJs.php';
                $templatesLayout['linksCss'] = PROJECT_PATH.'public/web/site/grafia/layout/head/linkEditableCss.php';
                $templatesLayout['tiny_config'] = PROJECT_PATH.'public/web/site/grafia/layout/head/tiny_config.js';
                break;
        }


        return [
          'templates.poznamky' => 'public/web/templates/info/poznamky.php',


           // Language packages tinyMce požívají krátké i dlouhé kódy, kód odpovídá jménu souboru např cs.js nebo en_US.js - proto mapování
            // pozn. - popisky šablon pro tiny jsou jen česky (TinyInit.js)
            'tinyLanguage' => [
                'cs' => 'cs',
                'de' => 'de',
                'en' => 'en_US'
            ],
            // title
            'title' => \Middleware\Web\AppContext::getWebTitle(),
            // folders
            'webPublicDir' => $webPublicDir,
            'webSitePublicDir' =>$webSitePublicDir,
            // layout folder
            'layout' => $templatesLayout['layout'],
            'tiny_config' =>    $templatesLayout['tiny_config'],
            // links do head
            'linksJs' =>    $templatesLayout['linksJs'],
            'linksCss' =>    $templatesLayout['linksCss'],
            // js links
           'urlTinyMCE' => $commonPublicDir.'tinymce5_3_1\js\tinymce\tinymce.min.js',
            'urlJqueryTinyMCE' => $commonPublicDir.'tinymce5_3_1\js\tinymce\jquery.tinymce.min.js',
//            'urlTinyMCE' => $commonPublicDir.'tinymce5_4_0\js\tinymce\tinymce.min.js',
//            'urlJqueryTinyMCE' => $commonPublicDir.'tinymce5_4_0\js\tinymce\jquery.tinymce.min.js',

//    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
//    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
//    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/jquery.tinymce.min.js" referrerpolicy="origin"></script>
            'urlTinyInit' => $webPublicDir.'js/TinyInit.js',
            'editScript' => $webPublicDir . 'js/edit.js',
            'kalendarScript' => $webPublicDir . 'js/kalendar.js',
            // css links
            'urlStylesCss' => $webPublicDir."styles/old/styles.css",
            'urlSemanticCss' => $webPublicDir."semantic/dist/semantic.min.css",
            'urlContentTemplatesCss' => $webPublicDir."templates/author/template.css",
            //
            'paperTemplatesUri' =>  $webPublicDir."templates/paper/",  // URI pro Template controler
            'contentTemplatesPath' => $webPublicDir."templates/author/",

        ];
    }

    public static function transformator() {
        return [
            'filesDirectory' => '/_www_oa_files/',  // relativní cesta vzhledem k DOCUMENT_ROOT (htdocs)
        ];
    }
}
