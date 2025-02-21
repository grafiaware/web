<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site\Grafia;

use Application\WebAppFactory;
use Red\Component\View\Generated\LanguageSelectComponent;
use Red\Component\View\Generated\SearchPhraseComponent;
use Web\Component\View\Flash\FlashComponent;
use Auth\Component\View\LoginComponent;
use Auth\Component\View\LogoutComponent;
use Auth\Component\View\RegisterComponent;
use Red\Component\View\Manage\EditorActionComponent;
use Events\Component\View\Manage\RepresentativeActionComponent;
use Red\Component\View\Manage\InfoBoardComponent;

use Red\Component\ViewModel\Menu\Enum\ItemTypeEnum;

use Pes\Logger\FileLogger;

/**
 * Description of Configuration
 *
 * @author pes2704
 */
class ConfigurationWeb extends ConfigurationConstants {

    // local
    const RED_TEMPLATES_COMMON = 'local/site/common/templates/red/';
    const RED_TEMPLATES_SITE = 'local/site/'.self::WEB_SITE.'templates/red/';
    const RED_STATIC = 'local/site/'.self::WEB_SITE.'static/';
    
    ### bootstrap ###
    #
    public static function bootstrap() {
        return [
            'bootstrap.logs.basepath' => self::WEB_BOOTSTRAP_LOGS,
            'bootstrap.productionhost' => self::WEB_BOOTSTRAP_PRODUCTION_HOST,
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
            'app.logs.type' => FileLogger::REWRITE_LOG,
            #
            #################################

            #################################
            # Konfigurace session loggeru
            #
            WebAppFactory::SESSION_NAME_SERVICE => 'www_gr_session',
            'app.logs.session.file' => 'Session.log',
            'app.logs.session.type' => FileLogger::REWRITE_LOG,
            #
            ##################################

            ##################################
            # Konfigurace router loggeru
            #
            'app.logs.router.file' => 'Router.log',
            'app.logs.router.type' => FileLogger::APPEND_TO_LOG,
            #
            ##################################

            ##################################
            # Konfigurace selector loggeru
            #
            'app.logs.selector.file' => 'Selector.log',
            'app.logs.selector.type' => FileLogger::APPEND_TO_LOG,
            #
            ##################################
            ##################################
            # Konfigurace noMatchedRouteLogger loggeru
            #
            'app.logs.nomatch.file' => 'NoMatchedRoute.log',
            'app.logs.nomatch.type' => FileLogger::APPEND_TO_LOG,
            #
            ##################################
            #
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
            'webcomponent.logs.directory' => 'Logs/Web',
            'webcomponent.logs.render' => 'Render.log',
            'webcomponent.logs.type' => FileLogger::REWRITE_LOG,
            'webcomponent.templates' =>
                [

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
            'title' => "Grafia s.r.o.",

            // folders
            'linksCommon' => self::WEB_LINKS_COMMON,
            'linksSite' => self::WEB_LINKS_SITE,

            // version - postfix ke jménům souborů typu css, jss pro zablokování cachování ve fázi vývoje
            'version' => '',  //?version='.time(), //
            
            // local templates paths
            // php templates
            'templates.layout' => self::WEB_TEMPLATES_SITE.'layout/layout.php',
            'templates.redScripts' => self::RED_TEMPLATES_COMMON.'layout/head/redScripts.php',
            // js templates
            'templates.navConfig' => self::WEB_TEMPLATES_COMMON.'js/navConfig.js',  //???
            'templates.tinyConfig' => self::RED_TEMPLATES_COMMON.'js/tinyConfig.js',

            // linksEditorJs links
            'urlTinyMCE' => self::WEB_ASSETS.'tinymce\js\tinymce\tinymce.min.js',  // tinymce_6.6.1 minified version
            // full dev not mified version:
//            'urlTinyMCE' => self::WEB_ASSETS.'tinymce_6.6.1_dev\js\tinymce\tinymce.js',
            'urlJqueryTinyMCE' => self::WEB_ASSETS.'tinymce-jquery.min.js',         // pro tinyMce 6.6.1   
            // full dev not mified version:
//            'urlJqueryTinyMCE' => self::WEB_ASSETS.'tinymce-jquery.js',         // pro tinyMce 6.6.1   
//            'urlTinyMCE' => "https://cdn.tiny.cloud/1/no-api-key/tinymce/5/jquery.tinymce.min.js",

            'urlTinyInit' => self::WEB_LINKS_COMMON.'js/tinyInit.js',
            'urlEditScript' => self::WEB_LINKS_COMMON . 'js/edit.js',

            // linkEditorCss links
            'urlStylesCss' => self::WEB_LINKS_COMMON."css/old/styles.css",
            'urlSemanticCss' => self::WEB_LINKS_SITE."semantic-ui/semantic.min.css",
            'urlContentTemplatesCss' => self::WEB_LINKS_COMMON."css/templates.css",
            'urlMediaCss' => self::WEB_LINKS_COMMON."css/media.css",
            // home page
            'home_page' => ['block', 'home'],
//           'home_page' => ['item', '5fad34398df10'],  // přednášky - pro test

            'templates.poznamky' => self::WEB_TEMPLATES_COMMON.'layout/info/poznamky.php',
            'templates.loaderElement' => self::WEB_TEMPLATES_COMMON.'layout/cascade/loaderElement.php',
            'templates.unknownContent' => self::WEB_TEMPLATES_COMMON.'layout/error/unknownContent.php',

            // hodnoty RequestCache pro hlavičky requestů odesílaných příkazem fetch v cascade.js
            // viz https://developer.mozilla.org/en-US/docs/Web/API/Request/cache
            'cascade.class' => 'cascade',
            // "reload" – don’t take the result from HTTP-cache (if any), but populate the cache with the response (if the response headers permit this action),
            'cascade.cacheReloadOnNav' => 'reload',
            // "default" – fetch uses standard HTTP-cache rules and headers,
            'cascade.cacheLoadOnce' => 'default',
            'apiaction.class' => 'apiaction',
            
            // mapování komponent na proměnné kontextu v šablonách
            // contextLayoutMap - mapa komponent načtených pouze jednou při načtení webu a cachovaných - viz parametr 'cascade.cacheLoadOnce'
            // contextServiceMap - mapy komponent, které budou v editačním modu načítány vždy znovu novým requestem - viz parametr 'cascade.cacheReloadOnNav'
            // parametry kontext - service/layout mapy jsou:
            //'contextName' => 'service_name'
            //      'contextName' = jméno proměnné v šabloně (bez znaku $, jméno kontextu abcd odpovídá proměnné v PHP šabloně $abcd), bude současně použit jako část URL (API path)
            //      'service_name' = jméno služby component kontejneru
            // Pro 'contextName' použijte jako bezpečné jméno v camel case notaci začínající písmenem, složené z písmen a číslic. 
            // Toto jméno odpovídá jménu proměnné v PHP šabloně (bez znaku $) a tím je dáno, že smí obsahovat jen písmena a číslice, ale je case-sensitive. 
            // Navíc však bude použito jako část API path (api uri), např. 'red/v1/component/menuVlevo', URL je case-insensitive a může docházet ke kódování znaků.
            
            //  název proměnné v šabloně => název služby v konteneru (obvykle název třídy komponentu)
            'contextServiceMap' => [
                    'flash' => FlashComponent::class,
                    'modalLogin' => LoginComponent::class,
                    'modalLogout' => LogoutComponent::class,
                    'modalRegister' => RegisterComponent::class,
                    'modalUserAction' => EditorActionComponent::class,
                    'info' => InfoBoardComponent::class,
                    'languageSelect'=> LanguageSelectComponent::class,
                    'searchPhrase'=> SearchPhraseComponent::class,
                ],
            //  název proměnné v šabloně => název služby v konteneru (obvykle název menu komponentu jako string)
            'contextLayoutMap' => [
                    'menuSvisle' => 'menuVertical',
                    'menuVodorovne' => 'menuHorizontal',
                    'menuPresmerovani' => 'menuRedirect',
                ],
            //  název proměnné v šabloně => název služby v konteneru (obvykle název menu komponentu jako string)
            'contextLayoutEditableMap' => [
                    'bloky' => 'menuBlocks',
                    'kos' => 'menuTrash',
                ],
            //  název proměnné v šabloně => hodnota targetId příslušná k menu z položky 'contextMenuMap'
            'contextTargetMap' => [
                    'content'=>['id'=>'menusvisle_target'],  
                ],
            'contextMenuMap' => [
                    'menuSvisle' => ['service'=>'menuVertical', 'targetContext'=>'content'],
                    'menuVodorovne' => ['service'=>'menuHorizontal', 'targetContext'=>'content'],
                    'menuPresmerovani' => ['service'=>'menuRedirect', 'targetContext'=>'content'],
                ],
            'contextMenuEditableMap' => [
                    'bloky' => ['service'=>'menuBlocks', 'targetId'=>'menutarget_content'],
                    'kos' => ['service'=>'menuTrash', 'targetId'=>'menutarget_content'],
                ],
            'contextBlocksMap' => [
                'aktuality'=>'a1',
                'nejblizsiAkce'=>'a2',
                'rychleOdkazy'=>'a3',
                'razitko'=>'a4',
                'socialniSite'=>'a5',
                ],            
            ];
    }
    public static function menu() {
            // menu
            // 'jméno služby kontejneru' => [pole parametrů menu]
            // 'jméno služby kontejneru' - jmébo služby kontejneru, která vrací příslušný menu komponent
            // parametry menu jsou:
            //      'rootName' => jméno kořene menu v db tabulce root_name,
            //      'itemtype' => jedna z hodnot ItemTypeEnum - určuje výběr rendereru menu item
            //      'levelRenderer' => jméno rendereru pro renderování "úrovně menu" - rodičovského view, který obaluje jednotlivé item view
        return [
            'menu.services' => [
                    '' => [
                        'rootName' => 'menu_redirect',
                        'itemtype' => ItemTypeEnum::ONELEVEL,
                        'levelRenderer' => 'menuRedirect.levelRenderer',
                        'levelRendererEditable' => 'menuRedirect.levelRenderer.editable',
                        ],
                    'menuHorizontal' => [
                        'rootName' => 'menu_horizontal',
                        'itemtype' => ItemTypeEnum::ONELEVEL,
                        'levelRenderer' => 'menuHorizontal.levelRenderer',
                        'levelRendererEditable' => 'menuHorizontal.levelRenderer.editable',
                        ],
                    'menuVertical' => [
                        'rootName' => 'menu_vertical',
                        'itemtype' => ItemTypeEnum::MULTILEVEL,
                        'levelRenderer' => 'menuVertical.levelRenderer',
                        'levelRendererEditable' => 'menuVertical.levelRenderer.editable',
                        ],
                    'menuBlocks' => [
                        'rootName' => 'blocks',
                        'itemtype' => ItemTypeEnum::ONELEVEL,
                        'levelRenderer' => 'menuBlocks.levelRenderer',
                        'levelRendererEditable' => 'menuVertical.levelRenderer.editable',
                        ],
                    'menuTrash' => [
                        'rootName' => 'trash',
                        'itemtype' => ItemTypeEnum::TRASH,
                        'levelRenderer' => 'menuTrash.levelRenderer',
                        'levelRendererEditable' => 'menuVertical.levelRenderer.editable',
                        ],
                ],

            ];
    }

    /**
     * Konfigurace prezentačního objektu - vrací parametry pro transformator
     * @return array
     */
    public static function transformator() {
        return [
            'replace' => [
                'template substitutions',
                'slots',
                'rs substitutions',
                'rs list urls'
            ],
            'publicDirectory' => self::WEB_LINKS_COMMON,
            'siteDirectory' => self::WEB_LINKS_SITE,
            'filesDirectory' => PES_RUNNING_ON_PRODUCTION_HOST ? self::WEB_FILES_SITE.'files/' : self::WEB_FILES_SITE.'files/',
        ];
    }

    public static function mail() {
        return [
            'mail.logs.directory' => 'Logs/Mail',
            'mail.logs.file' => 'Mail.log',
            'mail.paramsname' => 'grafiaInterni', //'najdisi', //'itGrafiaGmail', // 
            'mail.attachments' => PES_RUNNING_ON_PRODUCTION_HOST ? self::WEB_FILES_SITE.'attachments/' : self::WEB_FILES_SITE.'attachments/',

        ];
    }

    public static function files() {
        return [
            '@download' => PES_RUNNING_ON_PRODUCTION_HOST ? self::WEB_FILES_SITE.'download' : self::WEB_FILES_SITE.'download',
            '@commonimages' => PES_RUNNING_ON_PRODUCTION_HOST ? self::WEB_FILES_COMMON.'images' : self::WEB_FILES_COMMON.'images',
            '@commonmovies' => PES_RUNNING_ON_PRODUCTION_HOST ? self::WEB_FILES_COMMON.'movies' : self::WEB_FILES_COMMON.'movies',
            '@siteimages' => PES_RUNNING_ON_PRODUCTION_HOST ? self::WEB_FILES_SITE.'images' : self::WEB_FILES_SITE.'images',
            '@sitemovies' => PES_RUNNING_ON_PRODUCTION_HOST ? self::WEB_FILES_SITE.'movies' : self::WEB_FILES_SITE.'movies',
            '@siteupload' => PES_RUNNING_ON_PRODUCTION_HOST ? self::WEB_FILES_SITE.'upload' : self::WEB_FILES_SITE.'upload',

            '@presenter' => PES_RUNNING_ON_PRODUCTION_HOST ? self::WEB_FILES_SITE."presenter" : self::WEB_FILES_SITE."presenter",

        ];
    }

}
