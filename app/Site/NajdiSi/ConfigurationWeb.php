<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Site\NajdiSi;

use Application\WebAppFactory;

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
            'app.logs.type' => FileLogger::APPEND_TO_LOG,
            #
            #################################

            #################################
            # Konfigurace session loggeru
            #
            WebAppFactory::SESSION_NAME_SERVICE => 'www_na_session',
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
            'logs.directory' => 'Logs/Components',
            'logs.render' => 'Render.log',
            'logs.type' => FileLogger::REWRITE_LOG,
            'templates' => [
                
                //gdpr    
                'gdpr' => self::WEB_TEMPLATES_COMMON.'layout/gdpr/gdpr.php',

                // common layout templates
                'flash' => self::WEB_TEMPLATES_COMMON.'layout/info/flashMessages.php',
                'login' => self::WEB_TEMPLATES_COMMON.'layout/status/login.php',
                'logout' => self::WEB_TEMPLATES_COMMON.'layout/status/logout.php',
                'editoraction' => self::WEB_TEMPLATES_COMMON.'layout/status/editorAction.php',
                'representativeaction' => self::WEB_TEMPLATES_COMMON.'layout/status/representationAction.php',
                'statusboard' => self::WEB_TEMPLATES_COMMON.'layout/info/statusBoard.php',

                // site layout templates
                'register' => self::WEB_TEMPLATES_SITE.'layout/status/register-with-exhibitor-representative.php',

                // data templates - common
                'list' => self::WEB_TEMPLATES_COMMON.'data/list.php',
                'list2column' => self::WEB_TEMPLATES_COMMON.'data/list2column.php',
                'fields' => self::WEB_TEMPLATES_COMMON.'data/fields.php',
                'formWithFields' => self::WEB_TEMPLATES_COMMON.'data/formWithFields.php',
                'formWithFields2column' => self::WEB_TEMPLATES_COMMON.'data/formWithFields2column.php',
                'formEnctypeMultipartWithFields' => self::WEB_TEMPLATES_COMMON.'data/formEnctypeMultipartWithFields.php',
                'items' => self::WEB_TEMPLATES_COMMON.'data/items.php',
                'multiEditable' => self::WEB_TEMPLATES_COMMON.'data/multiEditable.php',
                'multi' => self::WEB_TEMPLATES_COMMON.'data/multi.php',                
                'checkbox' => self::WEB_TEMPLATES_COMMON.'data/checkbox.php',
                'checked' => self::WEB_TEMPLATES_COMMON.'data/checked.php',

                // data templates - components
                'company' => self::WEB_TEMPLATES_SITE.'events/data/company/company.php',     
                'companyEditable' => self::WEB_TEMPLATES_SITE.'events/data/company/company-editable.php',

                'companyContact' => self::WEB_TEMPLATES_SITE.'events/data/company-contact/contact.php',
                'companyContactEditable' => self::WEB_TEMPLATES_SITE.'events/data/company-contact/contact-editable.php',

                'companyAddress' => self::WEB_TEMPLATES_SITE.'events/data/company-address/address.php',
                'companyAddressEditable' => self::WEB_TEMPLATES_SITE.'events/data/company-address/address-editable.php',  

                'companyInfo' => self::WEB_TEMPLATES_SITE.'events/data/company-info/info.php',
                'companyInfoEditable' => self::WEB_TEMPLATES_SITE.'events/data/company-info/info-editable.php',  
    //            'companyInfoEditable' => self::WEB_TEMPLATES_SITE.'events/data/company-info/info-editable-image.php',  

                'job' => self::WEB_TEMPLATES_SITE.'events/data/job/job.php', 
                'jobEditable' => self::WEB_TEMPLATES_SITE.'events/data/job/job-editable.php', 
                
                'network' => self::WEB_TEMPLATES_SITE.'events/data/network/network.php',
                'networkEditable' => self::WEB_TEMPLATES_SITE.'events/data/network/network-editable.php',
                
                'jobTag' => self::WEB_TEMPLATES_SITE.'events/data/job-tag/tag.php',
                'jobTagEditable' => self::WEB_TEMPLATES_SITE.'events/data/job-tag/tag-editable.php',

                'jobToTag' => self::WEB_TEMPLATES_SITE.'events/data/job-to-tag/job-to-tag.php',
                'jobToTagEditable' => self::WEB_TEMPLATES_SITE.'events/data/job-to-tag/job-to-tag-editable.php',

                'document' => self::WEB_TEMPLATES_SITE.'events/data/document/document.php',
                'documentEditable' => self::WEB_TEMPLATES_SITE.'events/data/document/document-editable.php',  

                'visitorProfile' => self::WEB_TEMPLATES_SITE.'events/data/visitor-profile/visitor-profile.php',  
                'visitorProfileEditable' => self::WEB_TEMPLATES_SITE.'events/data/visitor-profile/visitor-profile-editable.php',

                ////
                'representativeCompanyAddress' => self::WEB_TEMPLATES_SITE.'events/representative-company-address.php',
                
            ],
        ];
    }
    /**
     * Konfigurace - parametry common templates
     * @return array
     */
    public static function commonTemplates() {
        return [
            'templates' => self::WEB_TEMPLATES_COMMON,
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
//                    'de' => 'de',
//                    'en' => 'en_US'
                ],

            // title
            'title' => "Veletrh práce a vzdělávání",

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
            
            //  název proměnné v šabloně => [routa => název služby v konteneru (obvykle název třídy komponentu)]
            'contextServiceMap' => [
//                    'info' => ["web/v1/service/info"=>InfoBoardComponent::class],
                ],
            //  název proměnné v šabloně => název služby v kontejneru (obvykle název menu komponentu jako string)
            'contextLayoutMap' => [
                    'menuSvisle' => 'menuVertical',
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
                    'menuEventsAdmin' => ['service'=>'menuEventsAdmin', 'targetContext'=>'content'],
                    'menuEventsRepresentative' => ['service'=>'menuEventsRepresentative', 'targetContext'=>'content'],
                    'menuEventsVisitor' => ['service'=>'menuEventsVisitor', 'targetContext'=>'content'],
                ],
            'contextMenuEditableMap' => [
                    'bloky' => ['service'=>'menuBlocks', 'targetId'=>'menutarget_content'],
                    'kos' => ['service'=>'menuTrash', 'targetId'=>'menutarget_content'],
                ],
            'contextBlocksMap' => [
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
                
                menuSupervisor:
            - nástroj jak vůbec umět přidávat menu - jinak asi nutno přidat do build
            - právo display jen supervisor
            - rootName - menu_supervisor - nutno přidat do menu root položku root -> ?? přidat do menu_supervisor položku static pro změny menu_root
            - 
                
                
                
                    'menuSupervisor' => [
                        'rootName' => 'menu_vertical',
                        'itemtype' => ItemTypeEnum::MULTILEVEL,
                        'levelRenderer' => 'menuVertical.levelRenderer',
                        'levelRendererEditable' => 'menuVertical.levelRenderer.editable',
                        ],
                    'menuEventsAdmin' => [
                        'rootName' => 'menu_vertical',
                        'itemtype' => ItemTypeEnum::MULTILEVEL,
                        'levelRenderer' => 'menuVertical.levelRenderer',
                        'levelRendererEditable' => 'menuVertical.levelRenderer.editable',
                        ],
                    'menuEventsRepresentative' => [
                        'rootName' => 'menu_vertical',
                        'itemtype' => ItemTypeEnum::ONELEVEL,
                        'levelRenderer' => 'menuVertical.levelRenderer',
                        'levelRendererEditable' => 'menuVertical.levelRenderer.editable',
                        ],
                    'menuEventsVisitor' => [
                        'rootName' => 'menu_vertical',
                        'itemtype' => ItemTypeEnum::ONELEVEL,
                        'levelRenderer' => 'menuVertical.levelRenderer',
                        'levelRendererEditable' => 'menuVertical.levelRenderer.editable',
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
                        'levelRendererEditable' => 'menuVertical.levelRenderer.editable',  // pro editable mode menuVertical
                        ],
                    'menuTrash' => [
                        'rootName' => 'trash',
                        'itemtype' => ItemTypeEnum::TRASH,
                        'levelRenderer' => 'menuTrash.levelRenderer',
                        'levelRendererEditable' => 'menuVertical.levelRenderer.editable',  // pro editable mode menuVertical
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
            // volba sady parametrů z Mail\ParamsTemplates
//            'mail.paramsname' => 'grafiaInterni', 
            'mail.paramsname' => 'najdisi', 
//            'mail.paramsname' => 'najdisiWebSMTP',
//            'mail.paramsname' => 'itGrafiaGmail', 
            'mail.attachments' => PES_RUNNING_ON_PRODUCTION_HOST ? self::WEB_FILES_SITE.'attachments/' : self::WEB_FILES_SITE.'attachments/',

        ];
    }

    public static function files() {
        return [
            '@download' => PES_RUNNING_ON_PRODUCTION_HOST ? self::WEB_FILES_SITE.'download/' : self::WEB_FILES_SITE.'download/',
            '@commonimages' => PES_RUNNING_ON_PRODUCTION_HOST ? self::WEB_FILES_COMMON.'images/' : self::WEB_FILES_COMMON.'images/',
            '@commonmovies' => PES_RUNNING_ON_PRODUCTION_HOST ? self::WEB_FILES_COMMON.'movies/' : self::WEB_FILES_COMMON.'movies/',
            '@siteimages' => PES_RUNNING_ON_PRODUCTION_HOST ? self::WEB_FILES_SITE.'images/' : self::WEB_FILES_SITE.'images/',
            '@sitemovies' => PES_RUNNING_ON_PRODUCTION_HOST ? self::WEB_FILES_SITE.'movies/' : self::WEB_FILES_SITE.'movies/',
            '@siteupload' => PES_RUNNING_ON_PRODUCTION_HOST ? self::WEB_FILES_SITE.'upload/' : self::WEB_FILES_SITE.'upload/',

            '@presenter' => PES_RUNNING_ON_PRODUCTION_HOST ? self::WEB_FILES_SITE."presenter/" : self::WEB_FILES_SITE."presenter/",

        ];
    }

}
