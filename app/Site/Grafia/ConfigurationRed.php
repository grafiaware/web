<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site\Grafia;

use Application\WebAppFactory;
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
class ConfigurationRed extends ConfigurationDb {

    ### bootstrap ###
    #
    public static function bootstrap() {
        return [
            'bootstrap_logs_base_path' => self::RED_BOOTSTRAP_LOGS,
        ];
    }

    ### kontejner ###
    #

    /**
     * Konfigurace kontejneru - vrací parametry pro AppContainerConfigurator
     * @return array
     */
    public static function app() {
        return [
            #################################
            # Konfigurace adresáře logů
            #
            'app.logs.directory' => 'Logs/App',
            #
            #################################

            #################################
            # Konfigurace session loggeru
            #
            WebAppFactory::SESSION_NAME_SERVICE => 'www_gr_session',
            'app.logs.session.file' => 'Session.log',
            #
            ##################################

            ##################################
            # Konfigurace router loggeru
            #
            'app.logs.router.file' => 'Router.log',
            #
            ##################################

            ##################################
            # Konfigurace selector loggeru
            #
            'app.logs.selector.file' => 'Selector.log',
            #
            ##################################
        ];
    }

    /**
     * Konfigurace kontejneru - vrací parametry pro ComponentContainerConfigurator
     * @return array
     */
    public static function component() {
        return [
            'component.logs.view.directory' => 'Logs/App/Web',
            'component.logs.view.file' => 'Render.log',
            'component.templatePath.paper' => self::RED_TEMPLATES_COMMON.'paper/',
            'component.template.'.FlashComponent::class => self::RED_TEMPLATES_COMMON.'layout/info/flashMessage.php',
            'component.template.'.LoginComponent::class => self::RED_TEMPLATES_COMMON.'layout/modal/login.php',
            'component.template.'.LogoutComponent::class => self::RED_TEMPLATES_COMMON.'layout/modal/logout.php',
            'component.template.'.UserActionComponent::class => self::RED_TEMPLATES_COMMON.'layout/modal/user_action.php',
        ];
    }

    ### presentation ###
    #

    /**
     * Konfigurace prezentace - vrací parametry pro statusPresentationManager
     * @return array
     */
    public static function statusPresentationManager() {
        return [
            'default_lang_code' => 'cs',
        ];
    }

    /**
     * Konfigurace prezentace - vrací parametry pro layoutControler
     * @return array
     */
    public static function layoutControler() {
        return [
           // Language packages tinyMce používají krátké i dlouhé kódy, kód odpovídá jménu souboru např cs.js nebo en_US.js - proto mapování
            // pozn. - popisky šablon pro tiny jsou jen česky (TinyInit.js)
            'tinyLanguage' => [
                    'cs' => 'cs',
                    'de' => 'de',
                    'en' => 'en_US'
                ],

            // title
            'title' => "Grafia, s.r.o.",

            // folders
            'linksCommon' => self::RED_LINKS_COMMON,
            'linksSite' => self::RED_LINKS_SITE,

            // local templates paths
            'layout' => self::RED_TEMPLATES_SITE.'layout/layout.php',
            'tiny_config' => self::RED_TEMPLATES_SITE.'js/tiny_config.js',
            'linksEditorJs' => self::RED_TEMPLATES_COMMON.'layout/links/linkEditorJs.php',
            'linkEditorCss' => self::RED_TEMPLATES_COMMON.'layout/links/linkEditorCss.php',

            // linksEditorJs links
            'urlTinyMCE' => self::RED_ASSETS.'tinymce5_3_1\js\tinymce\tinymce.min.js',
            'urlJqueryTinyMCE' => self::RED_ASSETS.'tinymce5_3_1\js\tinymce\jquery.tinymce.min.js',
//            'urlTinyMCE' => self::RED_ASSETS.'tinymce5_4_0\js\tinymce\tinymce.min.js',
//            'urlJqueryTinyMCE' => self::RED_ASSETS.'tinymce5_4_0\js\tinymce\jquery.tinymce.min.js',
//    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
//    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
//    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/jquery.tinymce.min.js" referrerpolicy="origin"></script>
            'urlTinyInit' => self::RED_LINKS_COMMON.'js/TinyInit.js',
            'urlEditScript' => self::RED_LINKS_COMMON . 'js/editDelegated.js',

            // linkEditorCss links
            'urlStylesCss' => self::RED_LINKS_COMMON."css/old/styles.css",
            'urlSemanticCss' => self::RED_LINKS_SITE."semantic-ui/semantic.min.css",
            'urlContentTemplatesCss' => self::RED_LINKS_COMMON."css/templates.css",   // KŠ ?????
            //
            'paperTemplatesUri' =>  self::RED_LINKS_SITE."templates/paper/",  // URI pro Template controler
            'authorTemplatesPath' => self::RED_LINKS_COMMON."templates/author/",

        ];
    }

    /**
     * Konfigurace prezentace - vrací parametry pro pageControler
     *
     * Home stránka může být definována jménem komponenty nebo jménem statické stránky nebo identifikátorem uid položky menu (položky hierarchie).
     *
     * @return array
     */
    public static function pageControler() {

        return [
               'home_page' => ['component', 'home'],
//               'home_page' => ['static', 'body-pro-zdravi'],
//               'home_page' => ['item', '5fad34398df10'],  // přednášky - pro test

               'templates.poznamky' => self::RED_TEMPLATES_COMMON.'layout/info/poznamky.php',
               'templates.loaderElement' => self::RED_TEMPLATES_COMMON.'layout/component-load/loaderElement.php',
               'templates.loaderElementEditable' => self::RED_TEMPLATES_COMMON.'layout/component-load/loaderElementEditable.php',
            ];
    }

    /**
     * Konfigurace prezentace - vrací parametry pro ComponentControler
     * @return array
     */
    public static function componentControler() {

        return [
               'static' => self::RED_TEMPLATES_SITE.'static/',

            ];
    }

    /**
     * Konfigurace prezentace - vrací parametry pro templateControler
     * @return array
     */
    public static function templateControler() {

        return [
               'templates.authorFolder' => self::RED_TEMPLATES_COMMON.'author/',
               'templates.paperFolder' => self::RED_TEMPLATES_COMMON.'paper/',
               'templates.paperContentFolder' => self::RED_TEMPLATES_COMMON.'paper-content/',

            ];
    }

    /**
     * Konfigurace upload files - vrací parametry pro FilesUploadControler
     * @return array
     */
    public static function filesUploadControler() {

        return [
            'filesDirectory' => PES_RUNNING_ON_PRODUCTION_HOST ? self::RED_FILES : self::RED_FILES,

            ];
    }

    /**
     * Konfigurace prezentačního objektu - vrací parametry pro transformator
     * @return array
     */
    public static function transformator() {
        return [
            // relativní cesta vzhledem k DOCUMENT_ROOT (htdocs) -začíná /
            'filesDirectory' => PES_RUNNING_ON_PRODUCTION_HOST ? self::RED_FILES : '/'.self::RED_FILES,
            'public' => self::RED_LINKS_COMMON,
        ];
    }
}
