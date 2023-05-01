<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site\VeletrhPrace;

use Application\WebAppFactory;
use Red\Component\View\Generated\LanguageSelectComponent;
use Red\Component\View\Generated\SearchPhraseComponent;
use Web\Component\View\Flash\FlashComponent;
use Auth\Component\View\LoginComponent;
use Auth\Component\View\LogoutComponent;
use Auth\Component\View\RegisterComponent;
use Red\Component\View\Manage\UserActionComponent;
use Red\Component\View\Manage\StatusBoardComponent;

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
            WebAppFactory::SESSION_NAME_SERVICE => 'www_vp_session',
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
    public static function webComponent() {
        return [
            'webcomponent.logs.directory' => 'Logs/App/Web',
            'webcomponent.logs.render' => 'Render.log',
            'webcomponent.templates' =>
                [

                ]
        ];
    }

    /**
     * Konfigurace kontejneru - vrací parametry pro ComponentContainerConfigurator
     *
     * Konfiguruje logování a šablony pro komponenty, které renderují šablony
     *
     * @return array
     */
    public static function redComponent() {
        return [
            'redcomponent.logs.directory' => 'Logs/App/Red',
            'redcomponent.logs.render' => 'Render.log',
            'redcomponent.templates' => [
                'flash' => self::RED_TEMPLATES_COMMON.'layout/info/flashMessages.php',
                'login' => self::RED_TEMPLATES_COMMON.'layout/status/login.php',
//                'register' => self::RED_TEMPLATES_COMMON.'layout/status/register.php',  // nahrazeno - site layout templates
                'logout' => self::RED_TEMPLATES_COMMON.'layout/status/logout.php',
                'useraction' => self::RED_TEMPLATES_COMMON.'layout/status/userAction.php',
                'statusboard' => self::RED_TEMPLATES_COMMON.'layout/info/statusBoard.php',
                'controleditmenu' => self::RED_TEMPLATES_COMMON.'layout/status/controlEditMenu.php',
                // site layout templates
                'register' => self::RED_TEMPLATES_SITE.'layout/status/register-with-exhibitor-representative.php',
            ]
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
            'accepted_languages' => ['cs']
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
//                    'de' => 'de',
//                    'en' => 'en_US'
                ],

            // title
            'title' => "Veletrh práce online",

            // folders
            'linksCommon' => self::RED_LINKS_COMMON,
            'linksSite' => self::RED_LINKS_SITE,

            // local templates paths
            // php templates
            'templates.layout' => self::RED_TEMPLATES_SITE.'layout/layout.php',
            'templates.redScripts' => self::RED_TEMPLATES_COMMON.'layout/head/redScripts.php',
            // js templates
            'templates.navConfig' => self::RED_TEMPLATES_COMMON.'js/navConfig.js',
            'templates.tinyConfig' => self::RED_TEMPLATES_COMMON.'js/tinyConfig.js',

            // linksEditorJs links
            'urlTinyMCE' => self::RED_ASSETS.'tinymce5_3_1\js\tinymce\tinymce.min.js',
            'urlJqueryTinyMCE' => self::RED_ASSETS.'tinymce5_3_1\js\tinymce\jquery.tinymce.min.js',
//            'urlTinyMCE' => self::RED_ASSETS.'tinymce5_4_0\js\tinymce\tinymce.min.js',
//            'urlJqueryTinyMCE' => self::RED_ASSETS.'tinymce5_4_0\js\tinymce\jquery.tinymce.min.js',
//    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
//    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
//    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/jquery.tinymce.min.js" referrerpolicy="origin"></script>
            'urlRedConfig' => self::RED_LINKS_COMMON.'js/redConfig.js',
            'urltinyConfig' => self::RED_LINKS_COMMON.'js/tinyConfig.js',
            'urlTinyInit' => self::RED_LINKS_COMMON.'js/tinyInit.js',
            'urlEditScript' => self::RED_LINKS_COMMON . 'js/edit.js',

            // linkEditorCss links
            'urlStylesCss' => self::RED_LINKS_COMMON."css/old/styles.css",
            'urlSemanticCss' => self::RED_LINKS_SITE."semantic-ui/semantic.min.css",
            'urlContentTemplatesCss' => self::RED_LINKS_COMMON."css/templates.css",
            'urlMediaCss' => self::RED_LINKS_COMMON."css/media.css",
            // home page
            'home_page' => ['block', 'home'],
//           'home_page' => ['item', '5fad34398df10'],  // přednášky - pro test

            'templates.poznamky' => self::RED_TEMPLATES_COMMON.'layout/info/poznamky.php',
            'templates.loaderElement' => self::RED_TEMPLATES_COMMON.'layout/cascade/loaderElement.php',
            'templates.unknownContent' => self::RED_TEMPLATES_COMMON.'layout/error/unknownContent.php',
            'layout_blocks' => [
                ],
            // hodnoty RequestCache pro hlavičky requestů odesílaných příkazem fetch v cascade.js
            // viz https://developer.mozilla.org/en-US/docs/Web/API/Request/cache
            'cascade.class' => 'cascade',
            'cascade.cacheReloadOnNav' => 'reload',
            'cascade.cacheLoadOnce' => 'default',

            // parametry kontext - service mapy jsou:
            //'context_name' => 'service_name'
            //      'context_name' - jméno proměnné v šabloně (bez znaku $),
            //      'service_name' => jméno služby component kontejneru,
            'contextServiceMap' => [
                    'flash' => FlashComponent::class,
                    'modalLogin' => LoginComponent::class,
                    'modalLogout' => LogoutComponent::class,
                    'modalRegister' => RegisterComponent::class,
                    'modalUserAction' => UserActionComponent::class,
                    'poznamky' => StatusBoardComponent::class,
                ],
            'contextLayoutMap' => [
                    'menuSvisle' => 'menu.svisle',
                    'bloky' => 'menu.bloky',
                    'kos' => 'menu.kos',
                ],
            ];
    }
    public static function menu() {
            // menu
            // 'jméno služby kontejneru' => [pole parametrů menu]
            // 'jméno služby kontejneru' - jmébo služby kontejneru, která vrací příslušný menu komponent
            // parametry menu jsou:
            //      'context_name' => jméno proměnné v šabloně (bez znaku $),
            //      'service_name' => jméno služby component kontejneru,
            //      'root_name' => jméno kořene menu v db tabulce root_name,
            //      'with_rootItem' => bool hodnota - true - zobrazuje se i obsah kořenového prvku menu,
            //      'itemtype! => jedna z hodnot 'menu', 'block', 'trash' - určuje výběr rendereru menu item
            //      'menuwraprenderer' => jméno rendereru obalového elementu menu
            //      'levelwraprenderer' => jméno rendereru jedné úrovně menu
        return [
            'menu.componentsServices' => [
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
                'roleRepresentative' => 'representative',
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
    public static function templates() {

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
            'presenter' => PES_RUNNING_ON_PRODUCTION_HOST ? self::RED_FILES_SITE."presenter/" : self::RED_FILES_SITE."presenter/",

        ];
    }

}
