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
    RegisterComponent,
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
            'bootstrap.logs.basepath' => self::RED_BOOTSTRAP_LOGS,
            'bootstrap.productionhost' => self::RED_BOOTSTRAP_PRODUCTION_HOST,
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
     *
     * Konfiguruje logování a šablony pro komponenty, které renderují šablony
     *
     * @return array
     */
    public static function component() {
        return [
            'component.logs.directory' => 'Logs/App/Web',
            'component.logs.render' => 'Render.log',
            'component.templatepath.paper' => self::RED_TEMPLATES_COMMON.'paper/',
            'component.templatepath.article' => self::RED_TEMPLATES_COMMON.'article/',
            'component.templatepath.multipage' => self::RED_TEMPLATES_COMMON.'multipage/',
            'component.templatepath.author' => self::RED_LINKS_COMMON."author/",
            // common layout templates
            'component.template.flash' => self::RED_TEMPLATES_COMMON.'layout/info/flashMessage.php',
            'component.template.login' => self::RED_TEMPLATES_COMMON.'layout/status/login.php',
            'component.template.register' => self::RED_TEMPLATES_COMMON.'layout/status/register.php',
            'component.template.logout' => self::RED_TEMPLATES_COMMON.'layout/status/logout.php',
            'component.template.useraction' => self::RED_TEMPLATES_COMMON.'layout/status/userAction.php',
            'component.template.statusboard' => self::RED_TEMPLATES_COMMON.'layout/info/statusBoard.php',
            'component.template.controleditmenu' => self::RED_TEMPLATES_COMMON.'layout/status/controlEditMenu.php',

        ];
    }

    ### presentation ###
    #

    /**
     * Konfigurace prezentace - vrací parametry pro statusPresentationManager
     *
     * Konfiguruje výchozí jazyk webu.
     *
     * @return array
     */
    public static function presentationStatus() {
        return [
            'default_lang_code' => 'cs',
            'accepted_languages' => ['cs', 'de', 'en']
        ];
    }

    /**
     * Konfigurace prezentace - vrací parametry pro layoutController
     * @return array
     */
    public static function layoutController() {
        return [
           // Language packages tinyMce používají krátké i dlouhé kódy, kód odpovídá jménu souboru např cs.js nebo en_US.js - proto mapování
            // pozn. - popisky šablon pro tiny jsou jen česky (TinyInit.js)
            'tinyLanguage' => [
                    'cs' => 'cs',
                    'de' => 'de',
                    'en' => 'en_US'
                ],

            // title
            'title' => "Grafia",

            // folders
            'linksCommon' => self::RED_LINKS_COMMON,
            'linksSite' => self::RED_LINKS_SITE,

            // local templates paths
            'layout' => self::RED_TEMPLATES_SITE.'layout/layout.php',
            'tinyConfig' => self::RED_TEMPLATES_COMMON.'js/tinyConfig.js',
            'scriptsEditableMode' => self::RED_TEMPLATES_COMMON.'layout/head/scriptsEditableMode.php',

            // linksEditorJs links
            'urlTinyMCE' => self::RED_ASSETS.'tinymce5_3_1\js\tinymce\tinymce.min.js',
            'urlJqueryTinyMCE' => self::RED_ASSETS.'tinymce5_3_1\js\tinymce\jquery.tinymce.min.js',
//            'urlTinyMCE' => self::RED_ASSETS.'tinymce5_4_0\js\tinymce\tinymce.min.js',
//            'urlJqueryTinyMCE' => self::RED_ASSETS.'tinymce5_4_0\js\tinymce\jquery.tinymce.min.js',
//    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
//    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
//    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/jquery.tinymce.min.js" referrerpolicy="origin"></script>
            'urlTinyInit' => self::RED_LINKS_COMMON.'js/TinyInit.js',
            'urlEditScript' => self::RED_LINKS_COMMON . 'js/edit.js',

            // linkEditorCss links
            'urlStylesCss' => self::RED_LINKS_COMMON."css/old/styles.css",
            'urlSemanticCss' => self::RED_LINKS_SITE."semantic-ui/semantic.min.css",
            'urlContentTemplatesCss' => self::RED_LINKS_COMMON."css/templates.css",   // KŠ ?????
        ];
    }

    /**
     * Konfigurace prezentace - vrací parametry pro pageController
     *
     * Definuje domácí (home) stránku webu.
     * Home stránka může být definována jménem komponenty nebo jménem statické stránky nebo identifikátorem uid položky menu (položky hierarchie).
     *
     * @return array
     */
    public static function pageController() {

        return [
               'home_page' => ['block', 'home'],
//               'home_page' => ['item', '5fad34398df10'],  // přednášky - pro test

//      'context_name' => jméno proměnné v šabloně, 'service_name' => jméno služby component kontejneru, 'root_name' => jméno v db tabulce root_name, 'with_title' => bool hodnota - true - zobrazuje se i obsah kořenového prvku menu],
                'menu' => [
                    ['context_name' => 'menuPresmerovani', 'service_name' => 'menu.presmerovani', 'root_name' => 'menu_redirect', 'with_title' => false],
                    ['context_name' => 'menuVodorovne', 'service_name' => 'menu.vodorovne', 'root_name' => 'menu_horizontal', 'with_title' => false],
                    ['context_name' => 'menuSvisle', 'service_name' => 'menu.svisle', 'root_name' => 'menu_vertical', 'with_title' => false],
                ],
                'blocks' =>  ['context_name' => 'bloky', 'service_name' => 'menu.bloky', 'root_name' => 'blocks', 'with_title' => true],
                'trash' => ['context_name' => 'kos', 'service_name' => 'menu.kos', 'root_name' => 'trash', 'with_title' => true],

               'templates.poznamky' => self::RED_TEMPLATES_COMMON.'layout/info/poznamky.php',
               'templates.loaderElement' => self::RED_TEMPLATES_COMMON.'layout/component-load/loaderElement.php',
               'templates.loaderElementEditable' => self::RED_TEMPLATES_COMMON.'layout/component-load/loaderElementEditable.php',
                'context_name_to_block_name_map' => [
                    'rychleOdkazy' => 'a3',
                    'nejblizsiAkce' => 'a2',
                    'aktuality' => 'a1',
                    'razitko' => 'a4',
                    'socialniSite' => 'a5',
                    'mapa' => 'a6',
                    'logo' => 'a7',
                    'banner' => 'a8',
                ]
            ];
    }

    public static function loginLogoutController() {
        ## HESLO - malé velké písmeno, číslice, min. 5 znaků
        $passwordPattern = "(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{5,}";
        $passwordInfo = "Heslo musí obsahovat nejméně jedno velké písmeno, jedno malé písmeno a jednu číslici. Jiné znaky než písmena a číslice nejsou povoleny. Délka musí být nejméně 5 znaků.";

        ## HESLO - malé velké písmeno, číslice, min. 3 znaky
//        $passwordPattern = "(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{3,}";
//        $passwordInfo = "Heslo musí obsahovat nejméně jedno velké písmeno, jedno malé písmeno a jednu číslici. Jiné znaky než písmena a číslice nejsou povoleny. Délka musí být nejméně 3 znaky.";

        $siteSpecificToken = str_replace('/', '', self::RED_SITE_PATH);
        return [
                'fieldNameJmeno' => 'jmeno'.$siteSpecificToken,
                'fieldNameHeslo' => 'heslo'.$siteSpecificToken,
                'passwordPattern' => $passwordPattern,
                'passwordInfo' => $passwordInfo,
                'roleVisitor' => 'visitor',
                'rolePresenter' => 'presenter',
        ];
    }

    /**
     * Konfigurace prezentace - vrací parametry pro ComponentController
     * @return array
     */
    public static function componentController() {

        return [
                'templates' => self::RED_TEMPLATES_SITE,
                'static' => self::RED_STATIC,
                'compiled' => self::RED_STATIC.'__compiled/',
                'presenterFiles' => PES_RUNNING_ON_PRODUCTION_HOST ? self::RED_FILES."presenter/" : self::RED_FILES."presenter/",
                'prettyUrlCallable' => function($nadpis) {
                        $url = $nadpis;
                        $url = preg_replace('~[^\\pL0-9_]+~u', '-', $url);
                        $url = trim($url, "-");
                        $url = iconv("utf-8", "us-ascii//TRANSLIT", $url);
                        $url = strtolower($url);
                        $url = preg_replace('~[^-a-z0-9_]+~', '', $url);
                        return $url;
                    }
            ];
    }

    /**
     * Konfigurace prezentace - vrací parametry pro templateController
     * @return array
     */
    public static function templateController() {

        return [
                'default_template_file_name' => 'template.php',
                'templates.authorFolder' => self::RED_TEMPLATES_COMMON.'author/',

                'templates.paperContentFolder' => self::RED_TEMPLATES_COMMON.'paper-content/',  // nepoužito
                // pole složek, jsou prohledávány postupně při hledání souboru s šablonou zadaného názvu
                'templates.articleFolder' => [
                   self::RED_TEMPLATES_SITE.'article/',
                   self::RED_TEMPLATES_COMMON.'article/',
                   ],
                'templates.paperFolder' => [
                   self::RED_TEMPLATES_SITE.'paper/',
                   self::RED_TEMPLATES_COMMON.'paper/',
                   ],
                'templates.multipageFolder' => [
                   self::RED_TEMPLATES_SITE.'multipage/',
                   self::RED_TEMPLATES_COMMON.'multipage/',
                   ],
            ];
    }

    /**
     * Konfigurace upload files - vrací parametry pro FilesUploadController
     * @return array
     */
    public static function filesUploadController() {

        return [
            'upload.red' => PES_RUNNING_ON_PRODUCTION_HOST ? self::RED_FILES.'upload/editor/' : self::RED_FILES.'upload/editor/',
            'upload.events.visitor' => PES_RUNNING_ON_PRODUCTION_HOST ? self::RED_FILES.'upload/events/visitor' : self::RED_FILES.'upload/events/visitor',
            'upload.events.acceptedextensions' => [".doc", ".docx", ".dot", ".odt", "pages", ".xls", ".xlsx", ".ods", ".txt", ".pdf"],
            ];
    }

    /**
     * Konfigurace prezentačního objektu - vrací parametry pro transformator
     * @return array
     */
    public static function transformator() {
        return [
            'publicDirectory' => self::RED_LINKS_COMMON,
            'siteDirectory' => self::RED_LINKS_SITE,
        ];
    }

    public static function mail() {
        return [
            'mail.logs.directory' => 'Logs/Mail',
            'mail.logs.file' => 'Mail.log',
            'mail.paramsname' => 'itGrafiaGmail', // 'najdisi',
            'mail.attachments' => PES_RUNNING_ON_PRODUCTION_HOST ? self::RED_FILES_PATH.'attachments/' : self::RED_FILES_PATH.'attachments/',

        ];
}

    public static function files() {
        return [
            '@download' => PES_RUNNING_ON_PRODUCTION_HOST ? self::RED_FILES.'download/' : self::RED_FILES.'download/',
            '@images' => PES_RUNNING_ON_PRODUCTION_HOST ? self::RED_FILES.'images/' : self::RED_FILES.'images/',
            '@movies' => PES_RUNNING_ON_PRODUCTION_HOST ? self::RED_FILES.'movies/' : self::RED_FILES.'movies/',
            'files' => PES_RUNNING_ON_PRODUCTION_HOST ? self::RED_FILES.'files/' : self::RED_FILES.'files/',

        ];
    }

}
