<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site\Grafia;

use Application\WebAppFactory;
use Component\View\Flash\FlashComponent;
use Component\View\Manage\{
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
            'component.template.flash' => self::RED_TEMPLATES_COMMON.'layout/info/flashMessages.php',
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

            // home page
            'home_page' => ['block', 'home'],
//           'home_page' => ['item', '5fad34398df10'],  // přednášky - pro test

            'templates.poznamky' => self::RED_TEMPLATES_COMMON.'layout/info/poznamky.php',
            'templates.loaderElement' => self::RED_TEMPLATES_COMMON.'layout/component-load/loaderElement.php',
            'templates.loaderElementEditable' => self::RED_TEMPLATES_COMMON.'layout/component-load/loaderElementEditable.php',
            'layout_blocks' => [
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
    public static function menu() {
            // menu
            // 'jméno služby kontejneru' => [pole parametrů menu]
            // 'jméno služby kontejneru' - jmébo služby kontejneru, která vrací příslušný menu komponent
            // parametry menu jsou:
            //      'rootName' => jméno kořene menu v db tabulce rootName,
            //      'withRootItem' => bool hodnota - true - zobrazuje se i obsah kořenového prvku menu,
            //      'itemtype! => jedna z hodnot 'onelevel', 'multilevel', 'trash' - určuje výběr rendereru menu item
            //      'levelRenderer' => jméno služby pro získání rendereru jedné úrovně menu - konfigurace je v ConfigurationStyles, tam se předávají příslušné ClassMap
        return [
            'menu.componentsServices' => [
                    'menu.presmerovani' => [
                        'rootName' => 'menu_redirect',
                        'withRootItem' => false,
                        'itemtype' => 'onelevel',
                        'levelRenderer' => 'menu.presmerovani.levelRenderer',
                        ],
                    'menu.vodorovne' => [
                        'rootName' => 'menu_horizontal',
                        'withRootItem' => false,
                        'itemtype' => 'onelevel',
                        'levelRenderer' => 'menu.vodorovne.levelRenderer',
                        ],
                    'menu.svisle' => [
                        'rootName' => 'menu_vertical',
                        'withRootItem' => false,
                        'itemtype' => 'multilevel',
                        'levelRenderer' => 'menu.svisle.levelRenderer',
                        ],
                    'menu.bloky' => [
                        'rootName' => 'blocks',
                        'withRootItem' => true,
                        'itemtype' => 'onelevel',
                        'levelRenderer' => 'menu.bloky.levelRenderer',
                        ],
                    'menu.kos' => [
                        'rootName' => 'trash',
                        'withRootItem' => true,
                        'itemtype' => 'trash',
                        'levelRenderer' => 'menu.kos.levelRenderer',
                        ],
                ],
            // parametry kontext - service mapy jsou:
            //'context_name' => 'service_name'
            //      'context_name' - jméno proměnné v šabloně (bez znaku $),
            //      'service_name' => jméno služby component kontejneru,
            'menu.contextServiceMap' => [
                    'menuPresmerovani' => 'menu.presmerovani',
                    'menuVodorovne' => 'menu.vodorovne',
                    'menuSvisle' => 'menu.svisle',
                    'bloky' => 'menu.bloky',
                    'kos' => 'menu.kos',
                ],
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
            ];
    }

    /**
     * Konfigurace - parametry pro templateController
     * @return array
     */
    public static function templateController() {

        return [
                'templates.defaultExtension' => '.php',
                // pole složek, jsou prohledávány postupně při hledání souboru s šablonou zadaného typu
                'templates.folders' => [
                    'author'=>[self::RED_TEMPLATES_COMMON.'author/'],   //jen v common
                    'paper' => [self::RED_TEMPLATES_SITE.'paper/', self::RED_TEMPLATES_COMMON.'paper/'],
                    'article' => [self::RED_TEMPLATES_SITE.'article/', self::RED_TEMPLATES_COMMON.'article/'],
                    'multipage' => [self::RED_TEMPLATES_SITE.'multipage/', self::RED_TEMPLATES_COMMON.'multipage/'],
                    ],
            ];
    }

    /**
     * Konfigurace upload files - vrací parametry pro FilesUploadController
     * @return array
     */
    public static function filesUploadController() {

        return [
            'upload.red' => PES_RUNNING_ON_PRODUCTION_HOST ? self::RED_FILES_SITE.'upload/editor/' : self::RED_FILES_SITE.'upload/editor/',
            'upload.events.visitor' => PES_RUNNING_ON_PRODUCTION_HOST ? self::RED_FILES_SITE.'upload/events/visitor' : self::RED_FILES_SITE.'upload/events/visitor',
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
            '@download' => PES_RUNNING_ON_PRODUCTION_HOST ? self::RED_FILES_SITE.'download/' : self::RED_FILES_SITE.'download/',
            '@commonimages' => PES_RUNNING_ON_PRODUCTION_HOST ? self::RED_FILES_COMMON.'images/' : self::RED_FILES_COMMON.'images/',
            '@commonmovies' => PES_RUNNING_ON_PRODUCTION_HOST ? self::RED_FILES_COMMON.'movies/' : self::RED_FILES_COMMON.'movies/',
            '@siteimages' => PES_RUNNING_ON_PRODUCTION_HOST ? self::RED_FILES_SITE.'images/' : self::RED_FILES_SITE.'images/',
            '@sitemovies' => PES_RUNNING_ON_PRODUCTION_HOST ? self::RED_FILES_SITE.'movies/' : self::RED_FILES_SITE.'movies/',

            'files' => PES_RUNNING_ON_PRODUCTION_HOST ? self::RED_FILES_SITE.'files/' : self::RED_FILES_SITE.'files/',

        ];
    }

}
